<?php
/**
 * Created by PhpStorm.
 * User: neal.yip
 * Date: 20/9/2017
 * Time: 17:24
 */

if (!function_exists('otp_validation_routes')) {
    function otp_validation_routes()
    {

        Route::group(['prefix' => 'otp_validation'], function () {
            Route::post('{key}', '\Nealyip\LaravelOTPValidation\Controllers\OTPValidationController@create');
            Route::put('{key}', '\Nealyip\LaravelOTPValidation\Controllers\OTPValidationController@attempt');
        });
    }
}

if (!function_exists('maskTelephoneNumber')) {
    /**
     * @param string $phoneNumber
     * @param int    $trim          Number of character that show as clear text
     * @param string $maskCharacter Mask character
     *
     * @return string
     */
    function maskTelephoneNumber($phoneNumber, $trim, $maskCharacter = '*')
    {
        $suffixNumber = substr($phoneNumber, strlen($phoneNumber) - $trim, $trim);
        $prefixNumber = substr($phoneNumber, 0, -$trim);
        $str          = '';
        for ($x = 0; $x < strlen($prefixNumber); $x++):
            $str .= (is_numeric($prefixNumber[$x])) ? str_replace($prefixNumber[$x], $maskCharacter, $prefixNumber[$x]) : $prefixNumber[$x];
        endfor;

        return $str . $suffixNumber;
    }
}

if (!function_exists('maskEmail')) {
    /**
     * @param string $email
     * @param int    $trim          Number of character that show as clear text
     * @param string $maskCharacter Mask character
     *
     * @return string
     */
    function maskEmail($email, $trim, $maskCharacter = '*')
    {

        list($userName, $domain) = explode('@', $email);

        $suffix = substr($userName, strlen($userName) - $trim, $trim);
        $prefix = substr($userName, 0, -$trim);
        $str          = '';
        for ($x = 0; $x < strlen($suffix); $x++):
            $str .= str_replace($suffix[$x], $maskCharacter, $suffix[$x]);
        endfor;

        return $prefix . $str. '@' . $domain;
    }
}