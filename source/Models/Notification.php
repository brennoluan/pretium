<?php

namespace Source\Models;

class Notification extends \CoffeeCode\DataLayer\DataLayer
{
    public function __construct()
    {
        parent::__construct("notifications", ["user_id", "message", "read_message"]);
    }

    public function findByUserId(int $id)
    {
        return (new Notification())->find("user_id=:user_id", "user_id=$id")->fetch(true);
    }

    public function user(): ?User
    {
        if($this->user_id){
            return ((new User())->findById($this->user_id));
        }
        return null;
    }
}