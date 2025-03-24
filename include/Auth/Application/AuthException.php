<?php namespace Robust\Auth;

class AuthException extends \Exception {
    static $DUPLICATED_USER = 1;
    static $WRONG_PASSWORD = 2;
    static $WEAK_PASSWORD = 3;
    static $MAX_LOGIN_ATTMPTS_EXCEDEED = 4;
    static $UNKNOWN_USER = 5;
    static $FORBIDDEN_ACTION = 6;
    static $EXPIRED_SESSION = 7;
    static $NOT_AUTHENTICATED = 8;
}
