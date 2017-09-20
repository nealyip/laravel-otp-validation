<?php
/**
 * Created by PhpStorm.
 * User: neal.yip
 * Date: 19/9/2017
 * Time: 16:34
 */

namespace Nealyip\LaravelOTPValidation\OTP\Exceptions;


class TooManyAttemptsException extends OTPValidationException
{

    /**
     * @var string
     */
    protected $_errorCode = 'otp_too_many_attempts';

    /**
     * @var int
     */
    protected $_statusCode = 429;

    public function __construct()
    {

        parent::__construct(trans('otp_messages::messages.otp_too_many_attempts'));
    }
}