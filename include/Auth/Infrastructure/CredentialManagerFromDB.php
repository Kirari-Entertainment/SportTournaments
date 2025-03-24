<?php namespace Robust\Auth;

use PDO;
use DateTime;

class CredentialManagerFromDB extends UserRepositoryFromDB implements CredentialManager {
    private string $tokenTableName;

    public function __construct(PDO $_dbConnection = null) {
        $this->tokenTableName = stripslashes(AuthToken::class);

        parent::__construct($_dbConnection);

        $queriesCreateTables = [];
        
        $queriesCreateTables[] = "CREATE TABLE IF NOT EXISTS $this->tokenTableName (
            token       VARCHAR(255),
            expiration  TIMESTAMP,
            user        VARCHAR(255),

            PRIMARY KEY (token)
        )";

        foreach ($queriesCreateTables as $query) {
            $stmt = $this->dbConnection->prepare($query);
            $stmt->execute();
        }
    }


    public function generateAuthTokenValidUntilFor(\DateTime $expiration, int $ownerId): ?AuthToken {
        if ($expiration > (new DateTime())) {
            $newToken = new AuthToken(
                static::generateToken(),
                $expiration,
                $ownerId
            );

            $this->storeToken($newToken);

            return $newToken;

        } else return null;
    }

    public function recoverAuthToken(string $token): ?AuthToken {
        $foundToken = null;

        $query = "SELECT * FROM $this->tokenTableName WHERE token = :token";
        $stmt = $this->dbConnection->prepare($query);
        $stmt->bindValue(':token', $token);

        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $foundToken = new AuthToken(
                tokenString: $result['token'],
                expiresAt: new DateTime($result['expiration']),
                ownerId: $result['user']
            );
        }

        return $foundToken;
    }


    public function dropAuthToken(AuthToken $newToken): void {
        $query = "DELETE FROM $this->tokenTableName WHERE token = :token";
        $stmt = $this->dbConnection->prepare($query);
        $stmt->bindValue(':token', $newToken);
        $stmt->execute();
    }


    private static function generateToken(): string {
        return bin2hex(random_bytes(20));
    }


    private function storeToken(AuthToken $token): void {
        $query = "INSERT INTO $this->tokenTableName (token, expiration, user)
            VALUES (:token, :expiration, :user)
        ";

        $stmt = $this->dbConnection->prepare($query);
        $stmt->bindValue(':token', $token->tokenString);
        $stmt->bindValue(':expiration', $token->expiresAt->format('Y-m-d H:i:s'));
        $stmt->bindValue(':user', $token->ownerId);

        $stmt->execute();
    }
}