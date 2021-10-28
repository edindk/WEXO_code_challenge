<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @include('layout.header')
    <title>Forside</title>
</head>
<body>
@include('layout.navbar')
<div class="container">
    <div class="jumbotron mt-3">
        <h1 class="display-4">WEXO - Code Challenge</h1>
        <p class="lead">A website that tries to sell movies online</p>
        <hr class="my-4">
        <div class="row justify-content-start">
            <h3>Genrer</h3>
        </div>
        <div class="row justify-content-md-center">
            @foreach($genres as $genre)
                <div class="col">
                    <a href="http://127.0.0.1:8000/movies-and-series/{{$genre->genreTitle}}/range=1-20">
                        <button type="button" class="btn btn-primary">{{$genre->genreTitle}} ({{$genre->numbOfTitles}})</button>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</div>
</body>
</html>
