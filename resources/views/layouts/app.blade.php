<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Task Parser v1.0.0"></meta>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>

    <title>Task Parser v1.0.0</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
    <link href="/css/app.css" rel="stylesheet" type="text/css">
</head>

<body>
    <section class="section" id="vue">
        <div class="container">
            @yield('content')
        </div>
    </section>

    <script src="/js/app.js"></script>
</body>

</html>
