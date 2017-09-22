<?php

namespace Nealyip\LaravelOTPValidation\Transport;


use App\Mail\SimpleEmail;
use App\User;

class ToLogTransport implements TransportInterface
{
    /**
     * @inheritDoc
     */
    public function type()
    {
        return static::TYPE_SMS;
    }


    /**
     * @inheritdoc
     *
     */
    public function send($phone_number, $message)
    {

        error_log(trans('otp_messages::messages.sms_to_email_intro', ['mobile' => $phone_number]) . PHP_EOL . $message . PHP_EOL);
    }
}