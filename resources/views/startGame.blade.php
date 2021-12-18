<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script type="text/javascript" src="{{ asset('js/game.js')  }}"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
</head>
<body class="antialiased">
<a class ="button-back" href="{{ route('menu') }}">Volver al Menú</a>
<div class="user">

    <div class="name-user">
        <span id="player1"><i class='fa fa-user-secret'></i> player 1 => X</span>
        <span id="player2"><i class='fa fa-android'></i> player 2 => O</span>
    </div>
</div>
<p class="player-turn">Es el turno de <span id="myturn">{{$turn}}</span></p>
<button class="new-game"
        onclick="newGame({{$type_game}})">Nueva Partida
</button>
<div class="game">
    <div class="board">
        @for ($i = 0; $i < $size; $i++)
            <div class="board-row">
                <button class="sqare position_{{($i*$size) + 1 -1}}"
                        onclick="sendDataGame({{($i*$size) + 1 -1}},{{$type_game}})">{{$board[($i*$size) + 1 -1]}}</button>
                <button class="sqare position_{{($i*$size) + 2 -1}}"
                        onclick="sendDataGame({{($i*$size) + 2 -1}},{{$type_game}})">{{$board[($i*$size) + 2 -1]}}</button>
                <button class="sqare position_{{($i*$size) + 3 -1}}"
                        onclick="sendDataGame({{($i*$size) + 3 -1}},{{$type_game}})">{{$board[($i*$size) + 3 -1]}}</button>
            </div>
        @endfor
    </div>
</div>

</body>
</html>
