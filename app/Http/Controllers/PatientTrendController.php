<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\TrendExport;
use App\Models\Queue;

class PatientTrendController extends Controller
{
    /** GET /trends */
    public function index(Request $req)
    {
        // ── basic filters ─────────────────────────────────────────────
        $from        = $req->input('from', now()->subDays(60)->format('Y-m-d'));
        $to          = $req->input('to',   now()->format('Y-m-d'));
        $model       = $req->input('model', 'ensemble');   // arima | lstm | ensemble
        $queueId     = $req->input('department');          // queue_id
        $steps       = (int) $req->input('steps', 14);     // 7 | 14 | 30 ...
        $windowSize  = (int) $req->input('window_size', 14);

        // ── call Flask service ───────────────────────────────────────
        $client = new Client(['base_uri' => 'https://passionate-dream-production.up.railway.app/', 'timeout' => 5]);

        try {
            $response = $client->get('trends/result', [
                'query' => [
                    'from'        => $from,
                    'to'          => $to,
                    'queue_id'    => $queueId,
                    'model'       => $model,
                    'steps'       => $steps,
                    'window_size' => $windowSize,
                ],
            ]);
            $trend = json_decode($response->getBody(), true);
        } catch (\Throwable $e) {
            report($e);
            $trend = null;
        }

        // ── load queues for dropdown ─────────────────────────────────
        $queues = Queue::orderBy('name')->get(['id', 'name']);

        return view('trends.index', compact(
            'from', 'to', 'model', 'trend',
            'queueId', 'queues', 'steps', 'windowSize'
        ))->with('department', $queueId); // keep old var name for convenience
    }

    /** POST /trends/generate-new */
    public function requestNew(Request $req)
    {
        $from        = $req->input('from', now()->subDays(60)->format('Y-m-d'));
        $to          = $req->input('to',   now()->format('Y-m-d'));
        $queueId     = $req->input('department');
        $model       = $req->input('model', 'ensemble');
        $steps       = (int) $req->input('steps', 14);
        $windowSize  = (int) $req->input('window_size', 14);

        $client = new Client(['base_uri' => 'https://passionate-dream-production.up.railway.app/', 'timeout' => 10]);
        try {
            $client->post('trends/analyse', [
                'json' => [
                    'from'        => $from,
                    'to'          => $to,
                    'queue_id'    => $queueId,
                    'model'       => $model,
                    'steps'       => $steps,
                    'window_size' => $windowSize,
                ],
            ]);
        } catch (\Throwable $e) {
            report($e);
        }

        return redirect()->route('trends.index', [
            'from'        => $from,
            'to'          => $to,
            'department'  => $queueId,
            'model'       => $model,
            'steps'       => $steps,
            'window_size' => $windowSize,
        ]);
    }

    /** GET /trends/excel */
    public function exportExcel(Request $req)
    {
        $from        = $req->input('from');
        $to          = $req->input('to');
        $model       = $req->input('model', 'ensemble');
        $queueId     = $req->input('department');
        $steps       = (int) $req->input('steps', 14);
        $windowSize  = (int) $req->input('window_size', 14);

        $client   = new Client(['base_uri' => 'https://passionate-dream-production.up.railway.app/']);
        $response = $client->get('trends/result', [
            'query' => [
                'from'        => $from,
                'to'          => $to,
                'queue_id'    => $queueId,
                'model'       => $model,
                'steps'       => $steps,
                'window_size' => $windowSize,
            ],
        ]);
        $trend = json_decode($response->getBody(), true);

        // rows for Excel
        $rows = [['Date', 'Historical Mean', strtoupper($model).' Forecast']];
        foreach ($trend[$model]['dates'] as $i => $d) {
            $rows[] = [$d, $trend['historical_mean'], $trend[$model]['values'][$i] ?? ''];
        }

        return Excel::download(
            new TrendExport($rows),
            "patient-trend-{$from}-{$to}.xlsx"
        );
    }

    /** GET /trends/pdf */
    public function exportPdf(Request $req)
    {
        $from        = $req->input('from');
        $to          = $req->input('to');
        $model       = $req->input('model', 'ensemble');
        $queueId     = $req->input('department');
        $steps       = (int) $req->input('steps', 14);
        $windowSize  = (int) $req->input('window_size', 14);

        $client = new Client(['base_uri' => 'https://passionate-dream-production.up.railway.app/']);
        $response = $client->get('trends/result', [
            'query' => [
                'from'        => $from,
                'to'          => $to,
                'queue_id'    => $queueId,
                'model'       => $model,
                'steps'       => $steps,
                'window_size' => $windowSize,
            ],
        ]);
        $trend = json_decode($response->getBody(), true);

        $pdf = Pdf::loadView('trends.pdf', compact(
            'from','to','model','trend','queueId','steps','windowSize'
        ));

        return $pdf->download("patient-trend-{$from}-{$to}.pdf");
    }
}
