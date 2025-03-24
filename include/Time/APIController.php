<?php namespace Robust\Time;

use Robust\Boilerplate\HTTP;
use Robust\Boilerplate\HTTP\API;
use const Robust\SysSettings\TIMEZONE;

class APIController extends API\BaseController {
    public static function index(): void {
        $response = new API\JSONResponse;
        try {
            $time = [
                "timezone"  => TIMEZONE,
                "date"      => ServDate::simple(),
                "hour"      => ServDate::hour_minute(),
                "stamp"     => ServDate::timestamp(),
            ];

            $response->setData($time);

            $response->setCode(HTTP\RCODES::OK);

        } catch (\Exception $e) { $response->setCode(HTTP\RCODES::InternalError); }

        static::renderResponse($response);
    }

    public static function show($elem) {
        $response = new API\JSONResponse;

        $response->setData(match ($elem) {
            "timezone"  => TIMEZONE,
            "date"      => ServDate::simple(),
            "hour"      => ServDate::hour_minute(),
            "stamp"     => ServDate::timestamp(),
        });

        $response->setCode(HTTP\RCODES::OK);

        static::renderResponse($response);
    }
}