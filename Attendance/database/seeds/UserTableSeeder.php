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
       $role_student = Role::where("name", "student")->first();
       $role_tutor  = Role::where("name", "tutor")->first();

        $student = new User();
        $student->name = 'Student';
        $student->email = "student@example.com";
        $student->password = bcrypt("secret");
        $student->save();
        $student->roles()->attach($role_student);

        $tutor = new User();
        $tutor->name = 'Tutor';
        $tutor->email = 'tutor@example.com';
        $tutor->password = bcrypt("secret");
        $tutor->save();
        $tutor->roles()->attach($role_tutor);
    }
}
