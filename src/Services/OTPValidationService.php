<?php
/**
 * Created by PhpStorm.
 * User: neal.yip
 * Date: 15/9/2017
 * Time: 17:25
 */


namespace Nealyip\LaravelOTPValidation\Services;

use Illuminate\Support\Arr;
use Nealyip\LaravelOTPValidation\OTP\Exceptions\ExpireException;
use Nealyip\LaravelOTPValidation\OTP\OTPInterface;
use Nealyip\LaravelOTPValidation\OTP\OTPTarget;
use Nealyip\LaravelOTPValidation\OTP\Payload;

class OTPValidationService
{

    /**
     * @var OTPInterface
     */
    protected $_otp;

    /**
     * @var string
     */
    protected $_scene;

    public function __construct(OTPInterface $otp)
    {

        $this->_otp = $otp;
    }


    /**
     * Set scene
     *
     * @param string $scene
     *
     * @return static
     */
    public function scene($scene)
    {
        $this->_scene = $scene;
        return $this;
    }


    /**
     * @param OTPTarget $target
     * @param array     $seed
     * @param string    $password
     *
     * @return Payload
     *
     * @throws \Exception
     */
    public function consume(OTPTarget $target, array $seed, $password)
    {

        return $this->_otp->consume($target, $this->_hash($seed), $this->_scene, $password);
    }

    /**
     * Attempt a password, this just check if a password match the hash.
     * since the hash is publicly known, you should not authenticate user with this method,
     * use consume instead.
     *
     * @see consume()
     *
     * @param string $hash     string containing scene_seed
     * @param string $password password to attempt, fail count will be conducted
     *
     * @throws \Exception
     */
    public function attempt($hash, $password)
    {

        $this->_otp->attempt($hash, $password);
    }

    /**
     * Send an otp
     *
     * @param OTPTarget $target  Target Model
     * @param array     $seed    Array of data to be hashed
     * @param array     $data    Store information
     * @param string    $message Message body
     *
     * @return Payload
     *
     * @throws \Exception
     */
    public function send(OTPTarget $target, array $seed, array $data, $message)
    {

        return $this->_otp->send($target, $this->_hash($seed), $this->_scene, $message, Arr::only($data, ['amount', 'is_sync']));
    }

    /**
     * Resend the otp
     *
     * @param string $hash string containing scene_seed
     *
     * @return Payload
     * @throws ExpireException
     */
    public function resend($hash)
    {

        $payload = $this->get($hash);
        if (is_null($payload)) {
            throw new ExpireException();
        }
        return $this->_otp->resend($payload);
    }

    /**
     * @param string $hash string containing scene_seed
     *
     * @return Payload|null
     */
    public function get($hash)
    {

        return $this->_otp->payload($hash);
    }


    /**
     * @param array $seed
     *
     * @return string
     */
    protected function _hash(array $seed)
    {
        return md5(implode(',', $seed));
    }
}