<?php
/**
 * Created by PhpStorm.
 * User: neal.yip
 * Date: 18/9/2017
 * Time: 12:03
 */

namespace Nealyip\LaravelOTPValidation\OTP;

interface OTPInterface
{


    /**
     * Override default options
     *
     * @param array $options
     *
     * @return static
     */
    public function configure(array $options);

    /**
     * Send an otp
     *
     * @param OTPTarget $target  Send to target
     * @param string    $seed    md5 hash 32 bytes seed
     * @param string    $scene   Scene passed to OTPTarget interface and differentiate amount scenes
     * @param string    $message Message body with :otp, :expire placeholder
     * @param array     $data    Extra information stored to cache
     *
     * @return Payload
     */
    public function send(OTPTarget $target, $seed, $scene, $message = null, array $data = []);

    /**
     * Resend using payload
     *
     * @param Payload $payload
     *
     * @return mixed
     */
    public function resend(Payload $payload);

    /**
     * Get payload
     *
     * @param string $hash string containing scene_seed
     *
     * @return Payload|null
     */
    public function payload($hash);

    /**
     * @param string $hash string containing scene_seed
     * @param string $password
     */
    public function attempt($hash, $password);

    /**
     * @param OTPTarget $target
     * @param string    $seed
     * @param string    $scene
     * @param string    $password
     *
     * @return Payload
     */
    public function consume(OTPTarget $target, $seed, $scene, $password);
}