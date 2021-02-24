<?php
namespace App\Modules\Workorder\Http\Controllers;

use App\Modules\Workorder\Http\Requests\Quotes\QuoteAcceptRequest;
use App\Modules\Workorder\Http\Requests\Quotes\QuoteDeleteRequest;
use App\Modules\Workorder\Models\Quote;
use App\Modules\Workorder\Repositories\Interfaces\IQuoteRepository;

use App\Modules\Workorder\Http\Requests\Quotes\QuoteStoreRequest;
use App\Modules\Workorder\Http\Requests\Quotes\QuoteUpdateRequest;
use App\Modules\Workorder\Http\Requests\Quotes\QuoteDataTableRequest;

use App\Modules\Workorder\Repositories\Interfaces\IWorkOrderRepository;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Modules\Workorder\Models\WorkOrder;

class QuoteController extends Controller
{
    protected $quote_repository;
    protected $work_order_repository;

    const AUTO_NOTE = 'AUTOMATIC - TRANSITIONED TO WORKORDER';

    public function __construct(IQuoteRepository $quoteRepository, IWorkOrderRepository $workOrderRepository)
    {
        $this->quote_repository = $quoteRepository;
        $this->work_order_repository = $workOrderRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response($this->quote_repository->list());
    }

    /**
     * Display a list of quote data with reference to quote vs work order
     *
     * @param QuoteDataTableRequest $request
     * @return \Illuminate\Http\Response
     */
    public function indexExtended(QuoteDataTableRequest $request)
    {
        $validated = $request->validated();
        $limit = $request->limit ?? config('misc.dataTable.defaultPerPage');
        $sort_column = $request->sort_column ?? config('misc.dataTable.defaultSortColumn');
        $sort_order = $request->sort_order ?? config('misc.dataTable.defaultSortOrder');
        $offset = $request->offset ?? 0;
        $type = null;

        if (array_key_exists("type", $validated)) {
            $type = $validated['type'];
            unset($validated['type']);
        }

        $quote_query = $this->quote_repository->list(true)->leftJoin(
            WorkOrder::getTableName(),
            WorkOrder::getTableName() . '.quote_id',
            Quote::getTableName() . '.id'
        )->select(
            Quote::getTableName() . '.*',
            \DB::raw("COALESCE(work_orders.value, quotes.value) as sort_value")
        )->where(
            $validated
        );

        if ($type && $type === 'quote') {
            $quote_query->whereNull(WorkOrder::getTableName() . '.id');
        } elseif ($type && $type === 'work_order') {
            $quote_query->whereNotNull(WorkOrder::getTableName() . '.id');
        }

        return collect([
            'row_count' => $quote_query->get()->count(),
            'rows' => $quote_query->skip($offset)
                ->with(['property.portfolio.clientAccount'])
                ->take($limit)
                ->orderBy($sort_column, $sort_order)
                ->get(),
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @param QuoteDataTableRequest $request
     * @return \Illuminate\Http\Response
     */
    public function datatable(QuoteDataTableRequest $request)
    {
        $validated = $request->validated();
        $limit = $request->limit ?? config('misc.dataTable.defaultPerPage');
        $sort_column = Quote::getTableName() . '.' . (
            $request->sort_column ?? config('misc.dataTable.defaultSortColumn')
        );
        $sort_order = $request->sort_order ?? config('misc.dataTable.defaultSortOrder');
        $offset = $request->offset ?? 0;
        return response(
            $this->quote_repository->list(
                false,
                $validated,
                $sort_column,
                $sort_order,
                $limit,
                $offset
            )
        );
    }

    /**
     * @param QuoteStoreRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function store(QuoteStoreRequest $request)
    {
        return response(
            $this->quote_repository->store(
                $request->validated()
            )
        );
    }

    public function storeAutoAccepted(QuoteStoreRequest $request)
    {
        $valid_data = $request->validated();
        $valid_data['locked_note'] = self::AUTO_NOTE;
        $valid_data['locked_by_id'] = $request->user()->id;


        return \DB::transaction(function () use ($request, $valid_data) {
            $quote = $this->quote_repository->store(
                $valid_data
            );

            $work_order = $quote->workOrder()->create([
                'value' => $quote->value
            ]);

            return $work_order;
        });
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        return response($this->quote_repository->get($id));
    }

    /**
     * @param QuoteUpdateRequest $request
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function update(QuoteUpdateRequest $request, int $id)
    {
        $quote = $this->quote_repository->get($id);
        if ($quote->workorder && $quote->workorder->paid_at) {
            return response('Invoice has been paid. No updates may be performed on this item', 403);
        }
        $validated = $request->validated();
        if ($request['rejected_note']) {
            $validated['rejected_by'] = Auth::id();
            $validated['rejected_at'] = now()->toDateTimeString();
        }
        return response(
            $this->quote_repository->update(
                $id,
                $validated
            )
        );
    }

    /**
     * @param int $id
     * @param QuoteDeleteRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function destroy(int $id, QuoteDeleteRequest $request)
    {
        $valid = $request->validated();
        $quote = $this->quote_repository->get($id);
        $quote->locked_by_id = request()->user()->id;
        $quote->locked_note = $valid['locked_note'];
        $quote->save();
        $quote = $quote->fresh();

        return response()->json(true);
    }

    public function accept(QuoteAcceptRequest $request, int $id)
    {
        $valid = $request->validated();
        $quote = $this->quote_repository->get($id);
        $quote->locked_by_id = request()->user()->id;
        $quote->locked_note = $valid['locked_note'] ?? self::AUTO_NOTE;
        $quote->save();
        $quote = $quote->fresh();

        $work_order = $quote->workOrder()->create([
            'value' => $valid['value']
        ]);

        return response()->json($work_order);
    }

    public function toHotPDF(int $id)
    {
        $quote = $this->quote_repository->get($id);

        if ($quote->unit) {
            $quote->load(['unit', 'unit.propertyManager', 'unit.propertyManager.user']);
        }
        $quote->load(['property', 'property.propertyManager', 'property.propertyManager.user']);

        if ($quote->workOrder) {
            $pdf = \PDF::loadView(
                'pdf.base',
                [
                    'quote' => $quote->toArray(),
                    'work_order' => $quote->workOrder->toArray()
                ]
            );
        } else {
            $pdf = \PDF::loadView('pdf.base', ['quote' => $quote->toArray()]);
        }
        return $pdf->download('invoice.pdf');
    }
}
