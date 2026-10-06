<?php

namespace Database\Seeders;

use App\Models\Club;
use App\Models\ClubMember;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUsersSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('password');

        // 1. Superadmin (Kendali tertinggi)
        $superadmin = User::firstOrCreate(
            ['email' => 'superadmin@dcasttrade.com'],
            ['name' => 'Super Admin', 'password' => $password, 'platform_role' => 'superadmin', 'is_admin' => true]
        );

        // 2. Moderator Platform
        $modPlatform = User::firstOrCreate(
            ['email' => 'mod.budi@dcasttrade.com'],
            ['name' => 'Moderator Budi', 'password' => $password, 'platform_role' => 'moderator', 'is_admin' => false]
        );

        // 3. Founder Andi
        $founder = User::firstOrCreate(
            ['email' => 'andi@dcasttrade.com'],
            ['name' => 'Founder Andi', 'password' => $password, 'platform_role' => 'member', 'is_admin' => false]
        );

        // 4. Co-founder Sari
        $cofounder = User::firstOrCreate(
            ['email' => 'sari@dcasttrade.com'],
            ['name' => 'Cofound Sari', 'password' => $password, 'platform_role' => 'member', 'is_admin' => false]
        );

        // 5. Club Moderator Deni
        $modClub = User::firstOrCreate(
            ['email' => 'deni@dcasttrade.com'],
            ['name' => 'Mod Club Deni', 'password' => $password, 'platform_role' => 'member', 'is_admin' => false]
        );

        // 6. Member Tono
        $member = User::firstOrCreate(
            ['email' => 'tono@dcasttrade.com'],
            ['name' => 'Member Tono', 'password' => $password, 'platform_role' => 'member', 'is_admin' => false]
        );

        // Membuat Club "DCast Indonesia"
        $club = Club::firstOrCreate(
            ['slug' => 'dcast-indonesia'],
            [
                'name' => 'DCast Indonesia',
                'description' => 'Komunitas kolektor diecast terbesar di Indonesia. Tempat nongkrong, trade, dan sharing seputar hobi koleksi.',
                'rules' => "1. Dilarang spam.\n2. Hormati sesama kolektor.\n3. Dilarang scam (sanksi ban permanen).",
                'founder_id' => $founder->id,
            ]
        );

        // Memasukkan User ke dalam Club dengan role masing-masing
        $roles = [
            $founder->id => 'founder',
            $cofounder->id => 'co_founder',
            $modClub->id => 'club_moderator',
            $member->id => 'member',
        ];

        foreach ($roles as $userId => $role) {
            ClubMember::firstOrCreate(
                ['club_id' => $club->id, 'user_id' => $userId],
                ['club_role' => $role]
            );
        }
    }
}
