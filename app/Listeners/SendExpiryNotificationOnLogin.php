<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Mail;
use App\Mail\ExpiryNotificationMail;
use Carbon\Carbon;
use App\Models\Employee;
use App\Models\Machine;
use App\Models\Fire;
use App\Models\Miscellaneous;

class SendExpiryNotificationOnLogin
{
    public function handle(Login $event)
    {
        $user = $event->user;

        // Filtriraj po korisniku ako modeli imaju user_id
        $now = Carbon::now();
        $in30 = $now->copy()->addDays(30);

        $data = [
            'zaposlenici' => Employee::where('user_id', $user->id)
                ->whereBetween('medical_examination_valid_until', [$now, $in30])
                ->get(),
            'strojevi' => Machine::where('user_id', $user->id)
                ->whereBetween('examination_valid_until', [$now, $in30])
                ->get(),
            'vatrogasni' => Fire::where('user_id', $user->id)
                ->whereBetween('examination_valid_until', [$now, $in30])
                ->get(),
            'ostalo' => Miscellaneous::where('user_id', $user->id)
                ->whereBetween('examination_valid_until', [$now, $in30])
                ->get(),
        ];

        // Pošalji mail samo ako postoji nešto za prikaz
        if (collect($data)->flatten(1)->isNotEmpty()) {
            Mail::to($user->email)->send(new ExpiryNotificationMail($data));
        }
    }
}
