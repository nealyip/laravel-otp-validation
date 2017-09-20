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
     * @param null $scene
     *
     * @return string
     */
    public function otpEmail($scene = null);

    /**
     * @param null $scene
     *
     * @return string
     */
    public function otpMobile($scene = null);

    /**
     * @return string
     */
    public function otpIdentifier();
}