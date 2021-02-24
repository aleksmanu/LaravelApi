<?php
namespace App\Modules\Workorder\Http\Controllers;

use App\Modules\Workorder\Models\WorkOrder;
use App\Modules\Workorder\Repositories\Interfaces\IWorkOrderRepository;

use App\Modules\Workorder\Http\Requests\WorkOrders\WorkOrderStoreRequest;
use App\Modules\Workorder\Http\Requests\WorkOrders\WorkOrderUpdateRequest;
use App\Modules\Workorder\Http\Requests\WorkOrders\WorkOrderDeleteRequest;
use App\Modules\Workorder\Http\Requests\WorkOrders\WorkOrderDataTableRequest;

use App\Http\Controllers\Controller;
use App\Modules\Workorder\Http\Requests\WorkOrders\WorkOrderCompleteRequest;
use App\Modules\Workorder\Http\Requests\WorkOrders\WorkOrderPayRequest;

use Carbon\Carbon;

class WorkOrderController extends Controller
{
    protected $work_order_repository;

    public function __construct(IWorkOrderRepository $workOrderRepository)
    {
        $this->work_order_repository = $workOrderRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response($this->work_order_repository->list());
    }

    /**
     * Display a listing of the resource.
     *
     * @param WorkOrderDataTableRequest $request
     * @return \Illuminate\Http\Response
     */
    public function datatable(WorkOrderDataTableRequest $request)
    {
        $validated = $request->validated();
        $limit = $request->limit ?? config('misc.dataTable.defaultPerPage');
        $sort_column = WorkOrder::getTableName() . '.' . (
            $request->sort_column ??  config('misc.dataTable.defaultSortColumn')
        );
        $sort_order = $request->sort_order ?? config('misc.dataTable.defaultSortOrder');
        $offset = $request->offset ?? 0;
        return response(
            $this->work_order_repository->list(
                $validated,
                $sort_column,
                $sort_order,
                $limit,
                $offset
            )
        );
    }


    /**
     * @param WorkOrderStoreRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function store(WorkOrderStoreRequest $request)
    {
        return response(
            $this->work_order_repository->store(
                $request->validated()
            )
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        return response($this->work_order_repository->get($id));
    }

    /**
     * @param WorkOrderUpdateRequest $request
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function update(WorkOrderUpdateRequest $request, int $id)
    {
        if ($this->work_order_repository->get($id)->paid_at) {
            return response('Invoice has been paid. No updates may be performed on this item', 403);
        }

        return response(
            $this->work_order_repository->update(
                $id,
                $request->validated()
            )
        );
    }

    /**
     * @param int $id
     * @param WorkOrderDeleteRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function destroy(int $id, WorkOrderDeleteRequest $request)
    {
        $valid = $request->validated();
        $work_order = $this->work_order_repository->get($id);
        $work_order->locked_by_id = request()->user()->id;
        $work_order->locked_note = $valid['locked_note'];
        $work_order->save();
        $work_order = $work_order->fresh();

        return response()->json(true);
    }

    /**
     * @param int $id
     * @param WorkOrderCompleteRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function complete(int $id, WorkOrderCompleteRequest $request)
    {
        $work_order = $this->work_order_repository->get($id);
        $work_order->completed_by_id = request()->user()->id;
        $work_order->completed_at = Carbon::now();
        $work_order->save();
        $work_order = $work_order->fresh();

        return response()->json($work_order);
    }

    /**
     * @param int $id
     * @param WorkOrderPayRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function pay(int $id, WorkOrderPayRequest $request)
    {
        $work_order = $this->work_order_repository->get($id);
        $work_order->paid_by_id = request()->user()->id;
        $work_order->paid_at = Carbon::now();
        $work_order->save();
        $work_order = $work_order->fresh();

        return response()->json($work_order);
    }

    public function toHotPDF(int $id)
    {
        $workOrder = $this->work_order_repository->get($id);
        $quote = $workOrder->quote;

        if ($quote->unit) {
            $quote->load(['unit', 'unit.propertyManager', 'unit.propertyManager.user']);
        }
        $quote->load(['property', 'property.propertyManager', 'property.propertyManager.user']);


        $pdf = \PDF::loadView('pdf.base', [
            'quote' => $quote->toArray(),
            'work_order' => $workOrder->toArray()
        ]);
        return $pdf->download('invoice.pdf');
    }
}
