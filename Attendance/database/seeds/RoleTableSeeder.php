<?php

use Illuminate\Database\Seeder;
use attendance\Role;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role_employee = new Role();
        $role_employee->name = "student";
        $role_employee->description = "A Student User";
        $role_employee->save();

        $role_manager = new Role();
        $role_manager->name = "tutor";
        $role_manager->description = "A Tutor User";
        $role_manager->save();

        $role_admin = new Role();
        $role_admin->name = 'admin';
        $role_admin->description = "A Admin User";
        $role_admin->save();
    }
}
