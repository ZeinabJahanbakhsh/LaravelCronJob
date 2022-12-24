<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;



class AuthController extends Controller
{

    protected $userService;

    /**
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }


    /**
     * Register new user
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|object
     */
    public function register(Request $request)
    {

        $validator = validator::make($request->all(),[
            'name'       => 'required|string|max:255',
            'email'      => 'required|string|email|max:255|unique:users',
            'password'   => 'required|string|min:8',
            'c_password' => 'required|same:password',
        ]);
        if ($validator->fails())
        {
            return response([
                'success' => false,
                'error'   => $validator->errors()
            ], 401);
        }

        $inputData = [
            'name'       => $request->name,
            'email'      => $request->email,
            'password'   => bcrypt($request->password)
        ];

        //Insert db
        $storeData = $this->userService->store($inputData);

        //Create access-token with passport
        $storeData['token'] = $storeData->createToken('user_register_token');


        return (new UserResource($storeData))
            ->additional(['success' => true])
            ->response()
            ->setStatusCode(201);

    }


    /**
     * Login user and return token
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function login(Request $request)
    {

        /*
         * TODO: in our pdf we have "User: liateam" not email!
         *  I can replace "name" and name should be unique because of login and authentication
         */
        $userData = [
            //'user'     => $request->name,
            'email'    => $request->email,
            'password' => $request->password
        ];

        $validator = validator::make($userData,[
            'email'      => 'required|email',
            'password'   => 'required',
        ]);
        if ($validator->fails())
        {
            return response([
                'data'    =>[],
                'success' => false,
                'error'   => $validator->errors()
            ], 401);
        }

        if (Auth::attempt($userData))
        {
            $token = auth()->user()->createToken('user_login_token')->accessToken;

            return response([
                'data' => ['token' => $token],
                'success' => true
            ], 200);
        }
        else
        {
            return response([
                'data'    => [],
                'success' => false,
            ], 401);
        }

    }


    /**
     * Get token and Logout user with revoke token
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function logout()
    {

        if (Auth::check())
        {
            Auth::user()->token()->revoke();

            return response([
                'success' => true,
                'msg'     => 'Successfully logged out',
            ], 200);

        }

        //If we don't have middleware, show this response
        return response([
            'success' => false,
            'msg'     => ['error' => "Access Denied!"],
        ], 403);

    }


    /**
     * Get token and return user's detail
     * @return \Illuminate\Http\JsonResponse
     */
    public function userDetail()
    {
        //dd( \request()->bearerToken());

        return response()->json([
            'success' => true,
            'data'    => auth()->user()
        ], 200);

    }


    public function getListProcess()
    {
        //It's hard :|

        //'ps' is not recognized as an internal or external command
        /*$execstring='ps -f -u www-data 2>&1';
        $output="";
        exec($execstring, $output);
        print_r($output);*/


        /*$process = new Process(['ls', '-lsa']);
        $process->start();
        $iterator = $process->getIterator($process::ITER_SKIP_ERR | $process::ITER_KEEP_OUTPUT);
        dd($iterator);
        foreach ($iterator as $data) {
            echo $data."\n";
        }*/


        //'ps' is not recognized as an internal or external command,
        /*$process = new Process(['ps', '-aux']);
        $process->run();
        // executes after the command finishes
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
        echo $process->getOutput();*/


        //'ps' is not recognized as an internal or external command,
        /*$execstring='ps -f -u www-data 2>&1';
        $output="";
        exec($execstring, $output);
        print_r($output);*/

        //exec("tasklist 2>NUL", $task_list);
        //exec('netstat -pnltu | grep -i "6001" && exit 0 || exit 1', $task_list);
        //echo "<pre>"; print_r($task_list);


        /*Artisan::call();
        return getmypid();*/


        /*        $thisfilepath = $_SERVER['SCRIPT_FILENAME'];
        $thisfilepath = fopen($thisfilepath,'r');
        if (!flock($thisfilepath,LOCK_EX | LOCK_NB))
        {
            customlogfunctionandemail("File is Locked");
            exit();
        }
        elseif(flock($thisfilepath,LOCK_EX | LOCK_NB)) // Acquire Lock
        {
            // Write your code


            // Unlock before finish
            flock($thisfilepath,LOCK_UN);  // Unlock the file and Exit
            customlogfunctionandemail("Process completed. File is unlocked");
            exit();
        }*/


        /*$process = new Process(['ps', '-aux']);
        $process->run();
        //-cwd: "F:\xampp\htdocs\liateam\public"
        //dd($process);

        //dd($process->getOutput());

        // executes after the command finishes
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
        echo $process->getOutput();*/

    }


    /**
     * Get directory name and create directory
     * @param $dirName
     * @return \Illuminate\Http\JsonResponse
     */
    public function createDirectory($dirName)
    {
        //main path: storage/app/public
        /**
         * also we can do this(opt/myprogram/$username/" directory.)
         * $username  = strstr(auth()->user()->email, '@', true);
         * $path      = '/public/'. $username;
         */

        $path = $dirName;
        $checkDirectory = $this->userService->checkDirectoryExists($dirName);
        if ($checkDirectory == false)
        {
            return response()->json([
                'success' => false,
                'data'    => []
            ], 422);
        }
        else
        {
           $this->userService->makeDirectory($path);

            return response()->json([
                'success' => true,
                'data'    => ['new directory' => $path]
            ], 200);
        }

    }


    /**
     * Get file name and create file
     * @param $fileName
     * @return \Illuminate\Http\JsonResponse
     */
    public function createFile($fileName/*, Request $request,$fileContent*/)
    {
        //main path: storage/app/public

        //TODO: what kind of validator and extensions?
       /* $validator = validator::make($request->all(),[
            'fileName'    => 'required|mimes:png,txt,pdf|max:2048',
            'fileContent' => 'sometimes|string|max:255',
        ]);
        if ($validator->fails())
        {
            return response([
                'success' => false,
                'error'   => $validator->errors()
            ], 401);
        }*/

        $path = $fileName;
        $checkDirectory = $this->userService->checkDirectoryExists($fileName);

        //Storage::disk('public')->put('/article/' . 'sample.pdf','this is test content into the file');
        //dd(Storage::disk('public')->put($fileName, $fileContent));


        if ($checkDirectory == false)
        {
            return response()->json([
                'success' => false,
                'data'    => []
            ], 422);
        }
        else
        {
            //$this->userService->makeDirectory($path);
            Storage::disk('public')->put($fileName, 'This is sample content');

            return response()->json([
                'success' => true,
                'data'    => ['new file' => $path]
            ], 200);
        }

    }


    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDirectoriesList()
    {
        return response()->json([
            'success' => true,
            'data' => ['all directories' => Storage::disk('public')->allDirectories()
            ]
        ], 200);
    }


    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFilesList()
    {
        return response()->json([
            'success' => true,
            'data' => ['all files' => Storage::disk('public')->files()
            ]
        ], 200);
    }


}
