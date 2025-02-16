<?php

namespace App\Http\Controllers;

use App\Models\Realtor;
use Illuminate\Support\Facades\DB;
use App\Models\RealtorCommunicationLog;
use App\Http\Requests\StoreRealtorCommunicationLogRequest;
use App\Http\Requests\UpdateRealtorCommunicationLogRequest;

class RealtorCommunicationLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(StoreRealtorCommunicationLogRequest $request)
    {
        DB::beginTransaction();
        try {
            RealtorCommunicationLog::create($request->validated());
            DB::commit();
            $realtor = Realtor::with([
                'communications' => function ($query) {
                    $query->orderBy('created_at', 'desc'); // Newest first
                },
                'communications.createdBy',
                'createdBy'
            ])->find($request->input('realtor_id'));
            return back()->with([
                'success' => 'The communication log was stored successfully!',
                'realtor' => $realtor,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            logger()->error('There was an error storing a communication log.', [
                'error' => $e->getMessage(),
                'data' => $request->validated(),
            ]);
            return back()->with('error', 'There was an error storing the log. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(RealtorCommunicationLog $realtorCommunicationLog)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RealtorCommunicationLog $realtorCommunicationLog)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRealtorCommunicationLogRequest $request, RealtorCommunicationLog $realtorCommunicationLog)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RealtorCommunicationLog $realtorCommunicationLog)
    {
        //
    }
}
