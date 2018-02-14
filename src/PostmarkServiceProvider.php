<?php

namespace Spur\Postmark;

use Postmark\PostmarkClient;

class PostmarkServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function register()
    {
        $this->app->singleton(PostmarkClient::class, function ($app) {
            return new PostmarkClient(
                config('services.postmark.key')
            );
        });
    }
}
