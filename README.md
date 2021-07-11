# Laravel Api Crud Generator Setup Guide
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

<!--
  Title: Awesome Regex
  Description: A curated list of amazingly awesome regex resources.
  Author: aloisdg
  -->

# Awesome Regex

[![Awesome](https://awesome.re/badge.svg)](https://awesome.re)
[![Main workflow](https://github.com/aloisdg/awesome-regex/workflows/Main%20workflow/badge.svg)](https://github.com/aloisdg/awesome-regex/actions)

<a href="https://github.com/Tinkal779-rathore/Laravel-ApiCrudGenerateCommand/issues"><img alt="GitHub issues" src="https://img.shields.io/github/issues/Tinkal779-rathore/Laravel-ApiCrudGenerateCommand"></a>
