<?php namespace Robust\Auth;

use DateTime;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;

class CredentialManagerFromDBAndJWT extends UserRepositoryFromDB implements CredentialManager {
    private const alg = 'HS256';
    private const keyString = 'RANDOM';

    public function generateAuthTokenValidUntilFor(\DateTime $expiration, int $ownerId) : ?AuthToken {
        if ($expiration > (new DateTime())) {
            return new AuthToken(
                static::generateToken(
                    payload: [
                        "exp" => $expiration->getTimestamp(),
                        "owner" => $ownerId
                    ]
                ),
                $expiration,
                ownerId: $ownerId
            );

        } else return null;
    }

    public function recoverAuthToken(string $token): ?AuthToken {
        try {
            $decodedData = JWT::decode(
                $token,
                static::getKey()
            );

        // To be managed in the use case
        } catch (ExpiredException $e) {
            preg_match('/(?<=\.)[^.]+(?=\.)/', $token, $decodedData);
            $decodedData = json_decode(base64_decode($decodedData[0]));

        } catch (SignatureInvalidException) {
            return null;
        }

        return new AuthToken(
            tokenString: $token,
            expiresAt: new DateTime(date("Y-m-d H:i:s", $decodedData->exp)),
            ownerId: $decodedData->owner
        );
    }


    public function dropAuthToken(AuthToken $newToken): void {
        // TODO: Crear lista negra de tokens
    }


    private static function getKey(): Key {
        return new Key(
            keyMaterial: self::keyString,
            algorithm: self::alg
        );
    }

    private static function generateToken($payload): string {
        return JWT::encode(
            payload: $payload,
            key: self::keyString,
            alg: self::alg
        );
    }
}