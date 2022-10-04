<?php


namespace App\OurEdu\TextChat\ServiceManager;


interface ChatServiceManagerInterface
{
    public function createRoom($name);
    public function joinUserToRoom($roomName ,$userName ,$socketID);
    public function listRooms();
    public function getRoomMessage($room);
}
