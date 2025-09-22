<?php

namespace App\Actions\Users;

use App\Models\Invitation;
use App\Models\User;
use App\Integrations\SMSAPI\SMSApiClient;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Notification;

class CreateInvitation
{
    public function __construct(
        private SMSApiClient $smsClient
    ) {}

    public function __invoke(string $email = null, string $phone = null, array $roles = []): Invitation
    {
        $token = Str::uuid()->toString();
        
        $invitation = Invitation::create([
            'email' => $email,
            'phone' => $phone,
            'token' => $token,
            'expires_at' => now()->addDays(7),
            'inviter_id' => auth()->id(),
            'metadata' => ['roles' => $roles],
        ]);

        // Send email notification if email provided
        if ($email) {
            // TODO: Implement email notification
            // Notification::route('mail', $email)->notify(new InvitationNotification($invitation));
        }

        // Send SMS if phone provided
        if ($phone) {
            $message = "Zostałeś zaproszony do panelu administracyjnego. Kliknij link: " . route('invite.accept', $token);
            $this->smsClient->sendSms($phone, $message);
        }

        return $invitation;
    }
}
