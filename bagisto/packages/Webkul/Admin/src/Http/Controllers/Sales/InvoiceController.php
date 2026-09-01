<?php

namespace Webkul\Admin\Http\Controllers\Sales;

use Illuminate\Http\Request;
use Webkul\Admin\DataGrids\InvoicesTransactionsDatagrid;
use Webkul\Admin\DataGrids\OrderInvoicesDataGrid;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Admin\Traits\Mails;
use Webkul\Core\Traits\PDFHandler;
use Webkul\Sales\Repositories\InvoiceRepository;
use Webkul\Sales\Repositories\OrderRepository;

class InvoiceController extends Controller
{
    use Mails, PDFHandler;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $_config;

    /**
     * Create a new controller instance.
     *
     * @param  \Webkul\Sales\Repositories\OrderRepository  $orderRepository
     * @param  \Webkul\Sales\Repositories\InvoiceRepository  $invoiceRepository
     * @return void
     */
    public function __construct(
        protected OrderRepository $orderRepository,
        protected InvoiceRepository $invoiceRepository
    )
    {
        $this->_config = request('_config');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        if (request()->ajax()) {
            return app(OrderInvoicesDataGrid::class)->toJson();
        }

        return view($this->_config['view']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function invoiceTransactions($id)
    {
        return app(InvoicesTransactionsDatagrid::class)->toJson();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  int  $orderId
     * @return \Illuminate\View\View
     */
    public function create($orderId)
    {
        $order = $this->orderRepository->findOrFail($orderId);

        if ($order->payment->method === 'paypal_standard') {
            abort(404);
        }

        return view($this->_config['view'], compact('order'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  int  $orderId
     * @return \Illuminate\Http\Response
     */
    public function store($orderId)
    {
        $order = $this->orderRepository->findOrFail($orderId);

        if (! $order->canInvoice()) {
            session()->flash('error', trans('admin::app.sales.invoices.creation-error'));

            return redirect()->back();
        }

        $this->validate(request(), [
            'invoice.items.*' => 'required|numeric|min:0',
            'is_gst_invoice'  => 'nullable|boolean',
            'billing_company_name' => 'nullable|required_if:is_gst_invoice,1|string|max:255',
            'gstin'           => ['nullable', 'required_if:is_gst_invoice,1', 'regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z][A-Z0-9]Z[A-Z0-9]$/'],
            'gst_place_of_supply' => 'nullable|required_if:is_gst_invoice,1|string|max:100',
            'gst_tax_type'    => 'nullable|required_if:is_gst_invoice,1|in:intra_state,inter_state',
        ]);

        if (! $this->invoiceRepository->haveProductToInvoice(request()->all())) {
            session()->flash('error', trans('admin::app.sales.invoices.product-error'));

            return redirect()->back();
        }

        if (! $this->invoiceRepository->isValidQuantity(request()->all())) {
            session()->flash('error', trans('admin::app.sales.invoices.invalid-qty'));

            return redirect()->back();
        }

        $order->is_gst_invoice = request()->boolean('is_gst_invoice');
        $order->billing_company_name = $order->is_gst_invoice ? request('billing_company_name') : null;
        $order->gstin = $order->is_gst_invoice ? strtoupper(request('gstin')) : null;
        $order->gst_place_of_supply = $order->is_gst_invoice ? request('gst_place_of_supply') : null;
        $order->gst_tax_type = $order->is_gst_invoice ? request('gst_tax_type') : null;
        $order->save();

        $this->invoiceRepository->create(array_merge(request()->all(), [
            'order_id' => $orderId,
        ]));

        session()->flash('success', trans('admin::app.response.create-success', ['name' => 'Invoice']));

        return redirect()->route($this->_config['redirect'], $orderId);
    }

    /**
     * Show the view for the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function view($id)
    {
        $invoice = $this->invoiceRepository->findOrFail($id);

        return view($this->_config['view'], compact('invoice'));
    }

    /**
     * Send duplicate invoice.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function sendDuplicateInvoice(Request $request, $id)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $invoice = $this->invoiceRepository->findOrFail($id);

        $this->sendDuplicateInvoiceMail($invoice, $request->email);

        session()->flash('success', trans('admin::app.sales.invoices.invoice-sent'));

        return redirect()->back();
    }

    /**
     * Print and download the for the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function printInvoice($id)
    {
        $invoice = $this->invoiceRepository->findOrFail($id);

        return $this->downloadPDF(
            view('admin::sales.invoices.pdf', compact('invoice'))->render(),
            'invoice-' . $invoice->created_at->format('d-m-Y')
        );
    }
}
