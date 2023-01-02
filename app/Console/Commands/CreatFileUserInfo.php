<?php

namespace App\Console\Commands;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CreatFileUserInfo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CreatFile:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create file to put user info';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        Log::info("Starting ....");

        $dateTime    = date('Y-m-d');
        $currentDate = Carbon::createFromFormat('Y-m-d', $dateTime)->format('m-d-Y');
        $content     = User::where('email', 'jahanbakhsh.zeinab@gmail.com')->first();

        //$currentDate.zip
        Storage::disk('cronJobInfoUser')->put( $currentDate.'.txt', $content);

        Log::info("Ended create file!");

    }
}
