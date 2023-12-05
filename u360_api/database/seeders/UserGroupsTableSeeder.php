<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserGroupsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        DB::table('user_groups')->delete();

        DB::table('user_groups')->insert(array(
            0 =>
            array(
                'id' => 1,
                'group_name' => 'Super Admin',
                'description' => 'Super Admin',
                'year_group' => 10,
                'status' => 1,
                'permissions' => '{"user-group":["list","add","edit","delete"],"users":["list","add","edit","delete"],"skills":["list","add","edit","delete"],"pupils":["list","add","edit","delete"],"diploma-management":["list","add","edit","delete"],"tutors":["list","add","edit","delete"],"staff":["list","add","edit","delete"],"evidence":["list","add","edit","delete"],"strands":["list","add","edit","delete"],"diploma-viewer":["list","add","edit","delete"],"email-template":["list","add","edit","delete"]}',
                'created_at' => '2023-02-09 10:01:55',
                'updated_at' => '2023-02-09 10:01:55',
            ),
        ));
    }
}
