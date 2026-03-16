<?php

namespace App\Console\Commands;

use App\Models\Invitation;
use Illuminate\Console\Command;

class ListInvitations extends Command
{
    protected $signature = 'invitations:list';
    protected $description = 'List all pending invitations with their links';

    public function handle()
    {
        $invitations = Invitation::where('status', 'pending')
            ->with('colocation.owner')
            ->get();

        if ($invitations->isEmpty()) {
            $this->info('No pending invitations found.');
            return 0;
        }

        $this->info('Pending Invitations:');
        $this->info(str_repeat('=', 50));

        foreach ($invitations as $invitation) {
            $link = route('invitations.show', $invitation->token);
            
            $this->info("Email: {$invitation->email}");
            $this->info("Colocation: {$invitation->colocation->name}");
            $this->info("Owner: {$invitation->colocation->owner->name}");
            $this->info("Expires: {$invitation->expires_at->format('Y-m-d H:i')}");
            $this->info("Link: {$link}");
            $this->info(str_repeat('-', 50));
        }

        return 0;
    }
}
