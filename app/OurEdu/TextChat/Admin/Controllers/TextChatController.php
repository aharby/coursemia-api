<?php

namespace App\OurEdu\TextChat\Admin\Controllers;

use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\BaseApp\Enums\ParentEnum;
use App\OurEdu\TextChat\ServiceManager\ChatServiceManagerInterface;
use Illuminate\Http\Request;

class TextChatController extends BaseController
{
    private $chatServiceManager;
    private $module;
    private $parent;

    public function __construct(ChatServiceManagerInterface $chatServiceManager)
    {
        $this->chatServiceManager = $chatServiceManager;
        $this->module = 'TextChat';
        $this->parent = ParentEnum::ADMIN;
    }

    public function index() {

        $data = $this->chatServiceManager->listRooms();
        $data = $data ? $data : [];
        $data['page_title'] = trans('textChat.List Rooms');
        $data['breadcrumb'] = '';
        return view($this->parent . '.' . $this->module . '.index', $data);
    }

    public function startSocket($name) {

        return view('chat' , compact('name') );
    }

    public function listRooms() {

    }

    public function createRoom(Request $request) {

        $res = $this->chatServiceManager->createRoom($request->name);
        if ($res) {
            flash()->success(trans("textChat.Room Created Successfully"));
        } else {
            flash()->error(trans("textChat.Something went wrong"));
        }
        return redirect()->back();
    }

    public function joinRoom(Request $request) {

        $res = $this->chatServiceManager->joinUserToRoom($request->room , $request->userName , $request->socketID);
        if ($res) {
            flash()->success(trans("textChat.Room Created Successfully"));
        } else {
            flash()->error(trans("textChat.Something went wrong"));
        }
        return redirect()->back();
    }
    public function roomMessages($room) {
        $data = $this->chatServiceManager->getRoomMessage($room );
        $data = $data ? $data : [];
        if (isset($data['messages'])) {
            $data['messages'] = $data['messages'][0];
        }
        $data['page_title'] = trans('textChat.List Rooms');
        $data['breadcrumb'] = '';
        $data['room'] = $room;
        return view($this->parent . '.' . $this->module . '.view', $data);
    }

    public function chatRoom($name , $room) {
        $data['name'] = $name;
        $data['room'] = $room;
        return view($this->parent . '.' . $this->module . '.ChatRoom', $data);
    }

    public function addUserToRoom(Request $request) {
        return redirect()->route('admin.textChat.chatRoom' , ['name' => $request->name , 'room' => $request->room]  );
    }
}
