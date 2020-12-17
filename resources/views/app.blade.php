<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/fontawesome.min.css">

    <title>{{ config('app.name', 'Jellyspamu Converter') }}</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">

    <!-- Scripts -->
    @routes
    <script src="{{ mix('js/app.js') }}" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.21.0/axios.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/parallax/3.1.0/parallax.min.js"></script>
   <script>
    var scene = document.getElementById('scene');
    var parallaxInstance = new Parallax(scene);
    </script>
</head>

<body id="scene" class="font-sans antialiased">
    <span id="layer-back" class="h-screen w-screen" data-depth="0.2"></span>
    <span id="layer-front" class="h-screen w-screen" >
        @inertia
    </span>
    
</body>

<style>
    #layer-back {
        background-image: url('bg.png');
        background-repeat: repeat;
        background-size: cover;
     
    }
    #layer-front {
        pointer-events: all !important;
    }
</style>
</html>

