<?php
// Se cargan las dependencias de Composer
require '../vendor/autoload.php';

use Pecee\SimpleRouter\SimpleRouter;
use const Robust\SysSettings\TIMEZONE;

date_default_timezone_set(TIMEZONE);

try {
    Robust\Boilerplate\AccessControl::setOrigins();
    Robust\Boilerplate\AccessControl::confirmPreflight();
    Robust\Time\Endpoints::open();
    Robust\Auth\Endpoints::open();

    App\Soccer\Endpoints::open();

    SimpleRouter::start();

} catch (Pecee\SimpleRouter\Exceptions\NotFoundHttpException) {
    http_response_code(404);
}
