<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()   /* : void */  
    {



        //Check if superadmin already exists
        $superadmin = User::where('role', 'superadmin')->first();

        if (!$superadmin){
            //create the superadmin
            User::create([
                'name' => 'Hmedix Superadmin',
                'email' => 'superadmin@gmail.com',
                'password' => bcrypt('123456789'),
                'is_approved' => 1,
                'role' => 'superadmin',
            ]);
        } else {
            // If the superadmin exist
            $superadmin->is_approved = 1;
            $superadmin->save();
        }

        /*
        $userObj = new User();
        $userObj->name = 'User Hmedix';
        $userObj->email = 'userHmedix@gmail.com';
        $userObj->password = Hash::make('123456789');
        $userObj->role = 'customercare';
        $adminObj->is_approved = true;
        $userObj->save();

        $adminObj = new User();
        $adminObj->name = 'Admin Hmedix';
        $adminObj->email = 'adminHmedix@gmail.com';
        $adminObj->password = Hash::make('123456789');
        $adminObj->role = 'admin';
        $adminObj->is_approved = true;
        $adminObj->save();

        $superAdminObj = new User();
        $superAdminObj->name = 'Super Admin Hmedix';
        $superAdminObj->email = 'superAdminHmedix@gmail.com';
        $superAdminObj->password = Hash::make('123456789');
        $superAdminObj->role = 'superadmin';
        $adminObj->is_approved = true;
        $superAdminObj->save();

        */

    }
}
