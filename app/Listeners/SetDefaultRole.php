<?php

namespace App\Listeners;

use App\Events\Registered;
use App\Models\Role;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SetDefaultRole
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event. Not use since it does not work properly
     */
    public function handle(Registered $event): void
    {
        $defaultRole = Role::where('code', 'USER')->first();
        // Assign role USER to the user who has registered an account
        $event->user->role()->associate($defaultRole)->save();
    }
}
