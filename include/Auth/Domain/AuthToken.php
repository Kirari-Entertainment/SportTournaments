<?php namespace Robust\Auth;

readonly class AuthToken {
    public function __construct(
        public string   $tokenString,
        public \DateTime $expiresAt,
        public int      $ownerId
    ) {}
}