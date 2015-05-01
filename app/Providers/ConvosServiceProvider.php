<?php namespace App\Providers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class ConvosServiceProvider
    extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->booting(function () {
            $loader = \Illuminate\Foundation\AliasLoader::getInstance();
            $loader->alias('Conversation', 'App\Model\Convos\Conversation');
            $loader->alias('Participant', 'App\Model\Convos\Participant');
            $loader->alias('Message', 'App\Model\Convos\Message');
            $loader->alias('ConvosException', 'App\Model\ConvosException');
        });

        App::bind('App\Repositories\ConvosRepositoryInterface', 'App\Repositories\ConvosRepository');
        App::bind('App\Repositories\ConvosServiceInterface', 'App\Repositories\ConvosService');
    }

    public function provides()
    {
        return [
            "Repositories\\ConvosRepositoryInterface"
        ];
    }
}