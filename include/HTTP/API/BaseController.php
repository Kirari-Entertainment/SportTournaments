<?php namespace Robust\Boilerplate\HTTP\API;

use Robust\Boilerplate\File\File;
use Robust\Boilerplate\File\Image;
use Robust\Boilerplate\Infrastructure\Provider as InfProvider;
use Pecee\SimpleRouter\SimpleRouter;
use Robust\Auth\Authenticator;
use Robust\Auth\AuthException;
use Robust\Auth\CredentialManager;
use Robust\Auth\Roles;
use Robust\Boilerplate\HTTP\RCODES;

abstract class BaseController {
    public static function renderResponse(JSONResponse|File $response) : void {
        // Retorno de la respuesta
        if ($response instanceof File) {
            header( 'Content-type: '.$response->getMime() );
            http_response_code( RCODES::OK->value );

        } else {
            header( 'Content-type: Application/json' );
            http_response_code( $response->getCode() );
        }

        echo $response->getData();

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

    public static function parseBinaryFileFromCurrentRequest(): ?Image {
        // Check if content type indicates binary data
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

        // Read raw input
        $rawData = file_get_contents('php://input');

        if (empty($rawData)) {
            return null;
        }

        // Create image from binary data
        if (str_starts_with($contentType, 'image/')) {
            return new Image(
                data: $rawData,
                mime: $contentType
            );
        } else {
            // Determine other child class of File based on content type
            // Example: return new OtherFileClass(data: $rawData, mime: $contentType);
            return null; // Placeholder, replace with actual logic
        }
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