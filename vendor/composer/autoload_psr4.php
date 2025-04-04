<?php

// autoload_psr4.php @generated by Composer

$vendorDir = dirname(__DIR__);
$baseDir = dirname($vendorDir);

return array(
    'SleekDB\\' => array($vendorDir . '/rakibtg/sleekdb/src'),
    'Robust\\Time\\' => array($baseDir . '/include/Time'),
    'Robust\\Boilerplate\\' => array($baseDir . '/include'),
    'Robust\\Auth\\' => array($baseDir . '/include/Auth', $baseDir . '/include/Auth/Application', $baseDir . '/include/Auth/Domain', $baseDir . '/include/Auth/Infrastructure'),
    'Picqer\\Barcode\\' => array($vendorDir . '/picqer/php-barcode-generator/src'),
    'Pecee\\' => array($vendorDir . '/pecee/simple-router/src/Pecee'),
    'Firebase\\JWT\\' => array($vendorDir . '/firebase/php-jwt/src'),
    'App\\' => array($baseDir . '/modules'),
);
