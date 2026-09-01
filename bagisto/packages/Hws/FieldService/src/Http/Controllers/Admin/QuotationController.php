<?php

namespace Hws\FieldService\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Hws\FieldService\Models\SiteSurvey;
use Hws\FieldService\Models\Quotation;
use Hws\FieldService\Models\QuotationItem;
use Hws\FieldService\Models\LeadActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;
use Webkul\Customer\Models\Customer;
use Webkul\Sales\Models\Order;
use Webkul\Sales\Models\OrderTransaction;
use Webkul\Sales\Repositories\OrderRepository;

use Illuminate\Support\Facades\Schema;

class QuotationController extends Controller
{
    public function __construct(protected OrderRepository $orderRepository)
    {
    }
    /**
     * Show the form to construct a quotation from a Lead.
     */
    public function create($leadId)
    {
        $lead = SiteSurvey::findOrFail($leadId);

        $products = [];
        if (Schema::hasTable('product_flat')) {
            $products = DB::table('product_flat')
                ->select('product_id', 'name', 'price')
                ->where('locale', app()->getLocale())
                ->get()
                ->toArray();
        }

        return view('hws::admin.quotations.create', compact('lead', 'products'));
    }



    /**
     * Store the quotation.
     */
    public function store(Request $request)
    {
        $request->validate([
            'lead_id'          => 'required|exists:hws_site_surveys,id',
            'customer_name'    => 'required|string',
            'customer_email'   => 'nullable|email',
            'customer_phone'   => 'nullable|string',
            'customer_address' => 'nullable|string',
            'discount'         => 'nullable|numeric|min:0',
            'tax_percent'      => 'nullable|numeric|min:0',
            'items'            => 'required|array|min:1',
            'items.*.name'     => 'required|string',
            'items.*.qty'      => 'required|integer|min:1',
            'items.*.rate'     => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            // Calculate subtotal
            $subtotal = 0.00;
            $itemsData = [];
            foreach ($request->items as $item) {
                $amount = $item['qty'] * $item['rate'];
                $subtotal += $amount;
                $itemsData[] = [
                    'item_name' => $item['name'],
                    'quantity'  => $item['qty'],
                    'rate'      => $item['rate'],
                    'amount'    => $amount,
                ];
            }

            $discount = floatval($request->input('discount', 0));
            $taxPercent = floatval($request->input('tax_percent', 0));
            $taxAmount = (($subtotal - $discount) * $taxPercent) / 100;
            $grandTotal = ($subtotal - $discount) + $taxAmount;

            // Generate Quote Number
            $quoteNo = 'QT-' . date('Y') . '-' . sprintf('%04d', Quotation::count() + 1);

            $quotation = Quotation::create([
                'lead_id'          => $request->lead_id,
                'quote_no'         => $quoteNo,
                'customer_name'    => $request->customer_name,
                'customer_email'   => $request->customer_email,
                'customer_phone'   => $request->customer_phone,
                'customer_address' => $request->customer_address,
                'subtotal'         => $subtotal,
                'discount'         => $discount,
                'tax_amount'       => $taxAmount,
                'grand_total'      => $grandTotal,
                'status'           => 'draft',
            ]);

            // Save Items
            foreach ($itemsData as $item) {
                $item['quotation_id'] = $quotation->id;
                QuotationItem::create($item);
            }

            // Log activity on the Lead
            LeadActivity::create([
                'survey_id'     => $request->lead_id,
                'action_by'     => auth()->guard('admin')->id() ?? 1,
                'activity_type' => 'note',
                'notes'         => "Quotation created: {$quoteNo} for amount ₹" . number_format($grandTotal, 2),
            ]);

            // Update Lead Status to 'proposal_sent'
            $lead = SiteSurvey::findOrFail($request->lead_id);
            $lead->update(['status' => 'proposal_sent']);

            DB::commit();

            session()->flash('success', "Quotation {$quoteNo} generated successfully!");

            return redirect()->route('hws.admin.sales-leads.index');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Failed to save quotation: ' . $e->getMessage()]);
        }
    }

    /**
     * Download the Quotation as PDF.
     */
    public function downloadPdf($id)
    {
        $quotation = Quotation::with('items')->findOrFail($id);
        
        $pdf = Pdf::loadView('hws::admin.quotations.pdf', compact('quotation'));
        
        return $pdf->download("Quotation-{$quotation->quote_no}.pdf");
    }

    /**
     * Send Quotation PDF via Email.
     */
    public function sendEmail($id)
    {
        $quotation = Quotation::with('items')->findOrFail($id);

        if (empty($quotation->customer_email)) {
            return redirect()->back()->withErrors(['error' => 'Customer email is empty.']);
        }

        try {
            $pdf = Pdf::loadView('hws::admin.quotations.pdf', compact('quotation'));
            $pdfData = $pdf->output();
            $quoteNo = $quotation->quote_no;
            $customerName = $quotation->customer_name;
            $customerEmail = $quotation->customer_email;

            Mail::send([], [], function ($message) use ($customerEmail, $customerName, $quoteNo, $pdfData) {
                $message->to($customerEmail)
                    ->subject("Quotation {$quoteNo} - Himalayan Water Science")
                    ->html("<h3>Dear {$customerName},</h3><p>Please find attached the quotation details from Himalayan Water Science.</p><p>Regards,<br>HWS Team</p>")
                    ->attachData($pdfData, "Quotation-{$quoteNo}.pdf", [
                        'mime' => 'application/pdf',
                    ]);
            });

            // Log activity on the Lead
            if ($quotation->lead_id) {
                LeadActivity::create([
                    'survey_id'     => $quotation->lead_id,
                    'action_by'     => auth()->guard('admin')->id() ?? 1,
                    'activity_type' => 'email',
                    'notes'         => "Quotation {$quoteNo} sent via email to {$customerEmail}.",
                ]);
            }

            // Update quotation status
            $quotation->update(['status' => 'sent']);

            session()->flash('success', "Quotation email sent successfully to {$customerEmail}!");

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to send email: ' . $e->getMessage()]);
        }

        return redirect()->back();
    }

    public function convertToOrder($id)
    {
        $quotation = Quotation::with(['items', 'lead'])->findOrFail($id);

        if ($quotation->order_id) {
            return redirect()->route('admin.sales.orders.view', $quotation->order_id);
        }

        DB::beginTransaction();

        try {
            $lead = $quotation->lead;
            $customer = $lead?->customer_id ? Customer::find($lead->customer_id) : Customer::where('email', $quotation->customer_email)->first();
            $channel = core()->getCurrentChannel();
            $currency = core()->getBaseCurrencyCode();
            $nameParts = preg_split('/\s+/', trim($quotation->customer_name), 2);
            $firstName = $nameParts[0] ?: 'Customer';
            $lastName = $nameParts[1] ?? 'Account';

            $order = Order::create([
                'increment_id'               => $this->orderRepository->generateIncrementId(),
                'status'                     => Order::STATUS_PENDING_PAYMENT,
                'sales_type'                 => $lead?->sales_type ?: 'trading',
                'channel_name'               => $channel?->name,
                'channel_id'                 => $channel?->id,
                'channel_type'               => $channel ? get_class($channel) : null,
                'customer_id'                => $customer?->id,
                'customer_type'              => $customer ? Customer::class : null,
                'is_guest'                   => $customer ? 0 : 1,
                'customer_email'             => $quotation->customer_email ?: 'customer@hws.local',
                'customer_first_name'        => $firstName,
                'customer_last_name'         => $lastName,
                'total_item_count'           => $quotation->items->count(),
                'total_qty_ordered'          => $quotation->items->sum('quantity'),
                'base_currency_code'         => $currency,
                'channel_currency_code'      => $currency,
                'order_currency_code'        => $currency,
                'sub_total'                  => $quotation->subtotal,
                'base_sub_total'             => $quotation->subtotal,
                'discount_amount'            => $quotation->discount,
                'base_discount_amount'       => $quotation->discount,
                'tax_amount'                 => $quotation->tax_amount,
                'base_tax_amount'            => $quotation->tax_amount,
                'grand_total'                => $quotation->grand_total,
                'base_grand_total'           => $quotation->grand_total,
                'shipping_amount'            => 0,
                'base_shipping_amount'       => 0,
            ]);

            $order->payment()->create([
                'method'       => 'manual_pending',
                'method_title' => 'Payment Pending',
                'additional'   => ['quotation_no' => $quotation->quote_no],
            ]);

            $address = [
                'first_name'  => $firstName,
                'last_name'   => $lastName,
                'email'       => $quotation->customer_email ?: 'customer@hws.local',
                'address1'    => $quotation->customer_address ?: 'Address not provided',
                'country'     => 'IN',
                'state'       => 'N/A',
                'city'        => 'N/A',
                'postcode'    => 0,
                'phone'       => $quotation->customer_phone ?: 'N/A',
                'customer_id' => $customer?->id,
            ];
            $order->addresses()->create($address + ['address_type' => 'order_billing']);
            $order->addresses()->create($address + ['address_type' => 'order_shipping']);

            foreach ($quotation->items as $item) {
                $taxShare = $quotation->subtotal > 0 ? ($item->amount / $quotation->subtotal) * $quotation->tax_amount : 0;
                $discountShare = $quotation->subtotal > 0 ? ($item->amount / $quotation->subtotal) * $quotation->discount : 0;
                $order->all_items()->create([
                    'sku'                  => 'QUOTE-' . $quotation->id . '-' . $item->id,
                    'type'                 => 'simple',
                    'name'                 => $item->item_name,
                    'qty_ordered'          => $item->quantity,
                    'price'                => $item->rate,
                    'base_price'           => $item->rate,
                    'total'                => $item->amount,
                    'base_total'           => $item->amount,
                    'discount_amount'      => $discountShare,
                    'base_discount_amount' => $discountShare,
                    'tax_amount'           => $taxShare,
                    'base_tax_amount'      => $taxShare,
                    'additional'           => ['quotation_id' => $quotation->id],
                ]);
            }

            $quotation->update(['order_id' => $order->id, 'status' => 'accepted']);
            $lead?->update(['order_id' => $order->id, 'status' => 'won']);

            if ($lead) {
                LeadActivity::create([
                    'survey_id' => $lead->id,
                    'action_by' => auth()->guard('admin')->id() ?? 1,
                    'activity_type' => 'note',
                    'notes' => "Quotation {$quotation->quote_no} converted to Order #{$order->increment_id}.",
                ]);
            }

            DB::commit();

            return redirect()->route('admin.sales.orders.view', $order->id)->with('success', 'Quotation converted to order successfully.');
        } catch (\Throwable $exception) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Unable to convert quotation: ' . $exception->getMessage()]);
        }
    }

    public function recordManualPayment(Request $request, $id)
    {
        $order = Order::with('payment')->findOrFail($id);
        $data = $request->validate([
            'payment_method' => 'required|in:cash,bank_transfer,cheque,upi_manual',
            'amount'         => 'required|numeric|min:0.01',
            'reference'      => 'nullable|string|max:100',
            'notes'          => 'nullable|string|max:500',
        ]);

        $titles = ['cash' => 'Cash', 'bank_transfer' => 'Bank Transfer', 'cheque' => 'Cheque', 'upi_manual' => 'Manual UPI'];
        $transaction = OrderTransaction::create([
            'transaction_id' => 'MAN-' . now()->format('YmdHis') . '-' . $order->id,
            'status'          => 'paid',
            'type'            => 'manual',
            'payment_method'  => $data['payment_method'],
            'data'            => json_encode(['amount' => (float) $data['amount'], 'reference' => $data['reference'] ?? null, 'notes' => $data['notes'] ?? null, 'recorded_by' => auth()->guard('admin')->user()->name ?? 'Admin']),
            'invoice_id'      => 0,
            'order_id'        => $order->id,
        ]);

        $order->payment()->updateOrCreate(['order_id' => $order->id], [
            'method' => $data['payment_method'],
            'method_title' => $titles[$data['payment_method']],
            'additional' => ['last_transaction_id' => $transaction->transaction_id, 'reference' => $data['reference'] ?? null],
        ]);
        $order->update(['status' => Order::STATUS_PROCESSING]);

        return redirect()->back()->with('success', $titles[$data['payment_method']] . ' payment recorded successfully.');
    }

}
