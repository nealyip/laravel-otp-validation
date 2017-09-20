<?php


return [
    'transport'    => \Nealyip\LaravelOTPValidation\Transport\ToLogTransport::class,
    'expiry'       => 300, //seconds for expiry
    'max_attempts' => 5, //max attempts for an otp
    'available'    => 60, // seconds available to trigger a resend
    'otp_seed'     => explode(',', env('OTP_SEED', '5,7,9,16,18,29')), //This sequence is used to generate refund otp, please use six numbers which are randomly picked and ranged from 0 to 31

    'mail_from_address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
    'mail_from_name'    => env('MAIL_FROM_NAME', 'Example')
];