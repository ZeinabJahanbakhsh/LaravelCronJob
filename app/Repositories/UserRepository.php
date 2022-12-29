<?php

namespace App\Repositories;

use App\Interfaces\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Client as OClient;


class UserRepository implements UserRepositoryInterface
{

    /**
     * @param $data
     * @return mixed
     */
    public function createUser($data)
    {
        return User::create($data);
    }


    /**
     * @return mixed
     */
    public function getUserClient()
    {
        return OClient::where('password_client', 1)->first();
    }


    /**
     * @param $user
     * @return mixed
     */
    public function logoutUser($user)
    {
        return $user->token()->revoke();
    }


    /**
     * @param $data
     * @return false|string
     */
    public function createUsernameWithEmail($data)
    {
        return strstr($data->email, '@', true);
    }


    /**
     * @param $id
     * @return mixed
     */
    public function getUserById($id)
    {
        return User::find($id);
    }


}

