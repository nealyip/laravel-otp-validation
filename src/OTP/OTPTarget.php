<?php
/**
 * Created by PhpStorm.
 * User: neal.yip
 * Date: 18/9/2017
 * Time: 12:21
 */

namespace Nealyip\LaravelOTPValidation\OTP;

interface OTPTarget
{

    /**
     * Provide the email for the user used by the Email Provider,
     * the return value may varies by the scene.
     * May return empty string if you use only SMS otp.
     *
     * @param null $scene
     *
     * @return string
     */
    public function otpEmail($scene = null);

    /**
     * Provide the mobile phone number for the user used by the SMS Provider,
     * the return value may varies by the scene.
     *
     * @param null $scene
     *
     * @return string
     */
    public function otpMobile($scene = null);

    /**
     * A unique user id, for example
     * $this->id
     *
     * @return string
     */
    public function otpIdentifier();
}