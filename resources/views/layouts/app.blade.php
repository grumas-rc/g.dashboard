<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <title>Gaia stats</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/js/app.js', 'resources/sass/app.scss'])
</head>
<body>
<header class="container-fluid">
    <nav class="navbar navbar-expand-md">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('stats') }}">
                <img alt="gaia-logo" class="logo-img" src="/img/logo.png"/>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarScroll" aria-controls="navbarScroll" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse text-right" id="navbarScroll">
                <ul class="navbar-nav my-2 my-lg-0 navbar-nav-scroll ms-auto" style="--bs-scroll-height: 100px;">
                    <li class="nav-item">
                        <a href="https://www.gaianet.ai" class="nav-link">Gaianet.ai</a>
                    </li>
                    <li class="nav-item">
                        <a href="https://docs.gaianet.ai/getting-started/quick-start" class="nav-link">Install Node</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
{{--    <div class="col-3">--}}
{{--        <img alt="gaia-logo" class="logo-img" src="/img/logo.png"/>--}}
{{--    </div>--}}
{{--    <div class="col-9">--}}
{{--        <a href="#">Install Node</a>--}}
{{--        <a href="#">www.gaianet.ai</a>--}}
{{--    </div>--}}
</header>
<main class="container-fluid">
    @yield('content')
</main>
</body>
</html>
