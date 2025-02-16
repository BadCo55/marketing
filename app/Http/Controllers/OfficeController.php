<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Office;
use Illuminate\Http\Request;
use App\Jobs\ImportOfficeJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreOfficeRequest;

class OfficeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $offices = Office::all();
        foreach ($offices as $office) {
            $office->append('image_url');
        }
        return inertia(
            'Office/Index',
            [
                'offices' => $offices,
            ]
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
    public function store(StoreOfficeRequest $request)
    {
        $office = Office::create($request->validated());
        $offices = Office::all();
        return back()->with([
            'success' => 'Office created successfully!',
            'offices' => $offices,
        ]);
    }

    /**
     * Upload the office's image
     */
    public function uploadImage(Request $request, $id)
    {
        $request->validate([
            'image' => 'required|image|max:1024',
        ]);
        $office = Office::findOrFail($id);
        // Delete the old avatar if it exists
        if ($office->avatar) {
            Storage::disk('public')->delete($office->image);
        }

        // Store the new avatar
        $path = $request->file('image')->store('avatars', 'public');

        // Update the realtor's avatar field
        $office->image = $path;
        $office->save();

        return response()->json([
            'message' => 'Avatar uploaded successfully.',
            'image_url' => Storage::url($path),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Office $office)
    {
        // $realtors = $office->realtors;
        $office->append('image_url');
        $users = User::all();

        $office->load([
            'notes' => function ($query) {
                $query->orderByDesc('is_pinned') // Pinned items first
                      ->orderByDesc('created_at'); // Newest items first
            },
            'notes.createdBy',
            'notes.assignedTo',
            'realtors',
            'visits'
        ]);
        $users = User::all();

        return inertia(
            'Office/Show',
            [
                'office' => $office,
                'users' => $users,
            ]
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Office $office)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Office $office)
    {
        if (!$request->input('request_type')) {
            return back()->with('error', 'Invalid request type. Please try again.');
        }
        if ($request->input('request_type') === 'update_details') {
            $validated = $request->validate([
                'parent_company' => 'required|string|max:255',
                'office_name' => 'nullable|string|max:255',
                'street_address' => 'required|string|max:255',
                'unit_number' => 'nullable|string|max:255',
                'city' => 'required|string|max:255',
                'state' => 'required|string|max:255',
                'zip_code' => 'required|numeric',
                'place_id' => 'required|string|max:255',
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
            ]);
            try {
                DB::beginTransaction();
                $office->update($validated);
                DB::commit();
                return back()->with([
                    'office' => $office,
                    'success' => 'The office details were updated successfully!'
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                logger()->error('There was an error updating office details', ['error' => $e->getMessage(), 'data' => $validated]);
                return back()->with('error', 'There was an error updating the details. Please try again.');
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Office $office)
    {
        //
    }

    /**
     * API end point to get all offices
     */
    public function getOffices()
    {
        return response()->json(['offices' => Office::all()]);
    }

    public function import(Request $request)
    {
        // Validate the file
        $request->validate([
            'file' => 'required|mimes:csv,txt|max:2048',
        ]);

        // Read CSV into an array
        $file = $request->file('file');
        $data = array_map('str_getcsv', file($file->getPathname()));
        $headers = array_shift($data); // Remove header row

        // Store failed entries for reporting
        $failedEntries = [];

        collect($data)->each(function ($row) use (&$failedEntries) {
            $fullAddress = "{$row[3]}, {$row[5]}, {$row[6]}, {$row[7]}";

            // Dispatch a queue job to handle each office separately
            ImportOfficeJob::dispatch($row, $fullAddress)
                ->delay(now()->addSeconds(1)); // Delay to prevent Google rate limits
        });

        // Return immediately with success message
        return back()->with([
            'success' => "Import started! Offices are being processed in the background.",
        ]);
    }

    private function fetchGooglePlaceDetails($address)
    {
        $apiKey = env('VITE_GOOGLE_API_KEY'); // Store API key in .env
        $url = "https://maps.googleapis.com/maps/api/place/findplacefromtext/json";

        $response = Http::get($url, [
            'input' => $address,
            'inputtype' => 'textquery',
            'fields' => 'place_id,geometry',
            'key' => $apiKey,
        ])->json();

        if (!empty($response['candidates'])) {
            $placeId = $response['candidates'][0]['place_id'];

            // Fetch lat/lng using the place_id
            return $this->fetchPlaceDetails($placeId, $apiKey);
        }

        return ['lat' => null, 'lng' => null, 'place_id' => null];
    }

    private function fetchPlaceDetails($placeId, $apiKey)
    {
        $url = "https://maps.googleapis.com/maps/api/place/details/json";

        $response = Http::get($url, [
            'place_id' => $placeId,
            'fields' => 'geometry',
            'key' => $apiKey,
        ])->json();

        if (!empty($response['result']['geometry']['location'])) {
            return [
                'lat' => $response['result']['geometry']['location']['lat'],
                'lng' => $response['result']['geometry']['location']['lng'],
                'place_id' => $placeId,
            ];
        }

        return ['lat' => null, 'lng' => null, 'place_id' => null];
    }
}
