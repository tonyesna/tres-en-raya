<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script type="text/javascript" src="{{ asset('js/game.js')  }}"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <!-- Styles -->

</head>
<body class="antialiased">

<p class ="title-game">TRES EN RAYA </p>
<div class="pannel-button">

    <div class="option-button">
        <a class="link-game" href="{{ route('game.type', ['id' => 1]) }}">1 VS 1</a>
        <a class="link-game" href="{{ route('game.type', ['id' => 2]) }}">1 VS MACHINE</a>
    </div>
</div>


</body>
</html>
