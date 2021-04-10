<?php

namespace MrAlirezaEb\BugloosTest\Console;

use Illuminate\Console\GeneratorCommand;

class MakeBLHeaderCommand extends GeneratorCommand
{
    protected $name = 'make:blheader';

    protected $description = 'Create a new a class to build BLHeaderSchema';

    protected $type = 'CreateBLHeader';

    protected function getStub()
    {
        return __DIR__ . '/stubs/header.php.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\BLSchema';
    }

    public function handle()
    {
        parent::handle();

        $this->doOtherOperations();
    }

    protected function doOtherOperations()
    {
        // Get the fully qualified class name (FQN)
        $class = $this->qualifyClass($this->getNameInput());

        // get the destination path, based on the default namespace
        $path = $this->getPath($class);

        $content = file_get_contents($path);

        // Update the file content with additional data (regular expressions)

        file_put_contents($path, $content);
    }
}