<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\Member;
use App\Models\EventItem;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ProductionDataSeeder extends Seeder
{
    public function run(): void
    {
        $superAdmin = User::updateOrCreate(
            ['email' => 'mustaphaadamu6773@gmail.com'],
            [
                'name' => 'Mustapha Adamu',
                'password' => Hash::make('TempPass123!'),
                'is_super_admin' => true,
                'email_verified_at' => now(),
            ]
        );

        $orgAdmin = User::updateOrCreate(
            ['email' => 'musteycs2@gmail.com'],
            [
                'name' => 'Mustapha Adamu',
                'password' => Hash::make('TempPass123!'),
                'is_super_admin' => false,
                'email_verified_at' => now(),
            ]
        );

        $memberUser = User::updateOrCreate(
            ['email' => 'mustaphaadamubabawuro@gmail.com'],
            [
                'name' => 'Mustapha Adamu',
                'password' => Hash::make('TempPass123!'),
                'is_super_admin' => false,
                'email_verified_at' => now(),
            ]
        );

        $organization = Organization::updateOrCreate(
            ['name' => 'Damaturu Central Mosque'],
            [
                'type' => 'mosque',
                'description' => "A mosque serving the Damaturu community with daily prayers, Friday Jumu'ah, and Islamic education programs.",
                'address' => 'Damaturu, Yobe State',
                'created_by' => $orgAdmin->id,
                'status' => 'approved',
            ]
        );

        Member::updateOrCreate(
            ['email' => 'musteycs2@gmail.com'],
            [
                'name' => 'Mustapha Adamu',
                'user_id' => $orgAdmin->id,
                'organization_id' => $organization->id,
                'role' => 'admin',
                'status' => 'approved',
                'join_date' => now(),
            ]
        );

        Member::updateOrCreate(
            ['email' => 'mustaphaadamubabawuro@gmail.com'],
            [
                'name' => 'Mustapha Adamu',
                'phone' => '09067735805',
                'user_id' => $memberUser->id,
                'organization_id' => $organization->id,
                'role' => 'member',
                'status' => 'approved',
                'join_date' => now(),
            ]
        );

        EventItem::updateOrCreate(
            ['title' => "Friday Jumu'ah Prayer", 'organization_id' => $organization->id],
            [
                'description' => 'Weekly congregational prayer for all members.',
                'event_date' => '2026-08-14',
                'event_time' => '13:20:00',
                'location' => 'Damaturu Central Mosque',
                'created_by' => $orgAdmin->id,
            ]
        );
    }
}
