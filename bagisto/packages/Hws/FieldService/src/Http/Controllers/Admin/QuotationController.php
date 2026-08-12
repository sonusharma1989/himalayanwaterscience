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

class QuotationController extends Controller
{
    /**
     * Show the form to construct a quotation from a Lead.
     */
    public function create($leadId)
    {
        $lead = SiteSurvey::findOrFail($leadId);

        return view('hws::admin.quotations.create', compact('lead'));
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
}
