<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\ExpiryNotificationMail;


class SendExpiryNotifications extends Command
{
    protected $signature = 'notify:expirations';
    protected $description = 'Šalje svakom korisniku obavijest o isteku roka za sve povezane resurse';

    public function handle()
    {
        $users = User::all();

        foreach ($users as $user) {
            $employees = $user->employees()
                ->whereBetween('medical_examination_valid_until', [now(), now()->addDays(30)])
                ->get();

            $machines = $user->machines()
                ->whereBetween('examination_valid_until', [now(), now()->addDays(30)])
                ->get();

            $fires = $user->fires()
                ->whereBetween('examination_valid_until', [now(), now()->addDays(30)])
                ->get();

            $misc = $user->miscellaneouses()
                ->whereBetween('examination_valid_until', [now(), now()->addDays(30)])
                ->get();

            if ($employees->isNotEmpty() || $machines->isNotEmpty() || $fires->isNotEmpty() || $misc->isNotEmpty()) {
               Mail::to($user->email)->send(new ExpiryNotificationMail([
    'zaposlenici' => $employees,
    'strojevi' => $machines,
    'vatrogasni' => $fires,
    'ostalo' => $misc,

]));
                $this->info("✅ Mail poslan korisniku: {$user->email}");
            } else {
                $this->info("ℹ️  Nema podataka za korisnika: {$user->email}");
            }
        }

        return Command::SUCCESS;
    }
}