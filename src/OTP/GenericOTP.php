<?php
/**
 * Created by PhpStorm.
 * User: neal.yip
 * Date: 18/9/2017
 * Time: 12:04
 */

namespace Nealyip\LaravelOTPValidation\OTP;


use Nealyip\LaravelOTPValidation\Transport\TransportInterface;
use Nealyip\LaravelOTPValidation\OTP\Exceptions\ExpireException;
use Nealyip\LaravelOTPValidation\OTP\Exceptions\FailAttemptException;
use Nealyip\LaravelOTPValidation\OTP\Exceptions\NotAvailableExceptions;
use Nealyip\LaravelOTPValidation\OTP\Exceptions\RequestNotFoundException;
use Nealyip\LaravelOTPValidation\OTP\Exceptions\TooManyAttemptsException;
use Nealyip\LaravelOTPValidation\OTP\Exceptions\WrongTargetException;
use Illuminate\Support\Arr;
use Cache;

class GenericOTP implements OTPInterface
{

    /**
     * @var TransportInterface
     */
    protected $_transport;

    /**
     * @var Cache
     */
    protected $_cache;

    /**
     * @var int
     */
    protected $_expiry;

    /**
     * @var int
     */
    protected $_maxAttempt;

    /**
     * @var int Resend available after ... seconds
     */
    protected $_available;

    public function __construct(TransportInterface $transport, Cache $cache)
    {

        $this->_transport  = $transport;
        $this->_cache      = $cache;
        $this->_expiry     = config('otp_validation.expiry');
        $this->_maxAttempt = config('otp_validation.max_attempts');
        $this->_available  = config('otp_validation.available');
    }

    /**
     * @inheritDoc
     */
    public function configure(array $options)
    {
        foreach (Arr::only($options, ['expiry', 'maxAttempt', 'available']) as $key => $option) {
            $key        = '_' . $key;
            $this->$key = $option;
        }

        return $this;
    }


    /**
     * @inheritdoc
     */
    public function send(OTPTarget $target, $seed, $scene, $message = null, array $data = [])
    {

        $key = $this->_key($scene, $seed);

        $payload           = $this->_cache::get($key) ?? new Payload();
        $payload->targetID = $target->otpIdentifier();
        $payload->data     = $data;
        $payload->key      = $key;
        $payload->message  = $message;
        $payload->to       = $this->_number($target, $scene);

        return $this->_send($payload);
    }

    /**
     * Send sms by payload
     *
     * @param Payload $payload
     *
     * @return Payload
     */
    protected function _send(Payload $payload)
    {

        if (!is_null($payload->available) && $payload->available > time()) {
            throw new NotAvailableExceptions($payload);
        }

        $payload->expire    = time() + $this->_expiry;
        $payload->available = time() + $this->_available;
        $payload->password  = $this->_password($payload->key);

        $this->_cache::put($payload->key, $payload, $this->_expiry / 60);
        $message = str_replace(':otp', $payload->password, $payload->message);
        $this->_transport->send($payload->to, $message);

        return $payload;
    }

    /**
     * Resend sms
     *
     * @param Payload $payload
     *
     * @return Payload
     */
    public function resend(Payload $payload)
    {

        return $this->_send($payload);
    }

    /**
     * @inheritDoc
     */
    public function payload($hash)
    {

        $cache = $this->_cache::get($hash);

        if (is_null($cache) || time() > $cache->expire) {
            return null;
        }

        return $cache;
    }

    /**
     * @param $hash
     * @param $password
     *
     * @return Payload|null
     * @throws ExpireException
     * @throws FailAttemptException
     * @throws RequestNotFoundException
     * @throws TooManyAttemptsException
     */
    protected function _attempt($hash, $password)
    {


        $cache = $this->_cache::get($hash);

        if (is_null($cache)) {
            throw new RequestNotFoundException();
        }

        if (time() > $cache->expire) {
            throw new ExpireException();
        }

        if (strcmp($cache->password, $password) !== 0) {
            // error
            if ($this->tooManyAttempts($cache)) {
                throw new TooManyAttemptsException();
            }
            $cache->attempt++;

            $this->_cache::put($hash, $cache, $cache->expire);
            throw new FailAttemptException();
        }

        return $cache;
    }

    /**
     * @inheritDoc
     */
    public function attempt($hash, $password)
    {

        $this->_attempt($hash, $password);
    }


    /**
     * @inheritdoc
     */
    public function consume(OTPTarget $target, $seed, $scene, $password)
    {

        $hash = $this->_key($scene, $seed);

        $cache = $this->_attempt($hash, $password);

        if (strcmp($cache->targetID, $target->otpIdentifier()) !== 0) {
            throw new WrongTargetException();
        }

        $this->_cache::forget($hash);

        return $cache;
    }


    /**
     * @return string
     */
    protected function _number(OTPTarget $target, $scene)
    {
        switch ($this->_transport->type()) {
            case $this->_transport::TYPE_SMS:
                return $target->otpMobile($scene);
            case $this->_transport::TYPE_EMAIL:
                return $target->otpEmail($scene);
        }
        throw new \Exception('unknown type');
    }

    /**
     * Is too many attempt?
     *
     * @param Payload $payload
     *
     * @return bool
     */
    protected function tooManyAttempts(Payload $payload)
    {
        return $payload->attempt >= $this->_maxAttempt;
    }

    /**
     * @param string $seed md5 32 bytes hash
     *
     * @return string
     */
    protected function _password($seed)
    {

        $hash = md5($seed . bin2hex(random_bytes(32)));

        $sequence = array_map(function ($v) {
            return $v % 10;
        }, array_map('ord', str_split($hash, 1)));

        return implode('', Arr::only($sequence, config('otp_validation.otp_seed')));
    }

    /**
     * Get store key
     *
     * @param string $scene
     * @param string $seed
     *
     * @return string
     */
    protected function _key($scene, $seed)
    {

        return $scene . '_' . $seed;
    }
}
