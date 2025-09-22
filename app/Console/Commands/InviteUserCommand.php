<?php

namespace App\Console\Commands;

use App\Actions\Users\CreateInvitation;
use Illuminate\Console\Command;

class InviteUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:invite {email?} {--phone=} {--role=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Invite a user to the admin panel';

    /**
     * Execute the console command.
     */
    public function handle(CreateInvitation $createInvitation)
    {
        $email = $this->argument('email');
        $phone = $this->option('phone');
        $role = $this->option('role');

        if (!$email && !$phone) {
            $this->error('Please provide either email or phone number.');
            return 1;
        }

        $roles = $role ? [$role] : ['Viewer'];

        try {
            $invitation = $createInvitation($email, $phone, $roles);

            $this->info('Invitation created successfully!');
            $this->info("Token: {$invitation->token}");
            $this->info("Expires at: {$invitation->expires_at}");

            if ($email) {
                $this->info("Email invitation sent to: {$email}");
            }

            if ($phone) {
                $this->info("SMS invitation sent to: {$phone}");
            }

            return 0;
        } catch (\Exception $e) {
            $this->error("Failed to create invitation: {$e->getMessage()}");
            return 1;
        }
    }
}
