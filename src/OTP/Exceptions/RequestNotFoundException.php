<?php
/**
 * Created by PhpStorm.
 * User: neal.yip
 * Date: 19/9/2017
 * Time: 16:29
 */

namespace Nealyip\LaravelOTPValidation\OTP\Exceptions;

class RequestNotFoundException extends OTPValidationException
{


    /**
     * @var string
     */
    protected $_errorCode = 'otp_not_found';

    /**
     * @var int
     */
    protected $_statusCode = 404;

    public function __construct()
    {

        parent::__construct(trans('otp_messages::messages.otp_not_available'));
    }

}