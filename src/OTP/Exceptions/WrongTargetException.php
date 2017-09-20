<?php
/**
 * Created by PhpStorm.
 * User: neal.yip
 * Date: 19/9/2017
 * Time: 16:32
 */

namespace Nealyip\LaravelOTPValidation\OTP\Exceptions;

class WrongTargetException extends OTPValidationException
{


    /**
     * @var string
     */
    protected $_errorCode = 'otp_wrong_target';

    /**
     * @var int
     */
    protected $_statusCode = 503;

    public function __construct()
    {

        parent::__construct(trans('otp_messages::messages.otp_wrong_target'));
    }
}