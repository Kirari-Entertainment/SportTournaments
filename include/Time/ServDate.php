<?php namespace Robust\Time;
/******************************************************
 * Dependencias
 ******************************************************/

use const Robust\SysSettings\TIMEZONE;

// Endpoints para obtener la fecha simple según el servidor
class ServDate {
    static function simple() : string {
        if (TIMEZONE) { $sdte = date('Y-m-d'); }
        else { throw new \Exception("Timezone is not defined.", 1); }

        return $sdte;
    }

    static function hour_minute() : string {
        if (TIMEZONE) { $hour = date('H:i'); }
        else { throw new \Exception("Timezone is not defined.", 1); }

        return $hour;
    }

    static function timestamp() : string {
        if (TIMEZONE) { $tmstmp = date('Y-m-d H:i:s'); }
        else { throw new \Exception("Timezone is not defined", 1); }

        return $tmstmp;
    }
}
