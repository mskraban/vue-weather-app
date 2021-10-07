<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Weather app</title>
    <link
        rel="stylesheet"
        href="https://unpkg.com/swiper@7/swiper-bundle.min.css"
    />
    <link rel="stylesheet" href="{{ asset('css/main.css') }}"/>
</head>
<body>
<div id="app" class="container">
    <weather-app></weather-app>
</div>

<script src="https://unpkg.com/swiper@7/swiper-bundle.min.js"></script>
<script src="{{ asset('js/app.js') }}" defer></script>
</body>
</html>
