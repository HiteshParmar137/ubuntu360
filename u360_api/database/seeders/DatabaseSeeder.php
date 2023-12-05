<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(CountriesTableSeeder::class);
        $this->call(AdminModulesTableSeeder::class);
        $this->call(CategoriesTableSeeder::class);
        $this->call(IndustriesTableSeeder::class);
        $this->call(SdgsTableSeeder::class);
        $this->call(SiteSettingsTableSeeder::class);
        $this->call(TemplatesManagementTableSeeder::class);
        $this->call(UserGroupsTableSeeder::class);
        $this->call(AdminUsersTableSeeder::class);
    }
}
