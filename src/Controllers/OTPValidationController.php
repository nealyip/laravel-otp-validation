<?php
/**
 * Created by PhpStorm.
 * User: neal.yip
 * Date: 20/9/2017
 * Time: 19:01
 */

namespace Nealyip\LaravelOTPValidation\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Nealyip\LaravelOTPValidation\Services\OTPValidationService;

class OTPValidationController
{

    /**
     * @var OTPValidationService
     */
    protected $_service;

    public function __construct(OTPValidationService $service)
    {

        $this->_service = $service;
    }

    /**
     * Attempt a password
     *
     * @param string               $key
     * @param Request              $request
     * @param OTPValidationService $service
     */
    public function attempt($key, Request $request)
    {

        list($scene) = explode('_', $key);
        $this->_service->scene($scene)->attempt($key, $request->input('otp'));

        return $this->_success();
    }

    /**
     * @param string $key
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function create($key)
    {

        list($scene) = explode('_', $key);
        $payload   = $this->_service->scene($scene)->resend($key);
        $resend_in = $payload->resendIn;

        return $this->_success('success', compact('resend_in'));
    }

    /**
     * Success response
     *
     * @param string $code
     * @param array  $extra
     * @param int    $status
     * @param bool   $forceJson
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    protected function _success($code = 'success', $extra = [], $status = 200, $forceJson = false)
    {
        if (request()->expectsJson() || $forceJson) {
            extract($extra);
            unset($extra);
            unset($forceJson);
            return response()->json(get_defined_vars());
        }
        return response()->make($code, $status);
    }
}