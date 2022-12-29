<?php

namespace App\Services;

use App\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;


class UserService
{

    /**
     * @var UserRepositoryInterface
     */
    protected $userRepositoryInterface;


    /**
     * @param UserRepositoryInterface $userRepositoryInterface
     */
    public function __construct(UserRepositoryInterface $userRepositoryInterface)
    {
        $this->userRepositoryInterface = $userRepositoryInterface;
    }


    /**
     * @param $data
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \Throwable
     */
    public function checkRegisterUserData($data)
    {

        //check validation
        $validator = validator::make($data->all(),[
            'name'       => 'required|string|max:255',
            'email'      => 'required|string|email|max:255|unique:users',
            'password'   => 'required|string|min:8',
            'c_password' => 'required|same:password',
        ]);
        if ($validator->fails())
        {
            return response([
                'data'    =>[],
                'success' => false,
                'error'   => $validator->errors()
            ], 401);
        }

        //create data[]
        $inputData = [
            'name'     => $data->name,
            'email'    => $data->email,
            'password' => bcrypt($data->password)
        ];

        //insert db
        DB::beginTransaction();
        try
        {
            $result = $this->userRepositoryInterface->createUser($inputData);
            DB::commit();
        }
        //Exception
        catch (\Throwable $e){
            DB::rollback();
            throw $e;
        }

        return response([
            'data'    => $result,
            'success' => true,
        ], 201);

    }


    /**
     * @param $data
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function checkLoginUserData($data)
    {

        //check validation
        $validator     =   validator::make($data->all(),[
            'email'    => 'required|email',
            'password' => 'required',
        ]);
        if ($validator->fails())
        {
            return response([
                'data'    =>[],
                'success' => false,
                'error'   => $validator->errors()
            ], 401);
        }

        $userData = [
            'email'    => $data->email,
            'password' => $data->password
        ];

        return response([
            'data'    => $userData,
            'success' => true,
        ], 200);

    }


    /**
     * @param $data
     * @return mixed
     */
    public function store($data)
    {
        return $this->userRepositoryInterface->createUser($data);
    }


    /**
     * @return mixed
     */
    public function userClient()
    {
        return $this->userRepositoryInterface->getUserClient();
    }


    /**
     * @param $data
     * @return false|string
     */
    public function createUsernameWithEmail($data)
    {
        return $this->userRepositoryInterface->createUsernameWithEmail($data);
    }


    /**
     * @param $name
     * @return bool
     */
    public function checkFileNameExists($name)
    {

        if (Storage::disk('public_uploads_files')->exists($name))
        {
            return false;
        }
        else
        {
            return true;
        }

    }


    /**
     * @param $name
     * @return bool
     */
    public function checkNameDirExists($name)
    {

        if (Storage::disk('public_uploads_dir')->exists($name))
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
        return  Storage::disk('public_uploads_dir')->makeDirectory($path);
    }


    /**
     * @return array
     */
    public function getAllDirectories()
    {
        return Storage::disk('public_uploads_dir')->allDirectories();
    }


    /**
     * @return array
     */
    public function getAllFiles()
    {
        return Storage::disk('public_uploads_files')->files();
    }


    public function createNewFile($name)
    {
        return Storage::disk('public_uploads_files')->put( $name.'.txt', 'This is sample content');
    }


    public function getUserById($id)
    {
        return $this->userRepositoryInterface->getUserById($id);
    }



}
