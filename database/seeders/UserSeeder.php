<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::firstOrCreate(
    		['email' => 'madmor@gmail.com'],
        	[
        		'name' 	=> 'madmor', 
        		'password' => Hash::make("password")
        	]
        );
        
        $user = User::firstOrCreate(
    		['email' => 'bone@gmail.com'],
        	[
        		'name' 	=> 'bone', 
        		'password' => Hash::make("password")
        	]
        );
        
        $alluser = User::get()->count();
        if($alluser <= 1){
            User::factory()->count(10)->create();
        }
    }
}
