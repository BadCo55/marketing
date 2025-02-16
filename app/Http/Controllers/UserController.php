<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\StoreUserRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Password;
use App\Notifications\ResetPasswordNotification;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        return inertia(
            'User/Index',
            [
                'users' => $users,
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
    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();
        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $path;
        }
        // dd($validated);
        $user = User::create($validated);
        if ($request->boolean('email_password_reset')) {
            $token = Password::createToken($user);
            $user->notify(new ResetPasswordNotification($token));
        }
        return back()->with([
            'success' => 'The user has been created successfully!',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return inertia(
            'User/Show',
            [
                'user' => $user
            ],
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();
        return back()->with([
            'success' => 'User has been deleted successfully',
        ]);
    }

    public function getUsers()
    {
        return response()->json(['users' => User::all()]);
    }

    public function uploadAvatar(Request $request, $id)
    {
        try {
            $request->validate([
                'avatar' => 'required|image|max:1024',
            ]);
            $user = User::findOrFail($id);
            // Delete the old avatar if it exists
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            // Store the new avatar
            $path = $request->file('avatar')->store('avatars', 'public');
            // Update the user's avatar field
            $user->avatar = $path;
            $user->save();
            return back()->with([
                'success' => 'Avatar uploaded successfully.',
                'user' => $user
                // 'avatar_url' => Storage::url($path),
            ]);
        } catch (\Exception $e) {
            logger()->error('There was an error uploading the avatar.', ['error' => $e->getMessage()]);
            return back()->with('error', 'There was an error uploading the avatar.');
        }
    }
}
