<?php namespace Robust\Auth;

interface CredentialManager {
    public function addUser(User $newUser) : void;
    public function findUserById(int $id) : ?User;
    public function findUserByUsername(string $username) : ?User;
    public function retrieveAllUsers() : array;
    public function updateUser(User $updated) : bool;
    public function deactivateUser(int $id) : void;

    public function generateAuthTokenValidUntilFor(\DateTime $expiration, int $ownerId) : ?AuthToken;
    public function recoverAuthToken(string $token) : ?AuthToken;
    public function dropAuthToken(AuthToken $newToken) : void;

    public function recordLoginAttempt(LoginAttempt $details) : void;
    public function retrieveLoginAttemptsForUser(User $user) : array;
}

