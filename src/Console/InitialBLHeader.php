<?php

namespace MrAlirezaEb\BugloosTest\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InitialBLHeader extends Command
{
    protected $signature = 'blheader:init';

    protected $description = 'drops all existing blheader schemas and insert new ones to the database';

    public function handle()
    {
        $this->info('Initializing BLHeaders...');

        $classPaths = glob(app_path().'/BLSchema/*.php');
        $classes = array();
        $namespace = 'App\BLSchema\\';
        foreach ($classPaths as $classPath) {
            $segments = explode('/', $classPath);
            $segments = explode('\\', $segments[count($segments) - 1]);
            $classes[] = str_replace('.php','',$namespace . $segments[count($segments) - 1]);
        }
        
        foreach($classes as $class)
        {
            $class::drop();
            $this->info($class." removed !");
            $class::create();
            $this->info($class." created !");
        }
        $this->info('Finished !');
    }
}