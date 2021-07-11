<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use InvalidArgumentException;
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
        } else if ($type == 'controller') {
            return  app_path() . '/Console/Commands/stubs/ApiController.stub';
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
    protected function controllerPath($path = '')
    {
        $path = "app/Http/Controllers";
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

            $replace = [];

            $replace = $this->buildContractReplacements($replace, $name);

            $this->files->put($path, str_replace(
                array_keys($replace),
                array_values($replace),
                $this->sortImports($this->buildClass($name, $type))
            ));
        }
        if ($type == 'controller') {

            $path = $this->controllerPath($name);

            $confirmation = $this->confirm('You Want To Make Controller In Your Custom Directory');

            $dir = '';
            if ($confirmation) {
                $dir = $this->ask('Please Enter Your Desire Contoller Directory');
            }

            $controllerName = str_replace('.', '/', $name . 'Controller') . '.php';

            $controllerPath = $confirmation ? $path . '/' . $dir . $controllerName : $path . '/' . $controllerName;

            $this->createDir($controllerPath);

            $controllerNamespace = $this->getControllerNamespace($dir, $controllerName);

            if ($this->files->exists($controllerPath)) {
                $this->error("File {$controllerPath} already exists!");
                return;
            }

            $replace = [];

            $replace = $this->buildControllerReplacements($replace, $name);

            $this->files->put($controllerPath, str_replace(
                array_keys($replace),
                array_values($replace),
                $this->sortImports($this->buildClass($name, $type, $controllerNamespace))
            ));
        }
        if ($type == 'service') {

            $this->createBaseServiceIfNotExist();

            $path = $this->servicePath($name);

            $this->createDir($path);

            if ($this->files->exists($path)) {
                $this->error("File {$path} already exists!");
                return;
            }

            $replace = [];

            $replace = $this->buildServiceReplacements($replace, $name);

            $this->files->put($path, str_replace(
                array_keys($replace),
                array_values($replace),
                $this->sortImports($this->buildClass($name, $type))
            ));
        }
    }


    /**
     * Build the model replacement values.
     *
     * @param  array  $replace
     * @return array
     */
    protected function buildServiceReplacements(array $replace, $model)
    {
        $modelClass = $this->parseModel($model);

        if (!class_exists($modelClass)) {
            if ($this->confirm("A {$modelClass} model does not exist. Do you want to generate it?", true)) {
                $this->call('make:model', ['name' => $modelClass]);
            }
        }

        return array_merge($replace, [
            '{{ message }}' => Str::lower($model),
            '{{ modelVariable }}' => Str::lower($model),
            '{{ modelList }}' => Str::title(Str::plural($model)),
        ]);
    }

    /**
     * Build the model replacement values.
     *
     * @param  array  $replace
     * @return array
     */
    protected function buildContractReplacements(array $replace, $model)
    {
        return array_merge($replace, [
            '{{ message }}' => Str::lower($model),
            '{{ modelVariable }}' => Str::lower($model),
            '{{ modelList }}' => Str::title(Str::plural($model)),
        ]);
    }


    /**
     * Build the model replacement values.
     *
     * @param  array  $replace
     * @return array
     */
    protected function buildControllerReplacements(array $replace, $model)
    {
        return array_merge($replace, [
            '{{ message }}' => Str::lower($model),
            '{{ modelVariable }}' => Str::lower($model),
            '{{ modelList }}' => Str::title(Str::plural($model)),
        ]);
    }

    /**
     * Get the default namespace for controller class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespaceforController($rootNamespace)
    {
        return $rootNamespace . '\Http\Controllers';
    }

    /**
     * Get namespace for Controller class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getControllerNamespace($dir, $controllerName)
    {
        $namespace = $this->getDefaultNamespaceforController('App') . Str::replaceLast('s', '', '\s') . str_replace('/','\\', $dir).$controllerName;
        return $namespace;
    }

    /**
     * Get the fully-qualified model class name.
     *
     * @param  string  $model
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    protected function parseModel($model)
    {
        if (preg_match('([^A-Za-z0-9_/\\\\])', $model)) {
            throw new InvalidArgumentException('Model name contains invalid characters.');
        }

        return $this->qualifyModel($model);
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
    protected function buildClass($name, $type = null, $namespace = null)
    {
        $stub = $this->files->get($this->getStub($type));
        $customNameSpace = $namespace != null ? $namespace : $name;
        return $this->replaceNamespace($stub, $customNameSpace)->replaceClass($stub, $name);
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


        $requestName = Str::ucfirst(Str::camel($name . 'Request'));

        $this->call('make:request', ['name' => $requestName]);

        $this->makeClass($name, 'controller');

        $this->makeClass($name, 'contract');

        $this->makeClass($name, 'service');

        $this->info($this->type . ' created successfully.');
    }
}
