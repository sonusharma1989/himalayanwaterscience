<?php

namespace Hws\FieldService\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Webkul\Sales\Models\Order;

class OrderEditController extends Controller
{
    public function edit($id)
    {
        $order = Order::with(['items', 'billing_address', 'shipping_address', 'invoices', 'shipments'])->findOrFail($id);
        $products = Schema::hasTable('product_flat')
            ? DB::table('product_flat')->select('product_id', 'name', 'sku', 'price')->where('locale', app()->getLocale())->get()
            : collect();

        return view('hws::admin.orders.edit', compact('order', 'products'));
    }

    public function update(Request $request, $id)
    {
        $order = Order::with(['items', 'billing_address', 'shipping_address'])->findOrFail($id);

        $data = $request->validate([
            'customer_first_name' => 'required|string|max:255',
            'customer_last_name'  => 'nullable|string|max:255',
            'customer_email'      => 'required|email|max:255',
            'billing.address1'    => 'required|string|max:500',
            'billing.city'        => 'required|string|max:100',
            'billing.state'       => 'required|string|max:100',
            'billing.postcode'    => 'required|string|max:20',
            'billing.phone'       => 'required|string|max:30',
            'shipping.address1'   => 'required|string|max:500',
            'shipping.city'       => 'required|string|max:100',
            'shipping.state'      => 'required|string|max:100',
            'shipping.postcode'   => 'required|string|max:20',
            'shipping.phone'      => 'required|string|max:30',
            'is_gst_invoice'      => 'required|boolean',
            'billing_company_name'=> 'required_if:is_gst_invoice,1|nullable|string|max:255',
            'gstin'               => ['required_if:is_gst_invoice,1', 'nullable', 'string', 'size:15', 'regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z][1-9A-Z]Z[0-9A-Z]$/'],
            'items'               => 'required|array|min:1',
            'items.*.name'        => 'required|string|max:255',
            'items.*.sku'         => 'required|string|max:255',
            'items.*.qty'         => 'required|numeric|min:1',
            'items.*.price'       => 'required|numeric|min:0',
            'items.*.product_id'  => 'nullable|integer|exists:products,id',
            'discount_amount'     => 'nullable|numeric|min:0',
            'tax_percent'         => 'nullable|numeric|min:0|max:100',
            'shipping_amount'     => 'nullable|numeric|min:0',
        ]);

        foreach ($data['items'] as $itemId => $itemData) {
            $item = $order->items->firstWhere('id', (int) $itemId);
            if (! $item && str_starts_with((string) $itemId, 'new_')) continue;
            abort_unless($item, 422);
            $isProcessed = $item->qty_shipped > 0 || $item->qty_invoiced > 0 || $item->qty_refunded > 0 || $item->qty_canceled > 0;

            if ($isProcessed && (
                $itemData['name'] !== $item->name
                || $itemData['sku'] !== $item->sku
                || (float) $itemData['qty'] !== (float) $item->qty_ordered
                || (float) $itemData['price'] !== (float) $item->price
            )) {
                throw ValidationException::withMessages([
                    "items.{$itemId}.name" => "{$item->name} is already processed and cannot be edited.",
                ]);
            }

            $minimumQty = max(1, (float) $item->qty_shipped, (float) $item->qty_invoiced, (float) $item->qty_refunded);

            if ((float) $itemData['qty'] < $minimumQty) {
                throw ValidationException::withMessages([
                    "items.{$itemId}.qty" => "Quantity for {$item->name} cannot be below fulfilled quantity {$minimumQty}.",
                ]);
            }
        }

        DB::transaction(function () use ($order, $data) {
            $subTotal = collect($data['items'])->sum(fn ($item) => (float) $item['qty'] * (float) $item['price']);
            $discount = min((float) ($data['discount_amount'] ?? 0), $subTotal);
            $taxable = max(0, $subTotal - $discount);
            $tax = $taxable * ((float) ($data['tax_percent'] ?? 0) / 100);
            $shipping = (float) ($data['shipping_amount'] ?? 0);

            foreach ($data['items'] as $itemId => $itemData) {
                $item = $order->items->firstWhere('id', (int) $itemId);
                $total = (float) $itemData['qty'] * (float) $itemData['price'];
                $ratio = $subTotal > 0 ? $total / $subTotal : 0;
                $values = [
                    'name' => $itemData['name'], 'sku' => $itemData['sku'],
                    'qty_ordered' => $itemData['qty'],
                    'price' => $itemData['price'], 'base_price' => $itemData['price'],
                    'total' => $total, 'base_total' => $total,
                    'discount_amount' => $discount * $ratio, 'base_discount_amount' => $discount * $ratio,
                    'tax_amount' => $tax * $ratio, 'base_tax_amount' => $tax * $ratio,
                    'product_id' => $itemData['product_id'] ?? null,
                    'product_type' => ! empty($itemData['product_id']) ? \Webkul\Product\Models\Product::class : null,
                    'type' => 'simple',
                ];

                if ($item) {
                    $item->update($values);
                } else {
                    $order->all_items()->create($values + ['additional' => ['added_from_order_editor' => true]]);
                }
            }

            $order->update([
                'customer_first_name' => $data['customer_first_name'],
                'customer_last_name' => $data['customer_last_name'] ?: '',
                'customer_email' => $data['customer_email'],
                'is_gst_invoice' => (bool) $data['is_gst_invoice'],
                'billing_company_name' => $data['is_gst_invoice'] ? $data['billing_company_name'] : null,
                'gstin' => $data['is_gst_invoice'] ? strtoupper($data['gstin']) : null,
                'total_item_count' => count($data['items']),
                'total_qty_ordered' => collect($data['items'])->sum(fn ($item) => (float) $item['qty']),
                'sub_total' => $subTotal, 'base_sub_total' => $subTotal,
                'discount_amount' => $discount, 'base_discount_amount' => $discount,
                'tax_amount' => $tax, 'base_tax_amount' => $tax,
                'shipping_amount' => $shipping, 'base_shipping_amount' => $shipping,
                'grand_total' => $subTotal - $discount + $tax + $shipping,
                'base_grand_total' => $subTotal - $discount + $tax + $shipping,
            ]);

            foreach (['billing', 'shipping'] as $addressType) {
                $address = $addressType === 'billing' ? $order->billing_address : $order->shipping_address;
                $address?->update($data[$addressType] + [
                    'first_name' => $data['customer_first_name'],
                    'last_name' => $data['customer_last_name'] ?: '',
                    'email' => $data['customer_email'],
                    'company_name' => $addressType === 'billing' && $data['is_gst_invoice'] ? $data['billing_company_name'] : $address?->company_name,
                ]);
            }
        });

        return redirect()->route('admin.sales.orders.view', $order->id)->with('success', 'Order updated successfully.');
    }
}
