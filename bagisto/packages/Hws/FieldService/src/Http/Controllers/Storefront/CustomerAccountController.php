<?php

namespace Hws\FieldService\Http\Controllers\Storefront;

use Hws\FieldService\Models\Quotation;
use Hws\FieldService\Models\SiteSurvey;
use Hws\FieldService\Models\Task;
use Illuminate\Routing\Controller;
use Webkul\Sales\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;

class CustomerAccountController extends Controller
{
    public function index()
    {
        $customer = auth('customer')->user();
        $email = $customer->email;

        $orders = Order::query()->where(fn ($query) => $query
            ->where('customer_id', $customer->id)
            ->orWhere('customer_email', $email))
            ->latest()->get();

        $leads = SiteSurvey::query()->where(fn ($query) => $query
            ->where('customer_id', $customer->id)
            ->orWhere('customer_email', $email))
            ->latest()->get();

        $tasks = Task::query()->where(fn ($query) => $query
            ->where('customer_id', $customer->id)
            ->orWhere('customer_email', $email))
            ->latest()->get();

        $quotations = Quotation::query()->whereIn('lead_id', $leads->pluck('id'))->latest()->get();

        return view('hws::shop.account.tracking', compact('customer', 'orders', 'leads', 'tasks', 'quotations'));
    }

    public function quotationPdf($id)
    {
        $customer = auth('customer')->user();
        $leadIds = SiteSurvey::query()->where(fn ($query) => $query
            ->where('customer_id', $customer->id)
            ->orWhere('customer_email', $customer->email))
            ->pluck('id');

        $quotation = Quotation::with('items')->whereIn('lead_id', $leadIds)->findOrFail($id);

        return Pdf::loadView('hws::admin.quotations.pdf', compact('quotation'))
            ->download("Quotation-{$quotation->quote_no}.pdf");
    }
}
