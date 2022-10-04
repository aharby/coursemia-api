<?php


namespace App\OurEdu\TextChat\ServiceManager;


use Zttp\Zttp;

class ChatServiceManager implements ChatServiceManagerInterface
{
    private $base;

    public function __construct()
    {
        $this->base = "http://127.0.0.1:3000/api/v1/";
    }

    public function createRoom($name) {

        $response = Zttp::post( $this->base . "create-room" , [
            'name' => $name
        ]);

        return $response->isOk();
    }
    public function joinUserToRoom($roomName ,$userName ,$socketID) {
        $response = Zttp::post( $this->base . "join-room" , [
            'roomName' =>  $roomName,
            'userName' => $userName ,
            'socketID' => $socketID ,
        ]);

        return $response->isOk();

    }
    public function listRooms() {
        $response = Zttp::get( $this->base . "list-rooms");

        if ($response->isOk()) {
            return $response->json();
        } else {
            return $response->isOk();
        }
    }
    public function getRoomMessage($room) {
        $response = Zttp::get( $this->base . "room-messages" . "/" . $room);

        if ($response->isOk()) {
            return $response->json();
        } else {
            return $response->isOk();
        }
    }

}
