<?php
namespace App\Modules\Reports\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Modules\Reports\Repositories\ReportRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;
use function GuzzleHttp\json_decode;
use App\Modules\Reports\Http\Requests\ConstrainedCsvRequest;

class ReportController extends Controller
{
    private $reportRepository;

    public function __construct(
        ReportRepository $reportRepository
    ) {
        $this->reportRepository = $reportRepository;
    }

    public function index()
    {
        return $this->reportRepository->all();
    }

    public function get($id)
    {
        return $this->reportRepository->find($id);
    }

    public function data($id)
    {
        $data = $this->reportRepository->getData($id, false);
        return response($data);
    }

    public function csv($id)
    {
        $report = $this->reportRepository->find($id);
        $date = Carbon::now()->format('d-m-Y_H:i:s_');
        $filename = $date . $report->slug;
        $data = $this->reportRepository->getData($id, true);


        return response([
            'filename' => $filename,
            'data'     => $data,
        ]);
    }

    public function csvWithIds(ConstrainedCsvRequest $data, $id)
    {
        $report = $this->reportRepository->find($id);
        $date = Carbon::now()->format('d-m-Y_H:i:s_');
        $filename = $date . $report->slug;
        $data = $this->reportRepository->getData($id, true, $data->input('ids'));


        return response([
            'filename' => $filename,
            'data'     => $data,
        ]);
    }
}
