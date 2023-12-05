<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminModulesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        DB::table('admin_modules')->delete();

        DB::table('admin_modules')->insert(array(
            0 =>
            array(
                'id' => 1,
                'parent_id' => NULL,
                'module_name' => 'Dashboard',
                'slug' => 'dashboard',
                'action' => '["list","add","edit","delete"]',
                'sort_order' => 1,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2023-02-09 10:01:55',
                'updated_at' => '2023-02-09 10:01:55',
            ),
            1 =>
            array(
                'id' => 2,
                'parent_id' => NULL,
                'module_name' => 'User Management',
                'slug' => 'user-management',
                'action' => '',
                'sort_order' => 3,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2023-02-09 10:01:55',
                'updated_at' => '2023-02-09 10:01:55',
            ),
            2 =>
            array(
                'id' => 3,
                'parent_id' => 17,
                'module_name' => 'Admin Users',
                'slug' => 'admin-users',
                'action' => '["list","add","edit","delete"]',
                'sort_order' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2023-02-09 10:01:55',
                'updated_at' => '2023-02-09 10:01:55',
            ),
            3 =>
            array(
                'id' => 4,
                'parent_id' => 17,
                'module_name' => 'User Groups',
                'slug' => 'user-groups',
                'action' => '["list","add","edit","delete"]',
                'sort_order' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2023-02-09 10:01:55',
                'updated_at' => '2023-02-09 10:01:55',
            ),
            4 =>
            array(
                'id' => 5,
                'parent_id' => NULL,
                'module_name' => 'Projects',
                'slug' => 'project',
                'action' => '["list","add","edit","delete"]',
                'sort_order' => 8,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2023-02-09 10:01:55',
                'updated_at' => '2023-02-09 10:01:55',
            ),
            5 =>
            array(
                'id' => 6,
                'parent_id' => NULL,
                'module_name' => 'Template Management',
                'slug' => 'templates-management',
                'action' => '["list","add","edit","delete"]',
                'sort_order' => 9,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2023-02-09 10:01:55',
                'updated_at' => '2023-02-09 10:01:55',
            ),
            6 =>
            array(
                'id' => 7,
                'parent_id' => NULL,
                'module_name' => 'CMS Management',
                'slug' => 'cms-management',
                'action' => '["list","add","edit","delete"]',
                'sort_order' => 10,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2023-02-09 10:01:55',
                'updated_at' => '2023-02-09 10:01:55',
            ),
            7 =>
            array(
                'id' => 11,
                'parent_id' => 2,
                'module_name' => 'Corporate Users',
                'slug' => 'corporate-users',
                'action' => '["list","add","edit","delete"]',
                'sort_order' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2022-12-19 10:28:49',
                'updated_at' => '2022-12-19 10:28:49',
            ),
            8 =>
            array(
                'id' => 12,
                'parent_id' => NULL,
                'module_name' => 'Email Subscription',
                'slug' => 'subscription',
                'action' => '["list","add","edit","delete"]',
                'sort_order' => 12,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2022-12-19 10:28:49',
                'updated_at' => '2022-12-19 10:28:49',
            ),
            9 =>
            array(
                'id' => 13,
                'parent_id' => NULL,
                'module_name' => 'ESG Reports',
                'slug' => 'esg-report',
                'action' => '["list","add","edit","delete"]',
                'sort_order' => 11,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2022-12-19 10:28:49',
                'updated_at' => '2022-12-19 10:28:49',
            ),
            10 =>
            array(
                'id' => 14,
                'parent_id' => NULL,
                'module_name' => 'Project Report',
                'slug' => 'project-report',
                'action' => '["list","add","edit","delete"]',
                'sort_order' => 13,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            11 =>
            array(
                'id' => 15,
                'parent_id' => NULL,
                'module_name' => 'User Report',
                'slug' => 'user-report',
                'action' => '["list","add","edit","delete"]',
                'sort_order' => 14,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            12 =>
            array(
                'id' => 16,
                'parent_id' => 2,
                'module_name' => 'Individual Users',
                'slug' => 'users',
                'action' => '["list","add","edit","delete"]',
                'sort_order' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2023-05-30 10:39:23',
                'updated_at' => NULL,
            ),
            13 =>
            array(
                'id' => 17,
                'parent_id' => NULL,
                'module_name' => 'Admin Management',
                'slug' => 'admin-management',
                'action' => '',
                'sort_order' => 2,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2023-02-09 10:01:55',
                'updated_at' => '2023-02-09 10:01:55',
            ),
            14 =>
            array(
                'id' => 18,
                'parent_id' => NULL,
                'module_name' => 'Site Settings',
                'slug' => 'site-settings',
                'action' => '["list","add","edit","delete"]',
                'sort_order' => 15,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
        ));
    }
}
