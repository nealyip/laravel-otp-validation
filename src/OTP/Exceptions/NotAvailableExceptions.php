<?php
/**
 * Created by PhpStorm.
 * User: neal.yip
 * Date: 19/9/2017
 * Time: 15:50
 */

namespace Nealyip\LaravelOTPValidation\OTP\Exceptions;


use Nealyip\LaravelOTPValidation\OTP\Payload;

class NotAvailableExceptions extends OTPValidationException
{


    /**
     * @var string
     */
    protected $_errorCode = 'otp_not_available';

    /**
     * @var int
     */
    protected $_statusCode = 429;

    /**
     * NotAvailableExceptions constructor.
     *
     * @param Payload $payload
     */
    public function __construct(Payload $payload)
    {

        parent::__construct(trans('otp_messages::messages.otp_not_available', ['seconds' => $payload->resendIn]));
    }
}