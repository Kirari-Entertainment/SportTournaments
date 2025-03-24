<?php namespace Robust\Boilerplate\HTTP;

enum RCODES: int {
    case OK = 200;
    case Created = 201;
    case Accepted = 202;
    case BadRequest = 400;
    case Unauthorized = 401;
    case Forbidden = 403;
    case NotFound = 404;
    case UnallowedMethod = 405;
    case Conflict = 409;
    case TooManyRequests = 429;
    case InternalError = 500;
    case Unimplemented = 501;
    case Unavailable = 503;
}