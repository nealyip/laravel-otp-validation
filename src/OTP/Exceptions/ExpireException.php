<?php
/**
 * Created by PhpStorm.
 * User: neal.yip
 * Date: 19/9/2017
 * Time: 16:57
 */

namespace Nealyip\LaravelOTPValidation\OTP\Exceptions;


class ExpireException extends OTPValidationException
{

    /**
     * @var string
     */
    protected $_errorCode = 'otp_expired';

    /**
     * @var int
     */
    protected $_statusCode = 403;

    public function __construct()
    {

        parent::__construct(trans('otp_messages::messages.otp_expired'));
    }
}