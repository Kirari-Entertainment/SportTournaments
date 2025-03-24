<?php namespace Robust\Boilerplate\HTTP\API;

use Robust\Boilerplate\Infrastructure\Provider as InfProvider;
use Pecee\SimpleRouter\SimpleRouter;
use Robust\Auth\Authenticator;
use Robust\Auth\AuthException;
use Robust\Auth\CredentialManager;
use Robust\Auth\Roles;
use Robust\Boilerplate\File\Image;
use Robust\Boilerplate\HTTP\RCODES;

abstract class BaseController {
    public static function renderResponse(JSONResponse|Image $response) : void {
        // Retorno de la respuesta
        if ($response instanceof Image) {
            header( 'Content-type: '.$response->getMime() );
            echo $response->getData();

        } else {
            header( 'Content-type: Application/json' );
            http_response_code( $response->getCode() );
            echo $response->getData();
        }

    }

    public static function parseJsonInputFromCurrentRequest() : array {
        $inputHandler = SimpleRouter::request()->getInputHandler();
        return $inputHandler->getOriginalPost();
    }

    public static function parseURLParamsFromCurrentRequest() : array {
        $inputHandler = SimpleRouter::request()->getInputHandler();
        return $inputHandler->getOriginalParams();
    }

    public static function parseFilesFromCurrentRequest() : array {
        $inputHandler = SimpleRouter::request()->getInputHandler();
        return $inputHandler->getOriginalFile();
    }

    protected static function checkAuthorization(
        array $authorizedRoles = [Roles::Administrator],
        array $authorizedUsersIds = [],
        bool $forHuman = true

    ) : true {
        $authTokenString = null;

        if (isset($_COOKIE['RobustToken'])) {
            $credentialManager = InfProvider::requestEntity(
                CredentialManager::class,
                ["userType" => 'human']
            );

            $authTokenString = $_COOKIE['RobustToken'];

        } else if (isset($_SERVER['HTTP_AUTHORIZATION']) && $authorizationHeader = $_SERVER['HTTP_AUTHORIZATION']) {
            $credentialManager = InfProvider::requestEntity(
                CredentialManager::class,
                ["userType" => $forHuman ? 'human' : 'robot']
            );

            $authTokenString = $authorizationHeader;

        } else throw new AuthException(code: AuthException::$UNKNOWN_USER);
        Authenticator::checkAuthorization(
            $credentialManager,

            $authTokenString,

            $authorizedRoles,
            $authorizedUsersIds
        );

        return true;
    }

    public static function unallowedMethod() : void {
        $response = new JSONResponse();
        $response->setCode(RCODES::UnallowedMethod);
        static::renderResponse($response);
    }

    public static function unimplemented() {
        $response = new JSONResponse();
        $response->setCode(RCODES::Unimplemented);
        static::renderResponse($response);
    }

}