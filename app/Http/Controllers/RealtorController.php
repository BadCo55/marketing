<?php

namespace App\Http\Controllers;

use App\Exceptions\OfficeAlreadyAssignedException;
use App\Models\User;
use App\Models\Office;
use App\Models\Realtor;
use App\Models\Sequence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreRealtorRequest;
use Carbon\Carbon;
use Exception;

class RealtorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $realtors = Realtor::all();
        foreach ($realtors as $realtor) {
            if ($realtor['office_id']) {
                $realtor['office'] = Office::find($realtor['office_id']);
            }
            $realtor->append('avatar_url');
        }
        return inertia(
            'Realtor/Index',
            [
                'realtors' => $realtors,
            ],
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return inertia(
            'Realtor/Create',
            [
                'offices' => Office::all(),
            ],
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRealtorRequest $request)
    {
        $validated = $request->validated();
        $realtor = [
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'phone_number' => $validated['phone_number'],
            'email' => $validated['email'],
            'office_id' => null,
            'title' => $validated['title'],
        ];
        if ($validated['office']) {
            $realtor['office_id'] = $validated['office']['id'];
        }
        // Fetch previous inspections using the new method
        $previousInspections = [];
        $previousInspectionsCount = 0;
        $previousInspections = $this->fetchPreviousInspections($realtor['email']);
        if (!empty($previousInspections['inspections'])) {
            $previousInspectionsCount = count($previousInspections['inspections']);
        }
        $totalValue = 0;
        $averageValue = 0;
        if (!empty($previousInspections['inspections'])) {
            $inspectionCount = count($previousInspections['inspections']); // Total number of inspections
            foreach ($previousInspections['inspections'] as &$inspection) { // Use reference (&) to modify the array
                $totalValue += $inspection['total_fee'];
                $inspection['inspection_date'] = date('m/d/y', strtotime($inspection['inspection_date']));
            }
            unset($inspection); // Unset the reference after use to avoid side effects
            $averageValue = $totalValue / $inspectionCount; // Calculate average
        }
        // Format values as $0.00
        $formattedTotalValue = '$' . number_format($totalValue, 2);
        $formattedAverageValue = '$' . number_format($averageValue, 2);
        $realtor['previous_inspections'] = $previousInspections['inspections'] ? $previousInspections['inspections'] : [];
        $realtor = Realtor::create($realtor);
        return redirect()->route('realtor.show', $realtor->id)->with('success', 'Realtor was created successfully!');
    }

    /**
     * Upload the realtor's avatar image
     */
    public function uploadAvatar(Request $request, $id)
    {
        try {
            $request->validate([
                'avatar' => 'required|image|max:1024',
            ]);
            $realtor = Realtor::findOrFail($id);
            // Delete the old avatar if it exists
            if ($realtor->avatar) {
                Storage::disk('public')->delete($realtor->avatar);
            }
            // Store the new avatar
            $path = $request->file('avatar')->store('avatars', 'public');
            // Update the realtor's avatar field
            $realtor->avatar = $path;
            $realtor->save();
            return back()->with([
                'success' => 'Avatar uploaded successfully.',
                'realtor' => $realtor
                // 'avatar_url' => Storage::url($path),
            ]);
        } catch (\Exception $e) {
            logger()->error('There was an error uploading the avatar.', ['error' => $e->getMessage()]);
            return back()->with('error', 'There was an error uploading the avatar.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Realtor $realtor)
    {
        $realtor = Realtor::findOrFail($realtor['id']);
        $realtor->append('avatar_url');

        $realtor->load([
            'activeSequences',
            'owner',
            'office',
            'communications' => function ($query) {
                $query->orderBy('created_at', 'desc'); // Sort communications newest first
            },
            'communications.createdBy',
            'notes' => function ($query) {
                $query->orderBy('created_at', 'desc'); // Sort notes newest first
            },
            'notes.assignedTo',
        ]);

        // // Fetch previous inspections using the new method
        // $previousInspections = [];
        // $previousInspectionsCount = 0;
        // $previousInspections = $this->fetchPreviousInspections($realtor->email);

        // if (!empty($previousInspections['inspections'])) {
        //     $previousInspectionsCount = count($previousInspections['inspections']);
        // }

        // $totalValue = 0;
        // $averageValue = 0;

        // if (!empty($previousInspections['inspections'])) {
        //     $inspectionCount = count($previousInspections['inspections']); // Total number of inspections
        //     foreach ($previousInspections['inspections'] as &$inspection) { // Use reference (&) to modify the array
        //         $totalValue += $inspection['total_fee'];
        //         $inspection['inspection_date'] = date('m/d/y', strtotime($inspection['inspection_date']));
        //     }
        //     unset($inspection); // Unset the reference after use to avoid side effects
        //     $averageValue = $totalValue / $inspectionCount; // Calculate average
        // }

        // // Format values as $0.00
        // $formattedTotalValue = '$' . number_format($totalValue, 2);
        // $formattedAverageValue = '$' . number_format($averageValue, 2);

        if ($realtor->office_id) {
            $realtor->office = Office::find($realtor->office_id);
        }

        return inertia(
            'Realtor/Show',
            [
                'realtor' => $realtor,
                'sequences' => Sequence::with('steps')->get(),
                'users' => User::all(),
            ],
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Realtor $realtor)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Realtor $realtor)
    {
        if ($request->request_type === 'update_owner') {
            $validated = $request->validate([
                'owner' => 'required|exists:users,id'
            ]);
            DB::beginTransaction();
            try {
                $realtor->update($validated);
                DB::commit();
                return back()->with('success', 'The realtor\'s owner has been updated successfully!');
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error updating contact owner.', ['error' => $e->getMessage()]);
                return response()->json(['summary' => 'Error!', 'detail' => 'Error updating realtor\'s owner. Please try again.']);
            }
        } else if ($request->input('request_type') === 'update_professional_info') {
            $validated = $request->validate([
                'specializations' => 'nullable|array',
                'license_number' => 'nullable|string|max:255',
                'years_in_business' => 'nullable|integer',
                'title' => 'nullable|string|max:50',
                'primary_region' => 'nullable|array',
                'preferred_inspector' => 'nullable|array',
                'preferred_inspection_company' => 'nullable|string|max:50',
            ]);
            DB::beginTransaction();
            try {
                $realtor->update($validated);
                DB::commit();
                return back()->with('success', 'The realtor\'s professional info has been updated successfully!');
            } catch (\Exception $e) {
                DB::rollBack();
                logger()->error('Error updating realtor\'s professional info.', ['error' => $e->getMessage()]);
                return back()->with('error', 'Error updating realtor\'s professional info. Please try again.');
            }
        } else if ($request->input('request_type') === 'remove_affiliation') {
            DB::beginTransaction();
            try {
                $realtor->update([
                    'office_id' => null,
                ]);
                DB::commit();
                return back()->with([
                    'success' => 'The office was removed successfully!',
                    'realtor' => $realtor,
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                logger()->error('There was an error removing an office affiliation', ['error' => $e->getMessage()]);
                return back()->with('error', 'There was an error removing the office. Please try again.');
            }
        } else if ($request->input('request_type') === 'set_affiliation') {
            DB::beginTransaction();
            try {
                if ($realtor->office && $request->input('office.id') === $realtor->office->id) {
                    throw new OfficeAlreadyAssignedException();
                }
                $realtor->update([
                    'office_id' => $request->input('office.id'),
                ]);
                DB::commit();
                return back()->with([
                    'success' => 'The office was added successfully!',
                    'realtor' => $realtor,
                ]);
            } catch (OfficeAlreadyAssignedException $e) {
                DB::rollBack();
                logger()->error('There was an error setting an office affiliation', ['error' => $e->getMessage()]);
                return back()->with('error', $e->getMessage());
            } catch (\Exception $e) {
                DB::rollBack();
                logger()->error('There was an error setting an office affiliation', ['error' => $e->getMessage()]);
                return back()->with('error', 'There was an error changing the office. Please try again.');
            }
        } else {
            $validated = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'phone_number' => 'required|string|max:20',
                'email' => 'required|email|max:255',
                'acquired_via' => 'nullable|string|max:50',
                'bni_member' => 'nullable|boolean',
                'bni_chapter' => 'nullable|string|max:255',
                'dob' => 'nullable|date',
            ]);
            $validated['dob'] = !empty($validated['dob'])
                ? Carbon::parse($validated['dob'])->format('Y-m-d')
                : null;
            $socialMediaLinks = [
                'facebook' => $request->input('facebook'),
                'instagram' => $request->input('instagram'),
                'linkedin' => $request->input('linked_in'),
                'tiktok' => $request->input('tiktok'),
                'website' => $request->input('website'),
            ];
            // Remove empty values
            $socialMediaLinks = array_filter($socialMediaLinks);
            DB::beginTransaction();
            try {
                $realtor->social_media_links = json_encode($socialMediaLinks);
                $realtor->update($validated);
                DB::commit();
                return back()->with('success', 'The realtor\'s personal info was updated successfully!');
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error updating realtor\'s personal info.', ['error' => $e->getMessage()]);
                return back()->with('error', 'Error updating realtor\'s personal info. Please try again.');
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Realtor $realtor)
    {
        //
    }

    // private function fetchPreviousInspections(string $email)
    // {
    //     try {
    //         // Ensure the email is sent as part of the POST request
    //         $response = Http::asForm()->post('https://app.dhi-portal.net/customers/getPreviousInspectionsByEmail', [
    //             'email' => $email,
    //         ]);

    //         // Debug response
    //         if ($response->failed()) {
    //             Log::error('Failed to fetch inspections:', $response->json());
    //         }

    //         return $response->json();
    //     } catch (\Exception $e) {
    //         // Log the error and return an empty array
    //         Log::error("Failed to fetch inspections: " . $e->getMessage());
    //         return [
    //             'status' => 'error',
    //             'message' => 'Failed to fetch inspections.',
    //         ];
    //     }
    // }

    private function fetchPreviousInspections(string $email)
    {
        try {
            // Make an HTTP POST request with a timeout of 10 seconds
            $response = Http::asForm()
                ->timeout(10) // Set timeout to 10 seconds
                ->post('https://app.dhi-portal.net/customers/getPreviousInspectionsByEmail', [
                    'email' => $email,
                ]);

            // Debug response if failed
            if ($response->failed()) {
                Log::error('Failed to fetch inspections:', $response->json());
            }

            return $response->json();
        } catch (\Illuminate\Http\Client\RequestException $e) {
            // Catch timeout exception
            Log::error("Request timeout: " . $e->getMessage());

            return [
                'status' => 'error',
                'message' => 'Request timed out. Please try again later.',
            ];
        } catch (\Exception $e) {
            // Catch general errors
            Log::error("Failed to fetch inspections: " . $e->getMessage());

            return [
                'status' => 'error',
                'message' => 'Failed to fetch inspections.',
            ];
        }
    }
}
