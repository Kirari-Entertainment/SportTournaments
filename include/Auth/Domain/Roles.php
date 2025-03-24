<?php namespace Robust\Auth;

enum Roles: string
{
    case Administrator = 'Administrator';
    case Manager = 'Manager';
    case Operator = 'Operator';
    case Any = 'Guest';
}