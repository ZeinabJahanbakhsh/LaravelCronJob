<?php

namespace App\Interfaces;

interface UserRepositoryInterface
{

    public function createUser($data);

    public function getUserClient();

    public function getUserById($id);

    public function logoutUser($data);


}
