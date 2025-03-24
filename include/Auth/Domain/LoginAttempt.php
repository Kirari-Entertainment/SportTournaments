<?php namespace Robust\Auth;

readonly class LoginAttempt {
    public function __construct(
        public string   $username,
        public string   $password,
        public \DateTime $dateTime,
        public string   $ip
    ) { }
}