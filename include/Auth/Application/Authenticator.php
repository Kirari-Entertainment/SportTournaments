<?php namespace Robust\Auth;

abstract class Authenticator {
    private static string $maxLoginTime = "5 hour";
    private static int $maxLoginAttempts = 10;

    /**
     * @throws AuthException
     */
    public static function authenticateUserWithPassword(
        CredentialManager $credentialManager,

        string $username,
        string $password
    ) : ?AuthToken {
        $requester = $credentialManager->findUserByUsername($username);
        
        if (
            static::countCurrentUserLoginAttempts($credentialManager, $requester)
            > static::$maxLoginAttempts

        ) throw new AuthException( code: AuthException::$MAX_LOGIN_ATTMPTS_EXCEDEED );

        $validUntil = static::getDateTimePlusMaxLoginTime();
        
        if ( $requester->verifyPassword($password) ) {
            $token = $credentialManager->generateAuthTokenValidUntilFor($validUntil, $requester->id);

        } else {
            $token = null;

            $credentialManager->recordLoginAttempt(
                new LoginAttempt(
                    $username,
                    $password,
                    new \DateTime(),
                    $_SERVER['REMOTE_ADDR'] ?? null
                )
            );
        }

        return $token;
    }


    private static function getDateTimePlusMaxLoginTime() : \DateTime {
        return (new \DateTime('now')
            )->add(\DateInterval::createFromDateString(
                static::$maxLoginTime
            )
        );
    }


    private static function countCurrentUserLoginAttempts(
        CredentialManager $credentialManager,

        User $user
    ) : int {
        $loginAttempts = $credentialManager->retrieveLoginAttemptsForUser($user);
        $currentAttempts = 0;
        $renewTime = (new \DateTime())->sub(\DateInterval::createFromDateString('1 hour'));
        
        foreach ($loginAttempts as $attempt) {
            if ($attempt->dateTime > $renewTime) ++$currentAttempts;
        }

        return $currentAttempts;
    }


    public static function endAllSessions(
        CredentialManager $credentialManager,

        string $username
    ) {
        return;
    }


    public static function checkAuthorization(
        CredentialManager $credentialManager,

        string $authKey,

        array $authorizedRoles = [Roles::Administrator],
        array $authorizedUsersIds = []
    ) {
        $authorizedRoles[] = Roles::Administrator;

        $authToken = $credentialManager->recoverAuthToken($authKey);

        if (is_null($authToken))
            throw new AuthException(code: AuthException::$UNKNOWN_USER);

        if ($authToken->expiresAt < new \DateTime())
            throw new AuthException(code: AuthException::$EXPIRED_SESSION);

        if (is_null(
            $owner = $credentialManager->findUserById($authToken->ownerId)

        )) throw new AuthException(code: AuthException::$UNKNOWN_USER);

        if (!(static::checkIfAnyRoleMatches($owner->getRoles(), $authorizedRoles)) &&
            !in_array($owner->id, $authorizedUsersIds)

        ) throw new AuthException(code: AuthException::$FORBIDDEN_ACTION);

        return $authToken;
    }


    private static function checkIfAnyRoleMatches(array $userRoles, array $whitelist) : bool {
        foreach ($userRoles as $role)
            if (in_array($role, $whitelist, true)) return true;

        return false;
    }
}