<?php namespace Robust\Boilerplate\HTTP\API;

use Robust\Auth\AuthException;
use Robust\Boilerplate\HTTP\RCODES;
use Robust\Boilerplate\Infrastructure\InfrastructureException;
use Robust\Boilerplate\UseCase\UseCaseException;

use Robust\Auth\Roles;
use Robust\Boilerplate\File\Image;

abstract class DefaultController extends BaseController {
    public static function executeAsGuest(
        callable $managedUseCase,
        array $resultCodes
    ) {
        static::executeHandlingErrors(
            $managedUseCase,
            $resultCodes,
            false
        );
    }


    public static function executeAuthenticated(
        callable $managedUseCase,
        array $resultCodes,

        array $authorizedRoles = [ Roles::Administrator ],
        array $authorizedUserIds = [],
        bool $forHuman = true
    ) {
        static::executeHandlingErrors(
            $managedUseCase,
            $resultCodes,
            true,

            $authorizedRoles,
            $authorizedUserIds,
            $forHuman
        );
    }


    private static function executeHandlingErrors(
        callable $managedUseCase,
        array $resultCodes,
        bool $checkAuthorization,

        array $authorizedRoles = [ Roles::Administrator ],
        array $authorizedUserIds = [],
        bool $forHuman = true
    ) {
        $response = new JSONResponse();

        try {
            if ($checkAuthorization) static::checkAuthorization(
                $authorizedRoles,
                $authorizedUserIds,
                $forHuman
            );

            $result = $managedUseCase();
            $resultEvaluation = gettype($result) == 'object' ? get_class($result) : gettype($result);
            $response->setCode($resultCodes[$resultEvaluation]);
            $response->setData($result);

        } catch (AuthException $e) {
            match($e->getCode()) {
                AuthException::$FORBIDDEN_ACTION => $response->setCode(RCODES::Forbidden),
                AuthException::$UNKNOWN_USER, AuthException::$EXPIRED_SESSION =>
                    $response->setCode(RCODES::Unauthorized),
                default => $response->setCode(RCODES::InternalError)
            };

        } catch (InfrastructureException $e) {
            match ($e->getCode()) {
                InfrastructureException::$UNAVAILABLE => $response->setCode(RCODES::InternalError),
                InfrastructureException::$INTERNAL_ERROR => $response->setCode(RCODES::InternalError),
                InfrastructureException::$EXCEDEED_QUOTA => $response->setCode(RCODES::InternalError),
                default => $response->setCode(RCODES::InternalError)
            };

        } catch (UseCaseException $e) {
            match ($e->getCode()) {
                UseCaseException::$INVALID_PARAMETER => $response->setCode(RCODES::BadRequest, $e->getMessage()),
                UseCaseException::$ENTITY_NOT_FOUND => $response->setCode(RCODES::NotFound, $e->getMessage()),
                UseCaseException::$ENTITY_ALREADY_EXISTS => $response->setCode(RCODES::Conflict, $e->getMessage()),
            };

        } catch (\InvalidArgumentException $e) {
            $response->setCode(
                RCODES::BadRequest,
                details: "Los argumentos no son del tipo esperado: ".$e->getMessage()
            );
            
        } catch (\Exception $e) {
            $response->setCode(
                RCODES::InternalError,
                details: "
                    Ocurrió un error inesperado. Si los problemas persisten,
                    contacte a soporte.
                ".$e->getMessage()
            );

        } catch (\ArgumentCountError $e) {
            $response->setCode(
                RCODES::BadRequest,
                details: "No se envió el mínimo de argumentos necesarios."
            );
            
        } catch (\TypeError $e) {
            $response->setCode(
                RCODES::BadRequest,
                details: "Los argumentos no son del tipo esperado."
            );

        } catch(\Error $e) {
            if (str_contains($e->getMessage(), "Unknown named parameter ")) {
                $response->setCode(
                    RCODES::BadRequest,
                    details: "Se recibió un parámetro desconocido."
                );

            } else {
                $response->setCode(
                    RCODES::InternalError,
                    details: "
                                Ocurrió un error inesperado. Si los problemas persisten,
                                contacte a soporte.
                            " . $e->getMessage()
                );

            }
        }

        static::renderResponse($response);
    }
}
