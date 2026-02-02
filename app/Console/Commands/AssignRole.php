<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class AssignRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assign:role {email} {role}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign a role to a user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $role = $this->argument('role');

        $user = \App\Models\User::where('email', $email)->first();

        if (!$user) {
            $this->error("User with email {$email} not found.");
            return;
        }

        $roles = $user->roles ?? [];
        if (!in_array($role, $roles)) {
            $roles[] = $role;
            $user->update(['roles' => $roles]);
            $this->info("Role '{$role}' assigned to {$email}.");
        } else {
            $this->info("User already has role '{$role}'.");
        }
    }
}
