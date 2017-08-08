<?php

namespace App\Repositories;

use App\User;
use phpseclib\Crypt\Hash;

/**
 * Created by PhpStorm.
 * User: raghavendra
 * Date: 7/8/17
 * Time: 7:44 AM
 */
class UserRepository
{

    public function findByEmailOrCreate($userData)
    {

        $user = User::findByEmail($userData->email);

        if (empty($user)) {
            $data = [
                'name' => $userData->name,
                'email' => $userData->email,
                'password' => bcrypt('pass' . rand(10000, 99999))
            ];
            $user = User::create($data);
        }

        return $user;
    }

}