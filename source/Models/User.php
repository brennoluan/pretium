<?php

namespace Source\Models;

class User extends \CoffeeCode\DataLayer\DataLayer
{
    public function __construct()
    {
        parent::__construct("users", []);
    }

    public function findByProvider(int $provider_id): User
    {
        return (new User())->find("provider_id=:provider_id", "provider_id=$provider_id")->fetch();
    }

    public function provider(): ?Provider
    {
        if($this->provider_id){
            return ((new Provider())->findById($this->provider_id));
        }
        return null;
    }
}