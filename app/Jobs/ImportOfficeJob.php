<?php

namespace App\Jobs;

use App\Models\Office;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ImportOfficeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $row;
    protected $fullAddress;

    /**
     * Create a new job instance.
     */
    public function __construct($row, $fullAddress)
    {
        $this->row = $row;
        $this->fullAddress = $fullAddress;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $googleResponse = $this->fetchGooglePlaceDetails($this->fullAddress);

        // Skip if Google API fails
        if (!$googleResponse['place_id']) {
            logger()->error("Failed to find Google Place ID", [
                'company' => $this->row[1],
                'address' => $this->fullAddress
            ]);
            return;
        }

        // Format phone number to 999-999-9999
        $formattedPhone = $this->formatPhoneNumber($this->row[8]);

        // Save to DB
        Office::updateOrCreate(
            ['place_id' => $googleResponse['place_id']],
            [
                'parent_company' => $this->row[1],
                'street_address' => $this->row[3],
                'unit_number' => (isset($this->row[4]) && strtolower(trim($this->row[4])) !== 'null' && trim($this->row[4]) !== '')
                                    ? $this->row[4]
                                    : null,
                'city' => $this->row[5],
                'state' => $this->row[6],
                'zip_code' => $this->row[7],
                'office_phone' => $formattedPhone,
                'latitude' => $googleResponse['lat'],
                'longitude' => $googleResponse['lng'],
                'place_id' => $googleResponse['place_id'],
                'county' => $googleResponse['county'],
                'notes' => $this->row[13] ?? null,
            ]
        );
    }

        /**
     * Fetch Google Place Details.
     */
    private function fetchGooglePlaceDetails($address)
    {
        $apiKey = env('VITE_GOOGLE_API_KEY');
        $url = "https://maps.googleapis.com/maps/api/place/findplacefromtext/json";

        $response = Http::get($url, [
            'input' => $address,
            'inputtype' => 'textquery',
            'fields' => 'place_id,geometry',
            'key' => $apiKey,
        ])->json();

        if (!empty($response['candidates'])) {
            $placeId = $response['candidates'][0]['place_id'];
            return $this->fetchPlaceDetails($placeId, $apiKey);
        }

        return ['lat' => null, 'lng' => null, 'place_id' => null];
    }

    /**
     * Fetch Latitude & Longitude.
     */
    private function fetchPlaceDetails($placeId, $apiKey)
    {
        $url = "https://maps.googleapis.com/maps/api/place/details/json";

        $response = Http::get($url, [
            'place_id' => $placeId,
            'fields' => 'geometry,address_components',
            'key' => $apiKey,
        ])->json();

        if (!empty($response['result']['geometry']['location'])) {
            // Extract county (administrative area level 2)
            $county = null;
            if (!empty($response['result']['address_components'])) {
                foreach ($response['result']['address_components'] as $component) {
                    if (in_array('administrative_area_level_2', $component['types'])) {
                        $county = $component['long_name']; // Get the county name
                        break;
                    }
                }
            }

            return [
                'lat' => $response['result']['geometry']['location']['lat'],
                'lng' => $response['result']['geometry']['location']['lng'],
                'place_id' => $placeId,
                'county' => $county, // Store the extracted county
            ];
        }

        return ['lat' => null, 'lng' => null, 'place_id' => null, 'county' => null];
    }

    /**
     * Format phone number to 999-999-9999.
     */
    private function formatPhoneNumber($phone)
    {
        // Remove all non-numeric characters
        $digits = preg_replace('/\D/', '', $phone);

        // Ensure it's a 10-digit number
        if (strlen($digits) === 10) {
            return substr($digits, 0, 3) . '-' . substr($digits, 3, 3) . '-' . substr($digits, 6);
        }

        return $phone; // Return original if formatting fails
    }
}
