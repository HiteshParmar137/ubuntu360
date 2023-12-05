<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SdgsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        DB::table('sdgs')->delete();

        DB::table('sdgs')->insert(array(
            0 =>
            array(
                'id' => 1,
                'name' => 'No Poverty',
                'icon' => 'no_poverty.png',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            1 =>
            array(
                'id' => 2,
                'name' => 'Zero Hunger',
                'icon' => 'zero_hunger.png',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            2 =>
            array(
                'id' => 3,
                'name' => 'Good Health and Well-being',
                'icon' => 'good_health_and_well-being.png',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            3 =>
            array(
                'id' => 4,
                'name' => 'Quality Education',
                'icon' => 'quality_education.png',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            4 =>
            array(
                'id' => 5,
                'name' => 'Gender Equality',
                'icon' => 'gender_equality.png',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            5 =>
            array(
                'id' => 6,
                'name' => 'Clean Water and Sanitation',
                'icon' => 'clean_water_and_sanitation.png',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            6 =>
            array(
                'id' => 7,
                'name' => 'Affordable and Clean Energy',
                'icon' => 'affordable_and_clean_energy.png',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            7 =>
            array(
                'id' => 8,
                'name' => 'Decent Work and Economic Growth',
                'icon' => 'decent_work_and_economic_growth.png',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            8 =>
            array(
                'id' => 9,
                'name' => 'Industry, Innovation and Infrastructure',
                'icon' => 'industry_innovation_and_infrastructure.png',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            9 =>
            array(
                'id' => 10,
                'name' => 'Reduced Inequalities',
                'icon' => 'reduced_inequalities.png',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            10 =>
            array(
                'id' => 11,
                'name' => 'Sustainable Cities and Communities',
                'icon' => 'sustainable_cities_and_communities.png',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            11 =>
            array(
                'id' => 12,
                'name' => 'Climate Action',
                'icon' => 'climate_action.png',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            12 =>
            array(
                'id' => 13,
                'name' => 'Life Below Water',
                'icon' => 'life_below_water.png',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            13 =>
            array(
                'id' => 14,
                'name' => 'Life and Land',
                'icon' => 'life_and_land.png',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            14 =>
            array(
                'id' => 15,
                'name' => 'Peace, Justice and Strong Institutions',
                'icon' => 'peace_justice_and_strong_institutions.png',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            15 =>
            array(
                'id' => 16,
                'name' => 'Partnerships for the Goals',
                'icon' => 'partnerships_for_the_goals.png',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
        ));
    }
}
