<?php

namespace Webkul\Admin\Http\Controllers\Sales;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\DB;
use Webkul\Admin\DataGrids\OrderDataGrid;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Sales\Repositories\OrderRepository;
use \Webkul\Sales\Repositories\OrderCommentRepository;

class OrderController extends Controller
{
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
     * @param  \Webkul\Sales\Repositories\OrderCommentRepository  $orderCommentRepository
     * @return void
     */
    public function __construct(
        protected OrderRepository $orderRepository,
        protected OrderCommentRepository $orderCommentRepository
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
            return app(OrderDataGrid::class)->toJson();
        }

        return view($this->_config['view']);
    }

    /**
     * Show the view for the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function view($id)
    {
        $order = $this->orderRepository->findOrFail($id);

        \Hws\FieldService\Helpers\BranchScopeHelper::authorizeBranch($order->branch_id);

        return view($this->_config['view'], compact('order'));
    }

    /**
     * Cancel action for the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function cancel($id)
    {
        $order = $this->orderRepository->findOrFail($id);
        \Hws\FieldService\Helpers\BranchScopeHelper::authorizeBranch($order->branch_id);

        $result = $this->orderRepository->cancel($id);

        if ($result) {
            session()->flash('success', trans('admin::app.response.cancel-success', ['name' => 'Order']));
        } else {
            session()->flash('error', trans('admin::app.response.cancel-error', ['name' => 'Order']));
        }

        return redirect()->back();
    }

    /**
     * Add comment to the order
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function comment($id)
    {
        $order = $this->orderRepository->findOrFail($id);
        \Hws\FieldService\Helpers\BranchScopeHelper::authorizeBranch($order->branch_id);

        Event::dispatch('sales.order.comment.create.before');

        $comment = $this->orderCommentRepository->create(array_merge(request()->all(), [
            'order_id'          => $id,
            'customer_notified' => request()->has('customer_notified'),
        ]));

        Event::dispatch('sales.order.comment.create.after', $comment);

        session()->flash('success', trans('admin::app.sales.orders.comment-added-success'));

        return redirect()->back();
    }

    /**
     * Update Quality Check (QC) status for an order item
     */
    public function updateItemQc($id)
    {
        $order = $this->orderRepository->findOrFail($id);
        \Hws\FieldService\Helpers\BranchScopeHelper::authorizeBranch($order->branch_id);

        $data = request()->validate([
            'item_id'      => 'required|exists:order_items,id',
            'qc_status'    => 'required|in:passed,failed,pending',
            'qc_serial_no' => 'nullable|string|max:191',
            'qc_notes'     => 'nullable|string|max:500',
        ]);

        $item = \Webkul\Sales\Models\OrderItem::where('order_id', $order->id)->where('id', $data['item_id'])->firstOrFail();

        $item->update([
            'qc_status'     => $data['qc_status'],
            'qc_serial_no'  => $data['qc_serial_no'] ?? $item->qc_serial_no,
            'qc_notes'      => $data['qc_notes'] ?? $item->qc_notes,
            'qc_checked_by' => auth()->guard('admin')->id() ?? 1,
            'qc_checked_at' => now(),
        ]);

        // Check overall QC status for the order
        $allItems = \Webkul\Sales\Models\OrderItem::where('order_id', $order->id)->whereNull('parent_id')->get();
        $allPassed = $allItems->every(fn($i) => $i->qc_status === 'passed');
        $anyPassed = $allItems->contains(fn($i) => $i->qc_status === 'passed');
        $hasFailed = $allItems->contains(fn($i) => $i->qc_status === 'failed');

        $overallStatus = 'pending';
        if ($allPassed) {
            $overallStatus = 'passed';
        } elseif ($anyPassed) {
            $overallStatus = 'partially_passed';
        } elseif ($hasFailed) {
            $overallStatus = 'failed';
        }

        $order->update(['qc_status' => $overallStatus]);

        session()->flash('success', "Item QC status updated to " . strtoupper($data['qc_status']) . " successfully.");

        return redirect()->back();
    }

    /**
     * Assign Account Manager to the order
     */
    public function assignAccountManager($id)
    {
        $order = $this->orderRepository->findOrFail($id);
        \Hws\FieldService\Helpers\BranchScopeHelper::authorizeBranch($order->branch_id);

        $accountManagerId = request()->input('account_manager_id') ?: null;

        $order->update([
            'account_manager_id' => $accountManagerId,
        ]);

        $managerName = $accountManagerId ? DB::table('admins')->where('id', $accountManagerId)->value('name') : 'Unassigned';

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Account manager set to {$managerName}.",
                'manager_name' => $managerName,
            ]);
        }

        session()->flash('success', "Account manager updated successfully.");

        return redirect()->back();
    }
}