@extends('layouts.admin_layout')
@push('title')
    {{ @$page_title }}
@endpush
@section('title', @$page_title)

@section('content')
    <div class="row">
        <div id="container">
            <div id="main-container">

            </div>

            <!-- Chat Controller -->
            <div id="controller">
                <textarea id="textbox" class="form-control" rows="2" placeholder="Enter your message here"></textarea>
                <div class="checkbox"><label><input checked type="checkbox" id="enter"/>Send on enter</label>
                    <button id="send" class="btn btn-primary">Send</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('css')
    <style>
        #container {
        width: 360px;
        height: 500px;
        overflow-y: auto;
        margin: 0px auto;
        background-color: rgb(228, 241, 254);
        border-radius: 10px;
        padding: 20px 20px 20px 20px;
        box-shadow: 10px 10px 15px 0 rgba(0, 0, 0, 0.3);
        }

        #main-container {
        width: 320px;
        height: 350px;
        overflow-y: auto;
        margin: 0px auto;
        background-color: #fff;
        border-radius: 4px;
        padding: 10px 20px 10px 20px;
        color: #000;
        }

        #controller {
        width: 320px;
        height: 100px;
        margin: 0px auto;
        margin-top: 7px;
        }

        .form-control {
        border: 2px black;
        }

        textarea {
        margin-top: 7px;
        resize: none;
        }

        #send {
        float: right;
        }
    </style>
@endpush

@push('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.3.0/socket.io.js"></script>

    <script>
        let url = 'ws://{{env('APP_DOMAIN')}}:8000/chat-service'
        let userName = '{{isset($name) ? $name : "unnammed"}}'
        let query = `userName=${userName}`
        let opts = { query }

        // Connect to a server.
        let socket = io.connect(url, opts)

        // Rooms messages handler (own messages are here too).
        socket.on('roomMessage', (room, msg) => {
        console.log(msg)
          console.log(`from socket ${msg.author}: ${msg.textMessage}`)

          makeMessage( msg.author , msg.author , msg.textMessage , msg.author)
        })

        // Auth success handler.
        socket.on('loginConfirmed', userName => {
           makeMessage( "Login" , "Login" , socket.id +"\n"+ userName, "Login")
        })

        // Auth error handler.
        socket.on('loginRejected', error => {
          console.error(error)
        })
    </script>

    <script>


function makeMessage(who, usernameIn, messageIn, userID)
{
var chatbox= document.getElementById("main-container");

	var newmessageblob = document.createElement("li")
	newmessageblob.className = who
	var entete = document.createElement("div")
	entete.className = "entete"
	newmessageblob.appendChild(entete)

	var timestamp = document.createElement("h3")
	var username = document.createElement("h2")
	entete.appendChild(timestamp)
	timestamp.innerText =  new Date().toLocaleString();
	entete.appendChild(username)
	username.innerText = usernameIn;

	var triangle = document.createElement("div")
	triangle.className = "triangle"

	var message = document.createElement("div")
	message.className = "message"
	message.innerText = messageIn

	newmessageblob.appendChild(triangle)
	newmessageblob.appendChild(message)
	chatbox.appendChild(newmessageblob)

}
function sendNetworkMessage(message) {

        socket.emit('roomMessage', '{{$room}}' , { textMessage: message })
}
document.getElementById("send").onclick=function() {
    var input= document.getElementById("textbox");

    sendNetworkMessage(input.value);
    input.value = "";
};

</script>
@endpush
