<?php

namespace Hws\FieldService\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Webkul\Inventory\Models\InventorySource;
use Webkul\Sales\Models\Order;
use Webkul\Sales\Models\Shipment;
use Webkul\Sales\Models\ShipmentItem;
use Webkul\Sales\Repositories\OrderRepository;

class ProjectShipmentController extends Controller
{
    public function __construct(protected OrderRepository $orderRepository) {}

    public function create($orderId)
    {
        $order = Order::with(['items', 'channel.inventory_sources', 'shipping_address'])->findOrFail($orderId);
        abort_unless($order->sales_type === 'projects', 404);

        if (! $order->canShip()) {
            return redirect()->back()->with('error', 'This project order has no remaining quantity to ship.');
        }

        return view('admin::sales.shipments.create', compact('order'));
    }

    public function store($orderId)
    {
        $order = Order::with(['items', 'shipping_address'])->findOrFail($orderId);
        abort_unless($order->sales_type === 'projects', 404);

        $data = request()->validate([
            'shipment.source' => 'required|integer',
            'shipment.carrier_title' => 'nullable|string|max:255',
            'shipment.track_number' => 'nullable|string|max:255',
            'shipment.items.*.*' => 'required|numeric|min:0',
        ]);

        $sourceId = (int) $data['shipment']['source'];
        $source = InventorySource::findOrFail($sourceId);
        $selectedItems = [];

        foreach ($data['shipment']['items'] ?? [] as $itemId => $sourceQuantities) {
            $item = $order->items->firstWhere('id', (int) $itemId);
            $qty = (float) ($sourceQuantities[$sourceId] ?? 0);

            if (! $item || $qty <= 0) continue;

            if ($qty > $item->qty_to_ship) {
                return redirect()->back()->withInput()->with('error', "Quantity for {$item->name} exceeds remaining quantity.");
            }

            $selectedItems[] = [$item, $qty];
        }

        if (! count($selectedItems)) {
            return redirect()->back()->withInput()->with('error', 'Enter quantity for at least one project item.');
        }

        DB::transaction(function () use ($order, $data, $source, $sourceId, $selectedItems) {
            $shipment = Shipment::create([
                'order_id' => $order->id,
                'total_qty' => collect($selectedItems)->sum(fn ($row) => $row[1]),
                'total_weight' => 0,
                'carrier_title' => $data['shipment']['carrier_title'] ?: 'Project Delivery',
                'track_number' => $data['shipment']['track_number'] ?: null,
                'customer_id' => $order->customer_id,
                'customer_type' => $order->customer_type,
                'order_address_id' => $order->shipping_address->id,
                'inventory_source_id' => $sourceId,
                'inventory_source_name' => $source->name,
            ]);

            foreach ($selectedItems as [$item, $qty]) {
                ShipmentItem::create([
                    'shipment_id' => $shipment->id, 'order_item_id' => $item->id,
                    'name' => $item->name, 'sku' => $item->sku, 'qty' => $qty, 'weight' => 0,
                    'price' => $item->price, 'base_price' => $item->base_price,
                    'total' => $item->price * $qty, 'base_total' => $item->base_price * $qty,
                    'product_id' => $item->product_id, 'product_type' => $item->product_type,
                    'additional' => $item->additional,
                ]);

                $item->update(['qty_shipped' => $item->qty_shipped + $qty]);
            }

            $this->orderRepository->updateOrderStatus($order->fresh());
        });

        return redirect()->route('admin.sales.orders.view', $order->id)->with('success', 'Project shipment created successfully.');
    }
}
