<?php

namespace App\Models;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Report extends Model
{
    /** @use HasFactory<\Database\Factories\ReportFactory> */
    use HasFactory;

    protected $fillable = [
        'report_type',
        'start_date',
        'end_date',
        'data'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'data' => 'json'
    ];

    protected $appends = [
        'formatted_type',
        'formatted_start_date',
        'formatted_end_date',
        'formatted_updated_at'
    ];

    public function getFormattedTypeAttribute()
    {
        return ucfirst($this->report_type);
    }

    public function getFormattedStartDateAttribute()
    {
        return Carbon::parse($this->start_date)->format('m/d/y');
    }

    public function getFormattedEndDateAttribute()
    {
        return Carbon::parse($this->end_date)->format('m/d/y');
    }

    public function getFormattedUpdatedAtAttribute()
    {
        return Carbon::parse($this->updated_at)->format('m/d/y g:i a');
    }


    /**
     * Generate report data.
     *
     * @return array
     */
    public function generateReportData(): array
    {
        // Convert stored JSON data into Laravel collections for easier processing
        $scheduled = collect($this->data['scheduled']);
        $occurred = collect($this->data['occurred']);
        $payments = collect($this->data['payments_received']);

        // Aggregate statistics
        $scheduledPaid = $scheduled->where('paid', 1)->count();
        $occurredPaid = $occurred->where('paid', 1)->count();
        $revenueScheduled = $scheduled->sum('total_fee');
        $revenuePassedDue = $occurred->where('paid', 0)->sum('total_fee');
        $revenueReceived = $payments->sum('amount');
        $officeVisitCount = $this->getOfficeVisitsDuringReport();
        $officesCreatedCount = $this->getOfficesCreatedDuringReport();
        $realtorsCreatedCount = $this->getRealtorsCreatedDuringReport();
        $completedTaskCount = $this->getCompletedTasksDuringReport();

        // Process scheduled & occurred inspections
        $processedScheduled = $this->processInspections($scheduled);
        $processedOccurred = $this->processInspections($occurred, true);

        return [
            'scheduled_count' => $scheduled->count(),
            'scheduled_paid' => $scheduledPaid,
            'occurred_count' => $occurred->count(),
            'occurred_paid' => $occurredPaid,
            'revenue_received' => $revenueReceived,
            'revenue_scheduled' => $revenueScheduled,
            'revenue_passed_due' => $revenuePassedDue,
            'office_visit_count' => $officeVisitCount,
            'realtors_created_count' => $realtorsCreatedCount,
            'offices_created_count' => $officesCreatedCount,
            'tasks_completed_count' => $completedTaskCount,
            'tabled_data' => [
                'occurred' => $processedOccurred,
                'scheduled' => $processedScheduled,
            ],
        ];
    }

    /**
     * Process a collection of inspections
     *
     * @param Collection $inspections
     * @param bool $isOccurred
     * @return array
     */
    private function processInspections(Collection $inspections, bool $isOccurred = false): array
    {
        return $inspections->map(function ($item) use ($isOccurred) {
            return [
                'id' => $item['inspection_number'],
                'inspector' => $this->formatName($item['main_inspector'] ?? null),
                'customer' => $this->formatName($item['primary_customer'] ?? null),
                'agent' => $this->formatName($item['primary_agent'] ?? null),
                'sqft' => $item['property']['square_footage'] ?? 0,
                'property_type' => $item['property']['type']['property_type'] ?? null,
                'total_fee' => $this->formatTotalFee($item['total_fee']),
                'is_paid' => $item['paid'],
                'payments' => $item['payments'],
                'row_class' => $isOccurred ? $this->getAgentRowClass($item['primary_agent'] ?? null) : null,
            ];
        })->toArray();
    }

    /**
     * Format full name for inspector, customer, or agent.
     *
     * @param array|null $person
     * @return string|null
     */
    private function formatName(?array $person): ?string
    {
        return $person ? "${person['first_name']} ${person['last_name']}" : null;
    }

    /**
     * Format total fee as currency.
     *
     * @param float $amount
     * @return string
     */
    private function formatTotalFee(float $amount): ?string
    {
        return '$' . number_format($amount, 2);
    }

    /**
     * Determines row class based on realtor existence.
     *
     * @param array|null $agent
     * @return bool
     */
    private function getAgentRowClass(?array $agent): bool
    {
        return $agent && Realtor::where('email', strtolower($agent['email']))->exists();
    }

    /**
     * Get the count of realtors created within the report's date range
     *
     * @return int
     */
    private function getRealtorsCreatedDuringReport(): int
    {
        return Realtor::whereBetween('created_at', [$this->start_date->startOfDay(), $this->end_date->endOfDay()])
            ->count();
    }

    /**
     * Get the count of office visits within the report's date range.
     *
     * @return int
     */
    private function getOfficeVisitsDuringReport(): int
    {
        return OfficeVisit::whereBetween('created_at', [$this->start_date->startOfDay(), $this->end_date->endOfDay()])
            ->count();
    }

    /**
     * Get the count of offices created within the report's date range
     *
     * @return int
     */
    private function getOfficesCreatedDuringReport(): int
    {
        return Office::whereBetween('created_at', [$this->start_date->startOfDay(), $this->end_date->endOfDay()])
            ->count();
    }

    /**
     * Get the count of tasks completed within the report's date range.
     *
     * @return int
     */
    public function getCompletedTasksDuringReport(): int
    {
        return Task::where('status', 'completed')
            ->whereBetween('updated_at', [$this->start_date->startOfDay(), $this->end_date->endOfDay()])
            ->count();
    }

    public static function fetchInspectionData($start_date, $end_date)
    {
        try {
            $response = Http::asForm()
                ->timeout(10)
                ->post('https://app.dhi-portal.net/customers/getInspectionsByDateRange', [
                    'start_date' => $start_date,
                    'end_date' => $end_date
                ]);

            if ($response->failed()) {
                Log::error('Failed to fetch inspection data', ['response' => $response->body()]);
                return ['status' => 'error', 'message' => 'Failed to fetch data from API.'];
            }

            $data = $response->json();

            if (!isset($data['occurring_today']) || !isset($data['created_today']) || !isset($data['payments'])) {
                Log::error('Invalid API response format', ['response' => $data]);
                return ['status' => 'error', 'message' => 'Invalid API response format.'];
            }

            return $data;
        } catch (\Illuminate\Http\Client\RequestException $e) {
            Log::error("Request timeout: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Request timed out. Please try again later.'];
        } catch (\Exception $e) {
            Log::error("Failed to fetch inspections: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Failed to fetch inspections.'];
        }
    }

    public function syncReportData()
    {
        $start_date = $this->start_date->toDateString();
        $end_date = $this->end_date->toDateString();

        Log::info("Starting sync for report: {$this->id}", [
            'start_date' => $start_date,
            'end_date' => $end_date,
        ]);

        // Fetch updated inspection data for the report's date range
        $response = self::fetchInspectionData($start_date, $end_date);

        if (!$response || $response['status'] !== 'success') {
            Log::error("Failed to fetch latest inspection data for report sync", [
                'response' => $response
            ]);
            return [
                'status' => 'error',
                'message' => 'Failed to fetch data. Please check the API connection.',
            ];
        }

        Log::info("Fetched new data from API", [
            'occurred' => count($response['occurring_today']),
            'scheduled' => count($response['created_today']),
            'payments' => count($response['payments']),
        ]);

        try {
            return DB::transaction(function () use ($response) {
                $beforeUpdate = $this->updated_at;

                $data = [
                    'occurred' => $response['occurring_today'],
                    'scheduled' => $response['created_today'],
                    'payments_received' => $response['payments'],
                ];

                Log::info("Updating report data for report: {$this->id}");

                $this->update([
                    'data' => $data,
                ]);

                $this->refresh(); // Reload the model from the database

                Log::info("Report updated successfully", [
                    'report_id' => $this->id,
                    'updated_at_before' => $beforeUpdate,
                    'updated_at_after' => $this->updated_at
                ]);

                return ['status' => 'updated', 'message' => 'Report data has been refreshed.'];
            });
        } catch (\Exception $e) {
            Log::error("Sync report failed", [
                'report_id' => $this->id,
                'error' => $e->getMessage(),
            ]);
            return [
                'status' => 'error',
                'message' => 'Database error. Please try again later.',
            ];
        }
    }
}
