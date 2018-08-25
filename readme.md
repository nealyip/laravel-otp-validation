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
you may change it to the given mail service or write your own sms provider.

For the given mail service, simply change config/otp_validation.php
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

To implement your own transport interface, simply implement the **Nealyip\LaravelOTPValidation\Transport\TransportInterface** interface.
for example,
```php
namespace App\SMS\Transport;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Nealyip\LaravelOTPValidation\Transport\TransportInterface;

class SMSTransport implements TransportInterface {
    /**
     * @var Client 
     */
    private $_client;

    public function __construct()
    {
        $this->_client = new Client();
    }

    /**
     * @inheritDoc
     */
    public function type()
    {
        return static::TYPE_SMS;
    }
    
    /**
     * @inheritdoc
     */
    public function send($phone_number, $message)
    {

        $sms_url = 'https://someurl';
        $parsed_url = parse_url($sms_url);
        $host       = $parsed_url['host'];

        try {

            $this->_client->request('POST', $sms_url, [
                'http_errors' => true,
                'headers'     => [
                    'Host'         => $host,
                    'Authorization'=> 'Bearer ' . config('sms.auth_code') ,
                    'Content-Type' => 'application/json',
                    'Accept'       => 'application/json'
                ],
                'json'        => compact('phone_number', 'message')
            ]);
        } catch (GuzzleException $e) {
            throw new \Exception(trans('error.fail_to_send'), 0, $e);
        }
    }
}
```
and change your config file (config/otp_validation.php)  
```php
'transport'    => App\SMS\Transport\SMSTransport::class,
```

## How to use ##

First you are required to implement **Nealyip\LaravelOTPValidation\OTP\OTPTarget** to your user model.
Implement the target user mobile number and email address functions for otp transportation.

```php
namespace App;

class User implement Nealyip\LaravelOTPValidation\OTP\OTPTarget {

    /**
     * Provide the email for the user used by the Email Provider, 
     * the return value may varies by the scene.
     * May return empty string if you use only SMS otp.
     *
     * @param string $scene
     * @return string
     */
    public function otpEmail($scene = null)
    {

        return $this->email;
    }
    
    /**
     * Provide the mobile phone number for the user used by the SMS Provider,
     * the return value may varies by the scene.
     *
     * @param null $scene
     * @return string
     */
    public function otpMobile($scene = null)
    {

        return $this->country_code . $this->area_code . $this->mobile_number;
    }

    /**
     * A unique user id, for example
     *
     * @return string
     */
    public function otpIdentifier()
    {
        return $this->id;
    }
}
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

protected $_service;

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

And you may build a modal dialog from the client side and ask for the password.

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

you can then consume the otp

```php
use Nealyip\LaravelOTPValidation\Services\OTPValidationService;

class FormController{

protected $_service;

public function __construct(OTPValidationService $service) {
    $this->_service = $service;
}

public function create(Request $request){
    ...
    
        $user = request()->user();
    if ($request->input('otp')) {
        // WrongTargetException will be thrown if the otp is incorrect
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