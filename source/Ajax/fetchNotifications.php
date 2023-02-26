<?php

require __DIR__ . "/../../vendor/autoload.php";

use Source\Models\Notification;

if(isset($_POST['user_id'])){

    $notifications = (new \Source\Models\Notification())->find("user_id=:user_id AND read_message=0",
        "user_id={$_POST['user_id']}")->limit(5)->order("created_at DESC")->fetch(true);

    $output = [];
    if($notifications){
        foreach($notifications as $notification){
            $output[]= array('message'=>$notification->message);
        }
    }
    echo json_encode($output);
}