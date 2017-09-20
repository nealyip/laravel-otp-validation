<?php

namespace Nealyip\LaravelOTPValidation\Providers;

use Illuminate\Support\ServiceProvider;
use Nealyip\LaravelOTPValidation\OTP\GenericOTP;
use Nealyip\LaravelOTPValidation\Transport\ToLogTransport;
use Nealyip\LaravelOTPValidation\Transport\TransportInterface;
use Nealyip\LaravelOTPValidation\OTP\OTPInterface;
use Nealyip\LaravelOTPValidation\OTP\SMSOTP;

class OTPServiceProvider extends ServiceProvider
{

    const DS = DIRECTORY_SEPARATOR;

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $root = __DIR__ . static::DS . '..' . static::DS . '..' . static::DS;
        $this->publishes(
            [
                $root . 'config' . static::DS . 'otp_validation.php' => config_path('otp_validation.php'),
            ], 'config');


        $trans = $root . 'resources' . static::DS . 'lang';

        $this->loadTranslationsFrom($trans, 'otp_messages');

        $this->publishes(
            [
                $trans => resource_path('lang/vendor/otp_messages'),
            ]
        );
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {

        $this->mergeConfigFrom(__DIR__ . static::DS . '..' . static::DS . '..' . static::DS . 'config' . static::DS . 'otp_validation.php', 'otp_validation');

        $this->app->bind(OTPInterface::class, GenericOTP::class);

        $this->app->singleton(TransportInterface::class, config('otp_validation.transport'));
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            OTPInterface::class,
            TransportInterface::class
        ];
    }
}
