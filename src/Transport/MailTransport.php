<?php
/**
 * Created by PhpStorm.
 * User: neal.yip
 * Date: 21/9/2017
 * Time: 17:22
 */

namespace Nealyip\LaravelOTPValidation\Transport;


use Nealyip\LaravelOTPValidation\Mail\SimpleMail;

class MailTransport implements TransportInterface
{
    /**
     * @inheritDoc
     */
    public function type()
    {
        return static::TYPE_EMAIL;
    }

    /**
     * @inheritDoc
     */
    public function send($email, $message)
    {

        \Mail::to($email)
            ->queue(
                (new SimpleMail(config('otp_validation.mail_from_address'), config('otp_validation.mail_from_name'), [$message]))
                    ->subject(trans('otp_messages::messages.otp_email_subject'))
            );
    }

}