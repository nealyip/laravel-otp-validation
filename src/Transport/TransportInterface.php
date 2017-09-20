<?php
/**
 * Created by PhpStorm.
 * User: neal.yip
 * Date: 10/5/2017
 * Time: 11:54
 */

namespace Nealyip\LaravelOTPValidation\Transport;

interface TransportInterface
{

    const TYPE_SMS   = 'sms';
    const TYPE_EMAIL = 'email';

    /**
     * Type of this transport method, either TYPE_SMS, TYPE_EMAIL
     *
     * @return string
     */
    public function type();

    /**
     * @param string $phone_number
     * @param string $message
     *
     * @return mixed
     */
    public function send($phone_number, $message);
}