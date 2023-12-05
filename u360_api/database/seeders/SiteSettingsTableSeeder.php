<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SiteSettingsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        DB::table('site_settings')->delete();

        DB::table('site_settings')->insert(array(
            0 =>
            array(
                'id' => 1,
                'key_name' => 'phone_no',
                'key_value' => '+1-234-567 8900',
                'created_by' => 1,
                'updated_by' => 11,
                'created_at' => '2023-01-06 09:11:27',
                'updated_at' => '2023-06-27 06:05:07',
            ),
            1 =>
            array(
                'id' => 2,
                'key_name' => 'email',
                'key_value' => 'info@mail.com',
                'created_by' => 1,
                'updated_by' => 11,
                'created_at' => '2023-01-06 09:11:27',
                'updated_at' => '2023-06-27 06:05:07',
            ),
            2 =>
            array(
                'id' => 3,
                'key_name' => 'whatsapp_no',
                'key_value' => '123 456 7890',
                'created_by' => 1,
                'updated_by' => 11,
                'created_at' => '2023-01-06 09:11:27',
                'updated_at' => '2023-06-27 06:05:07',
            ),
            3 =>
            array(
                'id' => 4,
                'key_name' => 'twitter',
                'key_value' => 'https://www.twiteer.com',
                'created_by' => 11,
                'updated_by' => 11,
                'created_at' => NULL,
                'updated_at' => '2023-06-27 06:28:36',
            ),
            4 =>
            array(
                'id' => 5,
                'key_name' => 'instagram',
                'key_value' => 'https://www.instagram.com',
                'created_by' => 11,
                'updated_by' => 11,
                'created_at' => NULL,
                'updated_at' => '2023-06-27 06:28:36',
            ),
            5 =>
            array(
                'id' => 6,
                'key_name' => 'facebook',
                'key_value' => 'https://www.facebook.com',
                'created_by' => 11,
                'updated_by' => 11,
                'created_at' => NULL,
                'updated_at' => '2023-06-27 06:28:36',
            ),
            6 =>
            array(
                'id' => 7,
                'key_name' => 'youtube',
                'key_value' => 'https://www.youtub.com',
                'created_by' => 11,
                'updated_by' => 11,
                'created_at' => NULL,
                'updated_at' => '2023-06-27 06:28:36',
            ),
            7 =>
            array(
                'id' => 8,
                'key_name' => 'visit_us',
                'key_value' => NULL,
                'created_by' => 11,
                'updated_by' => 1,
                'created_at' => NULL,
                'updated_at' => '2023-06-27 06:29:41',
            ),
        ));
    }
}
