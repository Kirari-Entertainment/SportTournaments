<?php namespace Robust\Auth;

use Robust\Boilerplate\HTTP;
use Robust\Boilerplate\HTTP\API;
use Robust\Boilerplate\IdGenerator;
use Robust\Boilerplate\Infrastructure\Provider as InfProvider;

class APIAuthController extends API\DefaultController {
    public static function index() : void {
        static::executeAuthenticated(
            managedUseCase: fn() => UserManagement::listUsers(
                InfProvider::requestEntity(CredentialManager::class)
            ),

            resultCodes: [ 'array' => HTTP\RCODES::OK ],

            authorizedRoles: [Roles::Administrator]
        );
    }

    public static function signup() : void {
        $response = new API\JSONResponse;
        $inputData = static::parseJsonInputFromCurrentRequest();

        try {
            UserManagement::registerUser(
                InfProvider::requestEntity(CredentialManager::class, ['userType' => 'human']),
                InfProvider::requestEntity(IdGenerator::class),
                ...$inputData
            );

            $response->setCode(HTTP\RCODES::Created);

        } catch (AuthException $e) {
            match ($e->getCode()) {
                AuthException::$DUPLICATED_USER => $response->setCode(HTTP\RCODES::Conflict),
                AuthException::$WEAK_PASSWORD => $response->setCode(HTTP\RCODES::BadRequest)
            };

        } catch (\Exception $e) { $response->setCode(HTTP\RCODES::InternalError); }

        static::renderResponse($response);
    }

    public static function login() : void {
        $URLParams = static::parseURLParamsFromCurrentRequest();
        $useCookies = isset($URLParams['useCookies']) && $URLParams['useCookies'];

        if ($useCookies) static::loginAndSetCookie();
        else static::loginAndGetToken();
    }

    private static function loginAndGetToken() : void {
        $response = new API\JSONResponse;
        $inputData = static::parseJsonInputFromCurrentRequest();

        try {
            $token = Authenticator::authenticateUserWithPassword(
                InfProvider::requestEntity(CredentialManager::class, ['userType' => 'human']),
                ...$inputData
            );

            if ($token) {
                $response->setCode(HTTP\RCODES::OK);
                $response->setData($token);

            } else {
                $response->setCode(HTTP\RCODES::Unauthorized);
            }

        } catch (AuthException $e) {
            match ($e->getCode()) {
                AuthException::$UNKNOWN_USER => $response->setCode(HTTP\RCODES::NotFound),
                AuthException::$MAX_LOGIN_ATTMPTS_EXCEDEED => $response->setCode(HTTP\RCODES::TooManyRequests)
            };

        } catch (\Exception $e) { $response->setData($e->getMessage()); $response->setCode(HTTP\RCODES::InternalError); }

        static::renderResponse($response);
    }

    private static function loginAndSetCookie() : void {
        try {
            $token = Authenticator::authenticateUserWithPassword(
                InfProvider::requestEntity(CredentialManager::class, ['userType' => 'human']),
                ...static::parseJsonInputFromCurrentRequest()
            );

            if ($token) {
                setcookie('RobustToken', $token->tokenString, $token->expiresAt->getTimestamp(), '/');
                http_response_code(HTTP\RCODES::OK->value);

            } else {
                http_response_code(HTTP\RCODES::Unauthorized->value);
            }

        } catch (AuthException $e) {
            match ($e->getCode()) {
                AuthException::$UNKNOWN_USER => http_response_code(HTTP\RCODES::NotFound->value),
                AuthException::$MAX_LOGIN_ATTMPTS_EXCEDEED => http_response_code(HTTP\RCODES::TooManyRequests->value)
            };

        } catch (\Exception $e) { http_response_code(HTTP\RCODES::InternalError->value); }
    }
}