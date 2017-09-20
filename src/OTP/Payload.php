<?php
/**
 * Created by PhpStorm.
 * User: neal.yip
 * Date: 18/9/2017
 * Time: 16:35
 */

namespace Nealyip\LaravelOTPValidation\OTP;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;


/**
 * Class Payload
 *
 * @package Nealyip\LaravelOTPValidation\OTP
 * @property-read string maskTo   Masked to address
 * @property-read int    resendIn Resend in ... seconds
 */
class Payload implements \JsonSerializable, \Serializable, Arrayable
{

    /**
     * @var string hash key
     */
    public $key;

    /**
     * @var int timestamp
     */
    public $expire;

    /**
     * @var int timestamp Time to be able to resend
     */
    public $available;

    /**
     * @var int number of attempts
     */
    public $attempt = 1;

    /**
     * @var string
     */
    public $targetID;

    /**
     * @var string
     */
    public $password;

    /**
     * @var array Storage of information
     */
    public $data = [];

    /**
     * @var string message
     */
    public $message;

    /**
     * @var string to target number/email, it's not masked
     */
    public $to;

    /**
     * @param string $name
     *
     * @return null|string
     */
    public function __get($name)
    {
        switch ($name) {
            case 'maskTo':
                return $this->_maskTo();
            case 'resendIn':
                return $this->_resendIn();
        }
        return null;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function __isset($name)
    {
        return in_array($name, ['maskTo', 'resendIn']);
    }

    /**
     * Return a masked to address
     *
     * @return string
     */
    protected function _maskTo()
    {
        if (filter_var($this->to, FILTER_VALIDATE_EMAIL)) {
            return maskEmail($this->to, 4);
        }
        return maskTelephoneNumber($this->to, 4);
    }

    /**
     * Resend in ... seconds
     *
     * @return int
     */
    protected function _resendIn()
    {
        return max($this->available - time(), 0);
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return $this->_toDefArray();
    }

    /**
     * @inheritDoc
     */
    public function serialize()
    {
        return serialize($this->_toDefArray());
    }

    /**
     * @inheritDoc
     */
    public function unserialize($serialized)
    {

        foreach (unserialize($serialized) as $item => $value) {
            $this->$item = $value;
        }
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        $resendIn = $this->resendIn;
        $maskTo   = $this->maskTo;

        return array_merge($this->_toDefArray(), compact('resendIn', 'maskTo'));
    }

    /**
     * To defined properties array for serialization
     *
     * @return array
     */
    protected function _toDefArray()
    {
        return Arr::only(get_object_vars($this), array_keys(get_class_vars(get_called_class())));
    }
}