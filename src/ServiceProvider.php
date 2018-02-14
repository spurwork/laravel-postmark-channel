<?php

namespace Spur\PostmarkChannel;

use Postmark\PostmarkClient;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    protected $defer = true;

    public function register()
    {
        $this->app->singleton(PostmarkClient::class, function ($app) {
            return new PostmarkClient(
                config('services.postmark.key')
            );
        });
    }
}
