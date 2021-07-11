# Laravel ApiCrud Generate Command Setup Guide
 🤖 🤖 generate api crud from service and interface contract binding pettern  🤖 🤖

#this command first time generate new service provider name BindingServiceProvider  😇 😇

#you can register this provider in laravel config/app.php file   😇 😇 😇  then  😋 

#you can bind your crud interface contract to your crud service file   😋 😋  👽 👽
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
   

like this example you can bind service to interface contract

#Happy Coding  🤩 🤩 🤩 🤩 🤩 🤩
