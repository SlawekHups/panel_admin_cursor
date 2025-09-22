<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Users\AcceptInvitation;
use App\Http\Controllers\Controller;
use App\Models\Invitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AcceptInvitationController extends Controller
{
    public function show(string $token)
    {
        $invitation = Invitation::where('token', $token)->first();

        if (!$invitation || !$invitation->isValid()) {
            return view('invitation.expired');
        }

        return view('invitation.accept', compact('invitation'));
    }

    public function store(Request $request, string $token, AcceptInvitation $acceptInvitation)
    {
        $invitation = Invitation::where('token', $token)->first();

        if (!$invitation || !$invitation->isValid()) {
            throw ValidationException::withMessages([
                'token' => ['Invitation is invalid or expired.'],
            ]);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        try {
            $user = $acceptInvitation($invitation, $request->password, $request->name);

            Auth::login($user);

            return redirect()->route('filament.admin.pages.dashboard')
                ->with('success', 'Account created successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to create account. Please try again.']);
        }
    }
}
