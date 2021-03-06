<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminsTableSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('admins')->insert([
           [
            'name' => 'Support',
            'email' => 'support@tresdear.com',
            'password' => bcrypt('password'),
            'types_id' => 1,
            'active' => 1,
            'remember_token' => NULL,
            'created_at' => '2019-12-04 18:02:57',
            'updated_at' => '2019-12-04 18:02:57'
           ]
       ]);
    }
}
