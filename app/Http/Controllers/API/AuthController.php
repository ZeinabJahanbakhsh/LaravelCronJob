<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Interfaces\UserRepositoryInterface;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;



class AuthController extends Controller
{

    protected $userRepositoryInterface;
    protected $userService;

    /**
     * @param UserRepositoryInterface $userRepositoryInterface
     */
    public function __construct(UserRepositoryInterface $userRepositoryInterface,
                                UserService $userService)
    {
        $this->userRepositoryInterface = $userRepositoryInterface;
        $this->userService = $userService;
    }


    /**
     * Register new user
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|object
     */
    public function register(Request $request)
    {

        //check validation $request
        $checkRegisterUserData = $this->userService->checkRegisterUserData($request);

        //$validator->fails()
        if($checkRegisterUserData->getStatusCode() != 201)
        {
            return $checkRegisterUserData;
        }

        //response json convert obj
        $objUserData      = json_decode(json_encode($checkRegisterUserData));
        $userDataResource = $objUserData->original->data;

        //Create access-token with passport
        //Error: cannot use object of type stdClass as array!
        //$getUserById = $this->userService->getUserById($userDataResource->id);
        //$userDataResource['token'] = $getUserById->createToken('user_register_token');


        return (new UserResource($userDataResource))
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

        $checkLoginUserData      = $this->userService->checkLoginUserData($request);
        $checkLoginUserDataArray = json_decode(json_encode($checkLoginUserData), true);

        //check login
        if (Auth::attempt($checkLoginUserDataArray['original']['data']))
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
            $this->userRepositoryInterface->logoutUser(Auth::user());

            return response([
                'success' => true,
                'msg'     => 'Successfully logged out',
            ], 200);

        }

        //Show this response, if we don't have middleware
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


    /**
     * Get name and create directory with it
     * @param $dirName
     * @return \Illuminate\Http\JsonResponse
     */
    public function createDirectory($dirName = null)
    {

        //$dirName = null => $dirName = $email
        if ($dirName)
        {
            $path = $dirName;
        }
        else
        {
            $path = $this->userRepositoryInterface->createUsernameWithEmail(auth()->user());
            $dirName = $path;
        }

        $checkDirectory = $this->userService->checkNameDirExists($dirName);
        if ($checkDirectory == false)
        {
            return response()->json([
                'success' => false,
                'message' => 'Duplicate data',
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
     * Get name and create file with it
     * @param $fileName
     * @return \Illuminate\Http\JsonResponse
     */
    public function createFile($fileName = null)
    {

        if ($fileName == null)
        {
            $fileName = $this->userRepositoryInterface->createUsernameWithEmail(auth()->user());;
        }

        $checkFile = $this->userService->checkFileNameExists($fileName.'.txt');
        if ($checkFile == false)
        {
            return response()->json([
                'success' => false,
                'message' => 'Duplicate file',
                'data'    => []
            ], 422);
        }
        else
        {
            $this->userService->createNewFile($fileName);

            return response()->json([
                'success' => true,
                'data'    => ['new file' => $fileName.'.txt']
            ], 200);
        }

    }



    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDirectoriesList()
    {

        $AllDirectories = $this->userService->getAllDirectories();

        return response()->json([
            'success' => true,
            'data'    => ['all directories' => $AllDirectories]
        ], 200);

    }


    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFilesList()
    {

        $AllFiles = $this->userService->getAllFiles();

        return response()->json([
            'success' => true,
            'data' => ['all files' => $AllFiles]
        ], 200);

    }


    protected $process ;
    protected $processes = array();

    public function getListRunningProcess()
    {

        exec("tasklist 2>NUL", $task_list);
        Storage::disk('local')->put( 'taskList.txt', $task_list);
        //return Storage::download('taskList.txt');

        die();

        $output = ob_get_clean();
        echo $output;
        //file_put_contents(time().'output.txt', $output);
        Storage::disk('local')->put( 'output.txt', $output);



        $file = fopen("taskList2.txt", "w");

        // 👇 write to the stream
        fwrite($file, "Somewhere over the rainbow");
        fwrite($file, "\n Skies are blue");

        // 👇 close the stream
        fclose($file);
        return true;


         /*if (empty(trim(shell_exec("pgrep $process")))) {
            return false;
        } else {
            return true;
        }*/


       /* require ( 'ProcessList.phpclass' ) ;
        $ps 	=  new ProcessList ( ) ;

        foreach  ( $ps  as  $process )
        {
            echo ( "PID : {$process -> ProcessId}, COMMAND : {$this -> Command}" ) ;
        }*/


        /*$execstring='ps -f -u www-data 2>&1';
        $output="";
        exec($execstring, $output);
        print_r($output);*/

    }


    public function getListProcess1()
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

}
