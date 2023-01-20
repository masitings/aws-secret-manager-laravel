<?php

namespace Masitings\AwsSecretManager;
use Illuminate\Support\ServiceProvider;

class SecretManagerProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/aws-secret.php' => base_path('config/aws-secret.php'),
            ], 'config');
        }

        if (config('aws-secret.enable')) {
            $secretManager = new SecretManager();
            $secretManager->loadSecret();
        }
    }

    public function register()
    {

    }
}
