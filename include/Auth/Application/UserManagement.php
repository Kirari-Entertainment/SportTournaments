<?php namespace Robust\Auth;

use Robust\Boilerplate\IdGenerator;

abstract class UserManagement {
    public static function registerUser(
        CredentialManager $credentialManager,
        IdGenerator $idGenerator,

        string $username,
        string $password
    ) {
        // No se debe registrar un usuario existente
        if ($credentialManager->findUserByUsername($username))
        throw new AuthException(code: AuthException::$DUPLICATED_USER);

        $newUserId = $idGenerator->nextForClass(User::class);
        $hashedPassword = User::hashPassword($password);

        $user = new User($newUserId, $username, $hashedPassword);

        $credentialManager->addUser($user);

        return true;
    }


    public static function listUsers(
        CredentialManager $credentialManager
    ) : array {
        $allUsersPlainData = [];

        $allUserObjects = $credentialManager->retrieveAllUsers();

        foreach ($allUserObjects as $userInstance) {
            $allUsersPlainData[] = [
                "username" => $userInstance->getUsername()
            ];
        }

        return $allUsersPlainData;
    }
}