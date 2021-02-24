<?php
namespace App\Modules\Client\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Client\Http\Requests\Portfolios\PortfolioStoreRequest;
use App\Modules\Client\Http\Requests\Portfolios\PortfolioUpdateRequest;
use App\Modules\Client\Repositories\PortfolioRepository;
use App\Modules\Common\Http\Requests\Notes\NoteStoreRequest;
use App\Modules\Common\Services\NoteService;
use App\Modules\Core\Library\EloquentHelper;
use App\Modules\Edits\Http\Requests\GetEditAuditTrailRequest;
use App\Modules\Edits\Models\EditBatchType;
use App\Modules\Edits\Models\ReviewStatus;
use App\Modules\Edits\Services\EditBatchService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PortfolioController extends Controller
{
    /**
     * @var PortfolioRepository
     */
    protected $portfolio_repository;

    /**
     * @var EditBatchService
     */
    protected $edit_batch_service;

    /**
     * @var NoteService
     */
    protected $note_service;

    /**
     * PortfolioController constructor.
     * @param PortfolioRepository $portfolioRepository
     * @param EditBatchService $service
     * @param NoteService $note_service
     */
    public function __construct(
        PortfolioRepository $portfolioRepository,
        EditBatchService $service,
        NoteService $note_service
    ) {
        $this->portfolio_repository = $portfolioRepository;
        $this->edit_batch_service   = $service;
        $this->note_service         = $note_service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response($this->portfolio_repository->getPortfolios());
    }

    /**
     * @param PortfolioStoreRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function store(PortfolioStoreRequest $request)
    {
        $validated_data = $request->validated();
        return response($this->portfolio_repository->storePortfolio($validated_data));
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        return response($this->portfolio_repository->getPortfolio($id));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function find(Request $request)
    {
        return response(
            $this->portfolio_repository->findPortfolios($request->input('ids'))
        );
    }

    /**
     * @param PortfolioUpdateRequest $request
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \Exception
     * @throws \Throwable
     */
    public function update(PortfolioUpdateRequest $request, $id)
    {

        $portfolio = \DB::transaction(function () use ($request, $id) {

            $validated_data = $request->validated();
            $portfolio      = $this->portfolio_repository->getPortfolio($id);

            if (!Auth::user()->canByPassEdit()) {
                $result = $this->edit_batch_service->makeBatch($validated_data, EditBatchType::EDIT, $portfolio);

                if ($result) {
                    $portfolio = $this->lockPortfolio($id);
                }
            } else {
                $portfolio = $this->portfolio_repository->updatePortfolio($id, $validated_data);
            }
            return $portfolio;
        });

        return response($portfolio);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return response($this->portfolio_repository->deletePortfolio($id));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function dataTable(Request $request)
    {

        return response($this->portfolio_repository->getPortfolioDataTable(
            $request->sort_column ?? config('misc.dataTable.defaultSortColumn'),
            $request->sort_order ?? config('misc.dataTable.defaultSortOrder'),
            intval($request->offset),
            $request->limit ?? config('misc.dataTable.defaultPerPage'),
            intval($request->client_account_id),
            $request->portfolio_name_partial
        ));
    }

    /**
     * @param NoteStoreRequest $request
     * @param $id
     * @return mixed
     * @throws \Illuminate\Validation\ValidationException
     */
    public function storeNote(NoteStoreRequest $request, $id)
    {
        return $this->note_service->make([
            'user_id'     => Auth::user()->id,
            'entity_id'   => $id,
            'entity_type' => $this->portfolio_repository->getModelClass(),
            'note'        => $request->note
        ]);
    }

    /**
     * @param  $request
     * @return bool
     */
    public function getEditAuditTrail(GetEditAuditTrailRequest $request, $id)
    {

        $week_ending_date = Carbon::parse($request->week_ending_date);

        $entity = $this->portfolio_repository->getPortfolio($id);

        return $this->edit_batch_service->getEntityAuditTrail($entity, $week_ending_date);
    }

    /**
     * @return mixed
     */
    public function getEditable()
    {
        $model_obj = $this->portfolio_repository->getModelObject();
        return $model_obj->getEditable();
    }

    /**
     * @param $id
     * @return mixed
     * @throws \Exception
     * @throws \Throwable
     */
    public function flag($id)
    {

        $portfolio = \DB::transaction(function () use ($id) {
            $portfolio = $this->portfolio_repository->getPortfolio($id);

            $this->edit_batch_service->makeBatch($portfolio->toArray(), EditBatchType::FLAG, $portfolio);

            return $this->lockPortfolio($id);
        });
        return $portfolio;
    }

    /**
     * @param $id
     * @return \App\Modules\Client\Models\Portfolio
     */
    private function lockPortfolio($id)
    {

        return $this->portfolio_repository->updatePortfolio($id, [
            'locked_at'         => Carbon::now(),
            'locked_by_user_id' => Auth::user()->id,
            'review_status_id'  => EloquentHelper::getRecordIdBySlug(ReviewStatus::class, ReviewStatus::IN_REVIEW)
        ]);
    }
}
