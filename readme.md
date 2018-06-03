## Description ##
The packages handle otp in various way for you. It is designed for modal dialog and called by ajax.

## Install ##
```
composer require nealyip/laravel-otp-validation
```

Add this provider to config/app.php
```php
Nealyip\LaravelOTPValidation\Providers\OTPServiceProvider::class,
```

Publish config

```bash
php .\artisan vendor:publish --provider=Nealyip\LaravelOTPValidation\Providers\OTPServiceProvider
```

## Configuration ##

You may translate error message under resources/lang/vendor/otp_messages

By default it use log transport service,
you may change it to provided mail service or write your own sms provider.

Simply change config/otp_validation.php
```php
// for example
'transport'    => \Nealyip\LaravelOTPValidation\Transport\MailTransport::class,
```
for the default mail transport class, define your mail driver, from address and name
(MAIL_FROM_ADDRESS and MAIL_FROM_NAME for smtp)

OTP_SEED
you can change the seed for otp by changing OTP_SEED env
check otp_validation.php for more details

## Development ##

To implement your own transport interface, simple implement the **Nealyip\LaravelOTPValidation\Transport\TransportInterface** interface.

and change your config file.

## How to use ##

First you are required to implement **Nealyip\LaravelOTPValidation\OTP\OTPTarget** to your user model.
It will provides the target user mobile number and email address for otp transportation.

```php
namespace App;

class User implement Nealyip\LaravelOTPValidation\OTP\OTPTarget {
```

Add routes for password attempt and resend request

```php
Route::group(['prefix' => 'backend'], function(){

otp_validation_routes();

});
```


To send the first otp, just call the send method from the OTPValidationService
```php
use Nealyip\LaravelOTPValidation\Services\OTPValidationService;

class FormController{

protected $service;

public function __construct(OTPValidationService $service) {
    $this->_service = $service;
}

public function create(){
    ...
    $user = request()->user();
    $payload = $this->_service->scene('changepassword')->send($user, ['id' => $user->id], [], 'You are about to change your password, please complete with this one time password :otp');
    
    return response()->json(['sent' => true, 'key' => $payload->key]);   
}
```

And you may build a model from the client side and ask for the password.

To attempt a password,
just call the routes, for example
```
PUT /backend/otp_validation/{key}
with json message body
{otp: 'password'}
```

to request resend
```
POST /backend/otp_validation/{key}
```

if the otp is correct it will return a success response with HTTP status code 200

you can then use the otp to submit again

```php
use Nealyip\LaravelOTPValidation\Services\OTPValidationService;

class FormController{

protected $service;

public function __construct(OTPValidationService $service) {
    $this->_service = $service;
}

public function create(Request $request){
    ...
    
        $user = request()->user();
    if ($request->input('otp')) {
        $this->_service->scene('changepassword')->consume($user, ['id' => $user->id], $request->input('otp'));    
        
        // success validate the otp
        // do your staff here
        
        return .....
    } else {
        $payload = $this->_service->scene('changepassword')->send($user, ['id' => $user->id], [], 'You are about to change your password, please complete with this one time password :otp');
        
        return response()->json(['sent' => true, 'key' => $payload->key]);
    }   
}
```