<?php namespace Robust\Auth;

use Robust\Boilerplate\Infrastructure\RepositoryFromDB;

class UserRepositoryFromDB extends RepositoryFromDB {
    private string $userTableName;
    private string $userRolesTableName;
    private string $loginAttemptTableName;

    public function __construct(\PDO $_dbConnection = null) {
        $this->userTableName = stripslashes(User::class);
        $this->userRolesTableName = stripslashes(User::class).'Roles';
        $this->loginAttemptTableName = stripslashes(LoginAttempt::class);

        parent::__construct($_dbConnection);

        $queriesCreateTables = [];

        $queriesCreateTables[] = "CREATE TABLE IF NOT EXISTS $this->userTableName (
            id              INT,
            username        VARCHAR(255),
            passwordHash    VARCHAR(255),
            
            isActive        BOOLEAN,

            PRIMARY KEY (id),
            UNIQUE (username)
        )";

        $queriesCreateTables[] = "CREATE TABLE IF NOT EXISTS $this->userRolesTableName (
            userId          INT,
            role            VARCHAR(255),
    
            UNIQUE (userId, role)
        )";

        $queriesCreateTables[] = "CREATE TABLE IF NOT EXISTS `$this->loginAttemptTableName` (
            username        VARCHAR(255),
            password        VARCHAR(255),
            datetime        TIMESTAMP,
            ipAddress       VARCHAR(15)
        )";

        foreach ($queriesCreateTables as $query) {
            $stmt = $this->dbConnection->prepare($query);
            $stmt->execute();
        }
    }


    public function addUser(User $newUser): void {
        $queryCreateUser = "INSERT INTO $this->userTableName (
           id, username, passwordHash, isActive
           ) VALUES (:id, :username, :passwordHash, True)";

        $stmt = $this->dbConnection->prepare($queryCreateUser);

        $stmt->bindValue(':id', $newUser->id, \PDO::PARAM_INT);
        $stmt->bindValue(':username', $newUser->getUsername(), \PDO::PARAM_STR);
        $stmt->bindValue(':passwordHash', $newUser->getPasswordHash(), \PDO::PARAM_STR);

        $stmt->execute();

        $queryAddRole = "INSERT INTO $this->userRolesTableName (userId, role) VALUES (:id, :role) ON DUPLICATE KEY UPDATE role = :role";

        foreach ($newUser->getRoles() as $role) {
            $stmt = $this->dbConnection->prepare($queryAddRole);

            $stmt->bindValue(':id', $newUser->id, \PDO::PARAM_INT);
            $stmt->bindValue(':role', $role->value);

            $stmt->execute();
        }

    }


    public function findUserById(int $id) : ?User {
        $foundUser = null;

        $queryFindUserById = "SELECT * FROM $this->userTableName WHERE id = :id";
        $queryRetrieveUserRoles = "SELECT role FROM $this->userRolesTableName WHERE userId = :id";

        $stmtFindUser = $this->dbConnection->prepare($queryFindUserById);
        $stmtFindUser->bindValue(':id', $id, \PDO::PARAM_INT);

        $stmtFindUser->execute();

        $userData = $stmtFindUser->fetch(\PDO::FETCH_ASSOC);

        if ($userData) {
            $stmtRetrieveUserRoles = $this->dbConnection->prepare($queryRetrieveUserRoles);
            $stmtRetrieveUserRoles->bindValue(':id', $id, \PDO::PARAM_INT);
            $stmtRetrieveUserRoles->execute();

            $userRolesPlain = $stmtRetrieveUserRoles->fetchAll(\PDO::FETCH_COLUMN);
            $userRoles = [];
            foreach ($userRolesPlain as $userRoleName) { $userRoles[] = Roles::tryFrom($userRoleName); }
            $foundUser = new User(
                $userData['id'],
                $userData['username'],
                $userData['passwordHash'],
                false,
                $userRoles
            );
        }

        return $foundUser;
    }


    public function findUserByUsername(string $username) : ?User {
        $foundUser = null;

        $queryFindUserByUsername = "SELECT * FROM $this->userTableName WHERE username = :username";
        $queryRetrieveUserRoles = "SELECT role FROM $this->userRolesTableName WHERE userId = :id";

        $stmtFindUser = $this->dbConnection->prepare($queryFindUserByUsername);
        $stmtFindUser->bindValue(':username', $username, \PDO::PARAM_STR);

        $stmtFindUser->execute();

        $userData = $stmtFindUser->fetch(\PDO::FETCH_ASSOC);

        if ($userData) {
            $stmtRetrieveUserRoles = $this->dbConnection->prepare($queryRetrieveUserRoles);
            $stmtRetrieveUserRoles->bindValue(':id', $userData['id'], \PDO::PARAM_INT);
            $stmtRetrieveUserRoles->execute();

            $userRolesPlain = $stmtRetrieveUserRoles->fetchAll(\PDO::FETCH_COLUMN);
            $userRoles = [];
            foreach ($userRolesPlain as $userRoleName) { $userRoles[] = Roles::tryFrom($userRoleName); }

            $foundUser = new User(
                $userData['id'],
                $userData['username'],
                $userData['passwordHash'],
                false,
                $userRoles
            );
        }

        return $foundUser;
    }


    public function retrieveAllUsers() : array {
        $allUsers = [];

        $queryRetrieveAllUsers = "SELECT * FROM $this->userTableName WHERE isActive = True";
        $queryRetrieveUserRoles = "SELECT role FROM $this->userRolesTableName WHERE userId = :id";

        $stmtRetrieveAllUsers = $this->dbConnection->prepare($queryRetrieveAllUsers);

        $stmtRetrieveAllUsers->execute();

        $allUsersPlainData = $stmtRetrieveAllUsers->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($allUsersPlainData as $userData) {
            $stmtRetrieveUserRoles = $this->dbConnection->prepare($queryRetrieveUserRoles);
            $stmtRetrieveUserRoles->bindValue(':id', $userData['id'], \PDO::PARAM_INT);
            $stmtRetrieveUserRoles->execute();

            $userRolesPlain = $stmtRetrieveUserRoles->fetchAll(\PDO::FETCH_COLUMN);
            $userRoles = [];
            foreach ($userRolesPlain as $userRoleName) { $userRoles[] = Roles::tryFrom($userRoleName); }

            $allUsers[] = new User(
                $userData['id'],
                $userData['username'],
                $userData['passwordHash'],
                false
            );

        }

        return $allUsers;
    }


    public function updateUser(User $updated) : bool {
        $updateDone = false;

        $oldUserData = $this->findUserById($updated->id);
        if ($oldUserData) {
            $this->deleteUser($updated->id);
            $this->addUser($updated);

            $updateDone = true;
        }

        return $updateDone;
    }

    public function deactivateUser(int $userId) : void {
        $query = "UPDATE $this->userTableName SET isActive = False WHERE id = :id";

        $stmt = $this->dbConnection->prepare($query);

        $stmt->bindValue(':id', $userId, \PDO::PARAM_INT);

        $stmt->execute();
    }


    private function deleteUser(int $userId) {
        $queryDeleteUser = "DELETE FROM $this->userTableName WHERE id = :id";
        $queryDeleteRoles = "DELETE FROM $this->userRolesTableName WHERE userId = :id";

        $stmt = $this->dbConnection->prepare($queryDeleteUser);
        $stmt->bindValue(':id', $userId, \PDO::PARAM_INT);
        $stmt->execute();

        $stmt = $this->dbConnection->prepare($queryDeleteRoles);
        $stmt->bindValue(':id', $userId, \PDO::PARAM_INT);
        $stmt->execute();
    }


    public function recordLoginAttempt(LoginAttempt $details) : void {
        $query = "INSERT INTO $this->loginAttemptTableName (
                   username,
                   password,
                   datetime,
                   ipAddress
        ) VALUES (:username, :password, :datetime, :ipAddress);";

        $stmt = $this->dbConnection->prepare($query);

        $stmt->bindValue(':username', $details->username);
        $stmt->bindValue(':password', $details->password);
        $stmt->bindValue(':datetime', $details->dateTime->format('Y-m-d H:i:s'));
        $stmt->bindValue(':ipAddress', $details->ip);

        $stmt->execute();
    }


    public function retrieveLoginAttemptsForUser(User $user): array {
        $query = "SELECT * FROM $this->loginAttemptTableName WHERE username = :username";

        $stmt = $this->dbConnection->prepare($query);

        $stmt->bindValue(':username', $user->getUsername(), \PDO::PARAM_STR);

        $stmt->execute();

        $loginAttemptsPlainData = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $loginAttempts = [];

        foreach ($loginAttemptsPlainData as $loginAttempt) {
            $loginAttempts[] = new LoginAttempt(
                $loginAttempt['username'],
                $loginAttempt['password'],
                new \DateTime($loginAttempt['datetime']),
                $loginAttempt['ipAddress']
            );
        };

        return $loginAttempts;
    }
}