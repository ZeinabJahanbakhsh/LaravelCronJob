<?php

namespace App\Traits;
use Symfony\Component\Process\Process;

trait Processable{

    /**
     * Run Server SH Process
     * @param string $command
     * @param string $cwd
     * @return mixed
     */
    private function runProcess($command = 'ls', $cwd = null){
        $processOutput = collect();
        $process = new Process($command);
        $process->setWorkingDirectory(($cwd ? $cwd : base_path()));
        $process->setIdleTimeout(10);
        $process->setTimeout(15);
        try{
            $process->run(function ($type, $buffer) use (&$processOutput, &$process) {
                $processOutput->push($buffer);
                if(Process::ERR === $type) {
                    //Handle Error
                    throw new \Exception('Whoopsie!');
                }
            });
            //$process->getPid(); //Get PID if needed
        }catch(\Exception $e){
            //Your Alert Method
        }
        return $processOutput;
    }
}
