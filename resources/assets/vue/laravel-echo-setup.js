import Echo from "laravel-echo";

window.Echo = new Echo({
    broadcaster: 'socket.io',
    host: window.laravel_echo_host  + window.laravel_echo_port,
});
