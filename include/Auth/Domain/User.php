<?php namespace Robust\Auth;

class User {
    public readonly int $id;
    private string $username;
    private bool $authenticated;
    private string $passwordHash;
    private array $roles = [];

    public function __construct(
        int $id,
        string $username,
        ?string $passwordHash,
        ?bool $authenticated = false,
        ?array $roles = [Roles::Any]
    ) {
        $this->id = $id;
        $this->setUsername($username);
        $this->passwordHash = $passwordHash;
        foreach ($roles as $role) { if ($role instanceof Roles) { $this->roles[] = $role; } }

        $this->authenticated = $authenticated;
    }

    private function setUsername($username) {
        $formattedUsername = trim($username);
        $this->username = $formattedUsername;
    }

    public function getUsername() : string { return $this->username; }

    public function setPassword($password) : void {
        $this->passwordHash = static::hashPassword($password);
    }

    public function getPasswordHash() : string {
        return $this->passwordHash;
    }

    public function verifyPassword(string $password) : bool {
        $this->authenticated = password_verify($password, $this->passwordHash);
        return $this->authenticated;
    }

    public function isAuthenticated() : bool {
        return $this->authenticated;
    }

    public function getRoles() : array
    { return $this->roles; }

    public static function hashPassword(string $password) : string {
        return password_hash($password, PASSWORD_DEFAULT);
    }

}