<?php

namespace Source\Models;

class RecoveryPassword extends \CoffeeCode\DataLayer\DataLayer
{
    public function __construct()
    {
        parent::__construct("recovery_password", ["email", "code", "expire_time"]);
    }
}