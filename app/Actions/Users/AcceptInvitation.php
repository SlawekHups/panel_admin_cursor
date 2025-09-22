<?php

namespace App\Actions\Users;

use App\Models\Invitation;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AcceptInvitation
{
    public function __invoke(Invitation $invitation, string $password, string $name = null): User
    {
        // Create user account
        $user = User::create([
            'name' => $name ?? $invitation->email ?? 'User',
            'email' => $invitation->email,
            'phone' => $invitation->phone,
            'password' => Hash::make($password),
            'status' => 'active',
        ]);

        // Assign roles from invitation metadata
        if (isset($invitation->metadata['roles']) && is_array($invitation->metadata['roles'])) {
            $user->assignRole($invitation->metadata['roles']);
        }

        // Mark invitation as accepted
        $invitation->update([
            'accepted_at' => now(),
        ]);

        return $user;
    }
}
