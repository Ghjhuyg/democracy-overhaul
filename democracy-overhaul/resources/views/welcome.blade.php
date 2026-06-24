<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/css/app.css', 'resources/js/app.jsx'])
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">

    <title>Democracy Overhaul - платформа для онлайн голосований о законопроектах.</title>

</head>
<body class="select-none">
    <div class="container">
        <div class="word democracy" id="democracy">DEMOCRACY</div>
        <div class="buttons" id="buttonsWrapper">
                <a href="{{ route('login') }}" class="btn btn-login">Вход</a>
                <a href="{{ route('register') }}" class="btn btn-register">Регистрация</a>
        </div>
        <div class="word overhaul" id="overhaul">OVERHAUL</div>
    </div>

    <script>
        const buttonsWrapper = document.getElementById('buttonsWrapper');
        const democracyWord = document.getElementById('democracy');
        const overhaulWord = document.getElementById('overhaul');

        if (buttonsWrapper && democracyWord && overhaulWord) {
            buttonsWrapper.addEventListener('mouseenter', () => {
                democracyWord.classList.add('move-up');
                overhaulWord.classList.add('move-down');
            });
            buttonsWrapper.addEventListener('mouseleave', () => {
                democracyWord.classList.remove('move-up');
                overhaulWord.classList.remove('move-down');
            });
        }
    </script>
</body>
</html>