<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IndustriesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        DB::table('industries')->delete();

        DB::table('industries')->insert(array(
            0 =>
            array(
                'id' => 1,
                'name' => 'Accounting',
                'created_at' => '2023-07-03 19:59:24',
            ),
            1 =>
            array(
                'id' => 2,
                'name' => 'Administration & Office Support',
                'created_at' => '2023-07-03 19:59:24',
            ),
            2 =>
            array(
                'id' => 3,
                'name' => 'Advertising, Arts & Media',
                'created_at' => '2023-07-03 19:59:24',
            ),
            3 =>
            array(
                'id' => 4,
                'name' => 'Banking & Financial Services',
                'created_at' => '2023-07-03 19:59:24',
            ),
            4 =>
            array(
                'id' => 5,
                'name' => 'Call Centre & Customer Service',
                'created_at' => '2023-07-03 19:59:24',
            ),
            5 =>
            array(
                'id' => 6,
                'name' => 'Community Services & Development',
                'created_at' => '2023-07-03 19:59:24',
            ),
            6 =>
            array(
                'id' => 7,
                'name' => 'Construction',
                'created_at' => '2023-07-03 19:59:24',
            ),
            7 =>
            array(
                'id' => 8,
                'name' => 'Consulting & Strategy',
                'created_at' => '2023-07-03 19:59:24',
            ),
            8 =>
            array(
                'id' => 9,
                'name' => 'Design & Architechture',
                'created_at' => '2023-07-03 19:59:24',
            ),
            9 =>
            array(
                'id' => 10,
                'name' => 'Education & Training',
                'created_at' => '2023-07-03 19:59:24',
            ),
            10 =>
            array(
                'id' => 11,
                'name' => 'Engineering',
                'created_at' => '2023-07-03 19:59:24',
            ),
            11 =>
            array(
                'id' => 12,
                'name' => 'Farming, Animals & Conservation',
                'created_at' => '2023-07-03 19:59:24',
            ),
            12 =>
            array(
                'id' => 13,
                'name' => 'Government & Defence',
                'created_at' => '2023-07-03 19:59:24',
            ),
            13 =>
            array(
                'id' => 14,
                'name' => 'Healthcare & Medical',
                'created_at' => '2023-07-03 19:59:24',
            ),
            14 =>
            array(
                'id' => 15,
                'name' => 'Hospitality & Tourism',
                'created_at' => '2023-07-03 19:59:24',
            ),
            15 =>
            array(
                'id' => 16,
                'name' => 'Human Resources & Recruitment',
                'created_at' => '2023-07-03 19:59:24',
            ),
            16 =>
            array(
                'id' => 17,
                'name' => 'Information & Communication Technology',
                'created_at' => '2023-07-03 19:59:24',
            ),
            17 =>
            array(
                'id' => 18,
                'name' => 'Insurance & Superannuation',
                'created_at' => '2023-07-03 19:59:24',
            ),
            18 =>
            array(
                'id' => 19,
                'name' => 'Legal',
                'created_at' => '2023-07-03 19:59:24',
            ),
            19 =>
            array(
                'id' => 20,
                'name' => 'Manufacturing, Transport & Logistics',
                'created_at' => '2023-07-03 19:59:24',
            ),
            20 =>
            array(
                'id' => 21,
                'name' => 'Marketing & Communications',
                'created_at' => '2023-07-03 19:59:24',
            ),
            21 =>
            array(
                'id' => 22,
                'name' => 'Mining, Resources & Energy',
                'created_at' => '2023-07-03 19:59:24',
            ),
            22 =>
            array(
                'id' => 23,
                'name' => 'Real Estate & Property',
                'created_at' => '2023-07-03 19:59:24',
            ),
            23 =>
            array(
                'id' => 24,
                'name' => 'Retail & Consumer Product',
                'created_at' => '2023-07-03 19:59:24',
            ),
            24 =>
            array(
                'id' => 25,
                'name' => 'Sales',
                'created_at' => '2023-07-03 19:59:24',
            ),
            25 =>
            array(
                'id' => 26,
                'name' => 'Science & Technology',
                'created_at' => '2023-07-03 19:59:24',
            ),
            26 =>
            array(
                'id' => 27,
                'name' => 'Sport & Recreation',
                'created_at' => '2023-07-03 19:59:24',
            ),
            27 =>
            array(
                'id' => 28,
                'name' => 'Trades & Services',
                'created_at' => '2023-07-03 19:59:24',
            ),
        ));
    }
}
