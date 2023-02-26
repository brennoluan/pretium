<?php

namespace Source\Models;

class Provider extends \CoffeeCode\DataLayer\DataLayer
{
    public function __construct()
    {
        parent::__construct("providers", ["social_reason", "cnpj", "email", "phonenumber"]);
    }
}