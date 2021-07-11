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

[![Awesome](https://awesome.re/badge.svg)](https://awesome.re)

<a href="https://github.com/Tinkal779-rathore/Laravel-ApiCrudGenerateCommand/issues"><img alt="GitHub issues" src="https://img.shields.io/github/issues/Tinkal779-rathore/Laravel-ApiCrudGenerateCommand"></a>


# Controller Look Like

```.php
   <?php

      namespace App\Http\Controllers\Api\Common;

      use App\Contracts\CountryContract;
      use App\Http\Controllers\Controller;
      use App\Http\Requests\CountryRequest;
      use App\Models\Country;

      class CountryController extends Controller
      {
          protected $country;
          public function __construct(CountryContract $country)
          {
              $this->country = $country;
          }
          /**
           * Display a listing of the resource.
           *
           * @return \Illuminate\Http\Response
           */
          public function index()
          {
              return $this->country->listCountryies('id','asc');
          }

          /**
           * Store a newly created resource in storage.
           *
           * @param  \Illuminate\Http\Request  $request
           * @return \Illuminate\Http\Response
           */
          public function store(CountryRequest $request)
          {
              return $this->country->createCountry($request->only(Country::ACCESSABLE_FEILDS));
          }

          /**
           * Display the specified resource.
           *
           * @param  int  $id
           * @return \Illuminate\Http\Response
           */
          public function show($id)
          {
              return $this->country->findCountryById($id);
          }

          /**
           * Update the specified resource in storage.
           *
           * @param  \Illuminate\Http\Request  $request
           * @param  int  $id
           * @return \Illuminate\Http\Response
           */
          public function update(CountryRequest $request, $id)
          {
              return $this->country->updateCountry($request->only(Country::ACCESSABLE_FEILDS),$id);
          }

          /**
           * Remove the specified resource from storage.
           *
           * @param  int  $id
           * @return \Illuminate\Http\Response
           */
          public function destroy($id)
          {
              return $this->country->deleteCountry($id);
          }

          /**
           * Restore the specified resource from storage.
           *
           * @param  int  $id
           * @return \Illuminate\Http\Response
           */
          public function restore($id)
          {
              return $this->country->restoreCountry($id);
          }
      }

```

# Magic Is Here

```.php
   <?php
      namespace App\Services;

      use App\Contracts\CountryContract;
      use App\Facades\ApiResponse;
      use App\Models\Country;
      use Doctrine\DBAL\Query\QueryException;
      use Illuminate\Database\Eloquent\ModelNotFoundException;
      use Symfony\Component\HttpFoundation\Response;

      class CountryService extends BaseService implements CountryContract
      {

          /**
           * CountryService constructor.
           * @param Country $model
           */
          public function __construct(Country $model)
          {
              parent::__construct($model);
              $this->model = $model;
          }
          /**
           * @param string $order
           * @param string $sort
           * @param array $columns
           * @return mixed
           */
          public function listCountryies(string $order = 'id', string $sort = 'desc', array $columns = ['*'])
          {
              return ApiResponse::make(__('message.country.list'), $this->all($columns, $order, $sort), Response::HTTP_OK);
          }

          /**
           * @param int $id
           * @return mixed
           */
          public function findCountryById(int $id)
          {
              try {
                  return ApiResponse::make(__('message.country.show'), $this->findOneOrFail($id), Response::HTTP_OK);
              } catch (ModelNotFoundException $e) {
                  return ApiResponse::make(__('message.country.not_found'), null, Response::HTTP_NOT_FOUND);
              }
          }

          /**
           * @param array $params
           * @return mixed
           */
          public function createCountry(array $params)
          {
              try {
                  $country = $this->create($params);
                  return ApiResponse::make(__('message.country.add'), $country, Response::HTTP_OK);
              } catch (QueryException $exception) {
                  return ApiResponse::make(__('message.internal_error'), null, Response::HTTP_BAD_REQUEST);
              }
          }

          /**
           * @param array $params
           * @return mixed
           */
          public function updateCountry(array $data, int $id)
          {
              try {
                  $this->update($data, $id);
                  return ApiResponse::make(__('message.country.update'), null, Response::HTTP_OK);
              } catch (QueryException $exception) {
                  return ApiResponse::make(__('message.internal_error'), null, Response::HTTP_BAD_REQUEST);
              }
          }

          /**
           * @param $id
           * @return bool
           */
          public function deleteCountry($id)
          {
              $country = $this->delete($id);
              if ($country) {
                  return ApiResponse::make(__('message.country.deleted'), null, Response::HTTP_OK);
              } else {
                  return ApiResponse::make(__('message.country.not_found'), null, Response::HTTP_NOT_FOUND);
              }
          }

          /**
           * @param $id
           * @return bool
           */
          public function restoreCountry($id)
          {
              $country = $this->restoreById($id);
              if ($country) {
                  return ApiResponse::make(__('message.country.restore'), null, Response::HTTP_OK);
              } else {
                  return ApiResponse::make(__('message.country.not_found'), null, Response::HTTP_NOT_FOUND);
              }
          }
      }
```



# Cache Usage In BaseService File For Fast Experiance

```.php
   <?php

     namespace App\Services;

     use App\Contracts\BaseContract;
     use Illuminate\Database\Eloquent\Model;
     use Illuminate\Database\Eloquent\ModelNotFoundException;
     use Illuminate\Support\Facades\Cache;
     use Illuminate\Support\Str;

     /**
      * Class BaseService
      *
      * @package \App\Services
      */
     class BaseService implements BaseContract
     {
         /**
          * @var Model
          */
         protected $model;
         protected $cacheTime;
         protected $cacheName;
         /**
          * BaseService constructor.
          * @param Model $model
          */
         public function __construct(Model $model)
         {
             $this->model = $model;
             $this->cacheTime = now()->addHour();
             $this->cacheName = Str::plural($this->model) . 'PR001';
         }

         /**
          * @param array $attributes
          * @return mixed
          */
         public function create(array $attributes)
         {
             Cache::forget(Str::plural($this->model));
             return $this->model->create($attributes);
         }

         /**
          * @param array $attributes
          * @param int $id
          * @return bool
          */
         public function update(array $attributes, int $id): bool
         {
             Cache::forget(Str::plural($this->model));
             return $this->find($id)->update($attributes);
         }

         /**
          * @param array $columns
          * @param string $orderBy
          * @param string $sortBy
          * @return mixed
          */
         public function all($columns = array('*'), string $orderBy = 'id', string $sortBy = 'asc')
         {
             $data =  Cache::remember($this->cacheName, $this->cacheTime, function () use ($columns, $orderBy, $sortBy) {
                 return $this->model->orderBy($orderBy, $sortBy)->get($columns);
             });
             return $data;
         }

         /**
          * @param int $id
          * @return mixed
          */
         public function find(int $id)
         {
             $data =  Cache::remember($this->cacheName . 'single', $this->cacheTime, function () use ($id) {
                 return $this->model->find($id);
             });
             return $data;
         }

         /**
          * @param int $id
          * @return mixed
          * @throws ModelNotFoundException
          */
         public function findOneOrFail(int $id)
         {
             $data =  Cache::remember($this->cacheName . 'findOrFail', $this->cacheTime, function () use ($id) {
                 return $this->model->findOrFail($id);
             });
             return $data;
         }

         /**
          * @param array $data
          * @return mixed
          */
         public function findBy(array $data)
         {
             return $this->model->where($data)->all();
         }

         /**
          * @param array $data
          * @return mixed
          */
         public function findOneBy(array $data)
         {
             return $this->model->where($data)->first();
         }

         /**
          * @param array $data
          * @return mixed
          * @throws ModelNotFoundException
          */
         public function findOneByOrFail(array $data)
         {
             return $this->model->where($data)->firstOrFail();
         }

         /**
          * @param int $id
          * @return bool
          */
         public function delete(int $id): bool
         {
             Cache::forget(Str::plural($this->model));
             return $this->model->find($id)->delete();
         }


         /**
          * @param int $id
          * @return bool
          */
         public function restoreById(int $id): bool
         {
             Cache::forget(Str::plural($this->model));
             return $this->model->onlyTrashed()->where('id', $id)->restore();
         }
     }
```
