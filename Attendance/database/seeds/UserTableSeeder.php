<?php

use Illuminate\Database\Seeder;
use attendance\Role;
use attendance\User;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       $role_admin = Role::where("name","admin")->first();

        //Create a admin profile
        $admin = new User();
        $admin->name = "Admin";
        $admin->email = "admin@example.com";
        $admin->password = bcrypt("admin");
        $admin->save();
        $admin->roles()->attach($role_admin);

    }
}
