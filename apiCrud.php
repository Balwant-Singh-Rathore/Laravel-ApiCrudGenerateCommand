<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;

class apiCrud extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'generate:apiCrud';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Api Crud';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Crud';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub($type = null)
    {
        if ($type == 'contract') {
            return  app_path() . '/Console/Commands/stubs/ServiceContractStub.stub';
        } else if ($type == 'service') {
            return  app_path() . '/Console/Commands/stubs/ServiceStub.stub';
        } else if ($type == 'BaseContract') {
            return  app_path() . '/Console/Commands/stubs/BaseContractStub.stub';
        } else if ($type == 'BaseService') {
            return  app_path() . '/Console/Commands/stubs/BaseServiceStub.stub';
        }
    }

    /**
     * Create view directory if not exists.
     *
     * @param $path
     */
    public function createDir($path)
    {
        $dir = dirname($path);

        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function contractPath($path = '')
    {
        $contract = str_replace('.', '/', $path . 'Contract') . '.php';
        $path = "Contracts/{$contract}";
        return $path;
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function servicePath($path = '')
    {
        $contract = str_replace('.', '/', $path . 'Service') . '.php';
        $path = "Services/{$contract}";
        return $path;
    }
    /**
     * Build the class with the given name.
     *
     * Remove the base controller import if we are already in the base namespace.
     *
     * @param  string  $name
     * @return string
     */
    protected function makeClass($name, $type = null)
    {

        if ($type == 'contract') {

            $this->createBaseContractIfNotExist();

            $path = $this->contractPath($name);

            $this->createDir($path);

            if ($this->files->exists($path)) {
                $this->error("File {$path} already exists!");
                return;
            }
            $this->files->put($path, $this->sortImports($this->buildClass($name, $type)));
        }
        if ($type == 'service') {

            $this->createBaseServiceIfNotExist();

            $path = $this->servicePath($name);

            $this->createDir($path);

            if ($this->files->exists($path)) {
                $this->error("File {$path} already exists!");
                return;
            }
            $this->files->put($path, $this->sortImports($this->buildClass($name, $type)));
        }
    }


    /**
     * make BaseContract If Not Exist
     */
    public function  createBaseContractIfNotExist()
    {
        $path = "Contracts/BaseContract.php";

        $this->createDir($path);

        $this->files->put($path, $this->sortImports($this->buildClass('BaseContract', 'BaseContract')));
    }


    /**
     * make BaseContract If Not Exist
     */
    public function  createBaseServiceIfNotExist()
    {
        $path = "Services/BaseService.php";

        $this->createDir($path);

        $this->files->put($path, $this->sortImports($this->buildClass('BaseService', 'BaseService')));
    }


    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function buildClass($name, $type = null)
    {
        $stub = $this->files->get($this->getStub($type));

        return $this->replaceNamespace($stub, $name)->replaceClass($stub, $name);
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the view.'],
        ];
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $this->call('make:provider', ['name' => 'BindingServiceProvider']);

        $name = $this->argument('name');

        $controllerName = Str::ucfirst(Str::camel($name . 'Controller'));

        $this->call('make:controller', ['name' => $controllerName, '--api' => true]);

        $requestName = Str::ucfirst(Str::camel($name . 'Request'));

        $this->call('make:request', ['name' => $requestName]);

        $this->makeClass($name, 'contract');

        $this->makeClass($name, 'service');

        $this->info($this->type . ' created successfully.');
    }
}
