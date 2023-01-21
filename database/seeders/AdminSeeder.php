<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = new Admin();
        $admin->name = "Admin";
        $admin->email = "admin@email.com";
        $admin->mobile = "1234567890";
        $admin->password = bcrypt("123456");
        $admin->save();
    }
}
