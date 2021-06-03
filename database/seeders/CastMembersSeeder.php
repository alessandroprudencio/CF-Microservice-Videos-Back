<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CastMember;

class CastMembersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CastMember::factory()->count(100)->create();
    }
}
