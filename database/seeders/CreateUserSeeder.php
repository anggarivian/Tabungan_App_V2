<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class CreateUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = [
            [
                'name'      => 'isKepsek',
                'username'  => 'isKepsek',
                'email'     => 'kepsek@mail.com',
                'password'  => bcrypt('12345'),
                'roles_id'  => 1
            ],
            [
                'name'      => 'isBendahara',
                'username'  => 'isBendahara',
                'email'     => 'bendahara@mail.com',
                'password'  => bcrypt('12345'),
                'roles_id'  => 2
            ]
            ,
            [
                'name'      => 'isWalikelas',
                'username'  => 'isWalikelas',
                'email'     => 'walikelas@mail.com',
                'password'  => bcrypt('12345'),
                'roles_id'  => 3
            ]
            ,
            [
                'name'      => 'isSiswa',
                'username'  => 'isSiswa',
                'email'     => 'siswa@mail.com',
                'password'  => bcrypt('12345'),
                'roles_id'  => 4
            ]
        ];

        foreach($user as $key => $value){
            User::create($value);
        }
    }
}
