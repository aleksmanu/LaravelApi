<?php

namespace App\Modules\Acquisition\Http\Controllers;

use App\Modules\Acquisition\Models\Site;
use App\Modules\Acquisition\Repositories\SiteRepository;
use App\Modules\Attachments\Traits\ControllerHasAttachments;
use App\Modules\Common\Traits\HasNotesController;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

class SiteController extends Controller
{
    use HasNotesController {
        getNotes as protected getNotesOld;
    }
    use ControllerHasAttachments {
        indexAttachments as protected indexAttachmentsOld;
    }

    protected $repository;
    protected $attachable_repository;
    protected $model;

    public function __construct(SiteRepository $siteRepository, Site $model)
    {
        $this->repository = $siteRepository;
        $this->attachable_repository = $siteRepository;
        $this->model = $model;
    }

    public function update(Request $request, $site)
    {
        return response($this->repository->updateSite($site, $request->all()));
    }

    public function create(Request $request)
    {
        return response($this->repository->createSite($request->all()));
    }

    public function show($siteId)
    {
        return response($this->repository->showSite($siteId));
    }

    public function getTimeline($id)
    {
        $site = $this->repository->showSite($id);
        return response($site->allTimelines());
    }

    public function indexAttachments($id)
    {
        $site = Site::findOrFail($id);

        return response($site->documentsIncludingFromSteps());
    }

    public function getNotes(int $assetId)
    {
        $site = Site::findOrFail($assetId);

        return response($site->notesIncludingFromSteps());
    }
}
