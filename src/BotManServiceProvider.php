<?php

namespace BotMan\BotMan;

use BotMan\BotMan\Cache\LaravelCache;
use Illuminate\Support\ServiceProvider;
use BotMan\BotMan\Storages\Drivers\FileStorage;
use App\FacebookPage;
use Log;

class BotManServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {

        $this->app->singleton('botman', function ($app) {
            $storage = new FileStorage(storage_path('botman'));
    
            // get page id from incoming payload
            $pageId = array_get(json_decode($app->make('request')->getContent(), true), 'recipient.id');
    
            // lookup the token to use
            $token = FacebookPage::wherePageId($pageId)->get()->token;
    
	    Log::info("Here's the token I'm using for ".$pageId.": ".$token);
            // Override config value
            config(['botman.facebook.token' => $token]);
    
    
            return BotManFactory::create(config('botman', []), new LaravelCache(), $app->make('request'), $storage);
        });

	/**
	 * Original code
	 *
        $this->app->singleton('botman', function ($app) {
            $storage = new FileStorage(storage_path('botman'));

            return BotManFactory::create(config('botman', []), new LaravelCache(), $app->make('request'),
                $storage);
        });
	*/
    }
}
