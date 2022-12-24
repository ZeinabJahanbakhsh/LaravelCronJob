<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Laravel\Passport\Client as OClient;

class UserService
{

    /**
     * @var User
     */
    protected $user;

    /**
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }


    /**
     * @param $data
     * @return mixed
     */
    public function store($data)
    {
        return $this->user->create($data);
    }


    /**
     * @return mixed
     */
    public function userClient()
    {
        return OClient::where('password_client', 1)->first();
    }

    /**
     * @param $dirName
     * @return bool
     */
    public function checkDirectoryExists($dirName)
    {

        if (Storage::disk('public')->exists($dirName))
        {
            return false;
        }
        else
        {
            return true;
        }

    }

    /**
     * @param $path
     * @return bool
     */
    public function makeDirectory($path)
    {
        return  Storage::disk('public')->makeDirectory($path);
    }



}
