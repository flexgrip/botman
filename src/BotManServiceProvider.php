<?php

namespace BotMan\BotMan;

use BotMan\BotMan\Cache\LaravelCache;
use Illuminate\Support\ServiceProvider;
use BotMan\BotMan\Container\LaravelContainer;
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

            /**
             * Want to log the json payload from Facebook?
             *
             * $payload = $app->make('request')->getContent();
             * Log::info($payload);
             */

            # Get payload json into array
            $payload = json_decode($app->make('request')->getContent());

            # Get the pageId
            # Switch to array_get()
            $page_id = $payload->entry["0"]->messaging["0"]->recipient->id ?? 0;

            ### Lookup the token to use
            # Todo: Maybe add a cached token check to see who has the
            # working token from all the users that have this page
            $page = FacebookPage::where('page_id', $page_id)->first();
    
            # Override config value
            # Todo: Maybe only override if token matches? Maybe not
            config(['botman.facebook.token' => ($page->token ?? 0)]);
    
    
            $botman = BotManFactory::create(config('botman', []), new LaravelCache(), $app->make('request'),
                $storage);

            $botman->setContainer(new LaravelContainer($this->app));

            return $botman;
        });
    }
}
