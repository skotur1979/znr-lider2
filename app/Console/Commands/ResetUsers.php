<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ResetUsers extends Command
{
    protected $signature = 'users:reset';
    protected $description = 'Briše sve korisnike i dodaje novog admina';

    public function handle()
    {
        // Briši sve korisnike
        User::truncate();
        $this->info('Svi korisnici su obrisani.');

        // Napravi novog korisnika
        $user = User::create([
            'name' => 'Sinisa Kotur',
            'email' => 'prvostupnik@gmail.com',
            'password' => Hash::make('NovaLozinka123'),
            'is_admin' => true,
        ]);

        $this->info('Novi korisnik je dodan:');
        $this->line("Email: {$user->email}");
        $this->line("Lozinka: NovaLozinka123");

        return 0;
    }
}