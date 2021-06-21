# Laravel ApiCrud Generate Command
generate api crud from service and contrract binding pettern

# <h2>this command firt time generate new service provider name BindingServiceProvider</h2>

# you can register this provider in laravel config/app.php file then

# you can bind your crud contract to your crud  service file
#ex:
```.php /**
     * Register Contract and Service To bind
     */
    protected $services = [
        BrandContract::class => BrandService::class,
        CountryContract::class => CountryService::class,
    ];
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        foreach ($this->services as $interface => $implementation) {
            $this->app->bind($interface, $implementation);
        }
    }
```
   

like this example you can bind service to interface 

#Happy Coding  🤩 🤩 🤩 🤩 🤩 🤩
