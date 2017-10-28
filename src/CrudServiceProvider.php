<?php

namespace AbdulQadir\CRUDGenerator;

use Illuminate\Support\ServiceProvider;

class CrudServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        include __DIR__.'/routes.php';
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {	
		$this->app->make('AbdulQadir\CRUDGenerator\CRUDController');
		$this->loadViewsFrom(__DIR__.'/views', 'crud');        
    }
}
