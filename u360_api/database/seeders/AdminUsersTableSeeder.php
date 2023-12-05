<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminUsersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        DB::table('admin_users')->delete();
        
        DB::table('admin_users')->insert(array (
            0 => 
            array (
                'id' => 1,
                'user_type' => '1',
                'name' => 'admin',
                'email' => 'admin@yopmail.com',
                'password' => '$2y$10$dTYEGhVIubbqbDoQlkwbK.hZWnXlrmvzmJbzpyjTzIEF5DiITpKe.',
                'user_group_id' => 1,
                'status' => '1',
                'image' => '1683881260.jpg',
                'reset_password_token' => NULL,
                'api_token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwczovL3VidW50dTM2MC5lcy9hcGkvYWRtaW4vbG9naW4iLCJpYXQiOjE2ODgwNDgzNTIsImV4cCI6MTY4ODA1MTk1MiwibmJmIjoxNjg4MDQ4MzUyLCJqdGkiOiIwZDNDT3M1aDhjbGdXSjhWIiwic3ViIjoiMSIsInBydiI6ImM4MjkyMjM4MzVkMTExMzhmMDhhY2U1NmZmYTY2MjhiYzI2ODNjYjUifQ.GhS6bn1MOCNn9bbsV1sVV1SfoF6d4HJEUCS5qEMTuYQ',
                'created_at' => '2023-02-09 10:01:55',
                'updated_at' => '2023-06-29 14:19:12',
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}