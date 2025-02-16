<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Report;
use App\Models\Realtor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Http\Requests\StoreReportRequest;
use App\Http\Requests\UpdateReportRequest;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reports = Report::all();

        // Check if daily report has been generated
        $today = now()->toDateString();
        $dailyReportExists = Report::where('report_type', 'daily')
            ->whereDate('start_date', $today)
            ->exists();

        return inertia(
            'Report/Index',
            [
                'reports' => $reports,
                'missing_daily_report' => $dailyReportExists,
            ],
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreReportRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Report $report)
    {
        $reportData = $report->generateReportData();
        // $vonageCalls = $report->getVonageCallsDuringReport();

        return inertia(
            'Report/Show',
            [
                'report' => $report,
                'data' => $reportData,
                // 'vonage_calls' => $vonageCalls,
            ]
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Report $report)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateReportRequest $request, Report $report)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Report $report)
    {
        $report->delete();
        $reports = Report::all();
        return back()->with([
            'success' => 'Report was deleted successfully.',
            'reports' => $reports,
        ]);
    }

    public function generateTodaysReport(Request $request)
    {
        $today = now()->toDateString();
        $response = $this->fetchInspectionData($today, $today);
        if ($response && $response['status'] === 'success') {
            DB::beginTransaction();
            try {
                $dailyReport = Report::where('report_type', 'daily')
                    ->whereDate('start_date', $today)
                    ->first();

                if ($dailyReport) {
                    $dailyReport->update([
                        'data' => [
                            'occurred' => $response['occurring_today'],
                            'scheduled' => $response['created_today'],
                            'payments_received' => $response['payments'],
                        ]
                    ]);
                    DB::commit();
                    return back()->with([
                        'success' => 'Report already exists but data was refreshed!',
                        'reports' => Report::all(),
                    ]);
                } else {
                    Report::create([
                        'report_type' => 'daily',
                        'start_date' => $today,
                        'end_date' => $today,
                        'data' => [
                            'occurred' => $response['occurring_today'],
                            'scheduled' => $response['created_today'],
                            'payments_received' => $response['payments']
                        ]
                    ]);
                    DB::commit();
                    return back()->with([
                        'success' => 'Report was generated successfully!',
                        'reports' => Report::all(),
                    ]);
                }

            } catch (\Exception $e) {
                DB::rollBack();
                logger()->error("There was an error generating the daily report for {$today}", ['error' => $e->getMessage()]);
                return back()->with('error', 'The report could not be generated. Please try again.');
            }
        } else {
            logger()->error("A response could not be received");
            return back()->with('error', 'No response was received. Please try again.');
        }
    }

    public function syncReport(Report $report)
    {
        $result = $report->syncReportData(); // ✅ Call syncReportData() on the report instance

        if ($result['status'] === 'error') {
            return back()->with('error', $result['message']);
        }

        return back()->with('success', $result['message']);
    }

    public function getData(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date'
        ]);

        $startDate = date('Y-m-d', strtotime($validated['start_date']));
        $endDate = date('Y-m-d', strtotime($validated['end_date']));

        $data = $this->fetchInspectionData($startDate, $endDate);
        $processedData = $this->processInspectionData($data);

        dd($processedData);
    }

    private function fetchInspectionData($start_date, $end_date)
    {
        try {
            $response = Http::asForm()
                ->timeout(10)
                ->post('https://app.dhi-portal.net/customers/getInspectionsByDateRange', [
                    'start_date' => $start_date,
                    'end_date' => $end_date
                ]);

            if ($response->failed()) {
                logger()->error('Failed to fetch inspection data', ['response' => $response->body()]);
                return ['status' => 'error', 'message' => 'Failed to fetch data from API.'];
            }

            $data = $response->json();

            if (!isset($data['occurring_today']) || !isset($data['created_today']) || !isset($data['payments'])) {
                logger()->error('Invalid API response format', ['response' => $data]);
                return ['status' => 'error', 'message' => 'Invalid API response format.'];
            }

            return $data;
        } catch (\Illuminate\Http\Client\RequestException $e) {
            logger()->error("Request timeout: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Request timed out. Please try again later.'];
        } catch (\Exception $e) {
            logger()->error("Failed to fetch inspections: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Failed to fetch inspections.'];
        }
    }

    public function generateReportByDateRange(Request $request)
    {
        // Validate the date range parameters
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);


        // Get the validated dates
        $startDate = date('Y-m-d', strtotime($validated['start_date']));
        $endDate = date('Y-m-d', strtotime($validated['end_date']));

        // Fetch inspection data for the provided date range
        $response = $this->fetchInspectionData($startDate, $endDate);

        if ($response && $response['status'] === 'success') {
            DB::beginTransaction();
            try {
                // Check if the report already exists for the provided date range
                $existingReport = Report::where('report_type', 'custom')
                    ->whereDate('start_date', $startDate)
                    ->whereDate('end_date', $endDate)
                    ->first();

                // Prepare the data to be saved in the report
                $reportData = [
                    'occurred' => $response['occurring_today'],
                    'scheduled' => $response['created_today'],
                    'payments_received' => $response['payments'],
                ];

                if ($existingReport) {
                    // If the report exists, update the data
                    $existingReport->update([
                        'data' => $reportData,
                    ]);
                    DB::commit();
                    return back()->with([
                        'success' => 'Report already exists but data was refreshed!',
                        'reports' => Report::all(),
                    ]);
                } else {
                    // If the report doesn't exist, create a new report
                    Report::create([
                        'report_type' => 'custom', // You can adjust this type as needed
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                        'data' => $reportData,
                    ]);
                    DB::commit();
                    return back()->with([
                        'success' => 'Report generated successfully!',
                        'reports' => Report::all(),
                    ]);
                }
            } catch (\Exception $e) {
                DB::rollBack();
                logger()->error("There was an error generating the report for {$startDate} to {$endDate}", ['error' => $e->getMessage()]);
                return back()->with('error', 'The report could not be generated. Please try again.');
            }
        } else {
            logger()->error("A response could not be received for the date range {$startDate} to {$endDate}");
            return back()->with('error', 'No response was received. Please try again.');
        }
    }

    private function processInspectionData($data)
    {
        $unprocessedOccurring = $data['occurring_today'];
        $unprocessedCreated = $data['created_today'];

        function formatInspectionTypes($inspection)
        {
            $inspection_types = [];
            if ($inspection['general_inspection'] === 1) {
                $inspection_types[] = 'General Inspection';
            }
        }

        /**
         * Inspections that were scheduled today
         * Inspections that were paid today
         * Inspections that occurred today
         * Inspections that occurred with customers that exist in marketing portal
         * Fees owed
         */

        $inspection = [
            'scheduled_at' => '',
            'scheduled_for' => '',
            'inspection_types' => '',
            'is_paid' => '',
            'payments' => '',
            'property' => '',
            'main_inspector' => '',
            'primary_customer' => '',
            'primary_agent' => '',
            'payment_reminded' => '',
        ];

        return [
            // 'occurred' => $inspectionsOccurred,
            // 'scheduled' => $inspectionsScheduled,
        ];
    }
}
