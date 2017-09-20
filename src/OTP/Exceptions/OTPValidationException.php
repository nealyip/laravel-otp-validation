<?php
/**
 * Created by PhpStorm.
 * User: neal.yip
 * Date: 21/9/2017
 * Time: 15:40
 */

namespace Nealyip\LaravelOTPValidation\OTP\Exceptions;


class OTPValidationException extends \Exception
{

    /**
     * @return string
     */
    public function getErrorCode()
    {
        return $this->_errorCode;
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->_statusCode;
    }
}