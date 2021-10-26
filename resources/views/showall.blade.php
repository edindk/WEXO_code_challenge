<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @include('layout.header')
</head>
<body>
@include('layout.navbar')
<div class="container">
    <div class="row mb-2 mt-2">
        <div class="col col-lg-6 justify-content-start">
            <h1>{{$genre}}</h1>
        </div>
    </div>
    <div class="row justify-content-md-center">
        @foreach($movies as $movie)
            <div class="col col-lg-3 text-center">
                <img src="{{$movie->img}}" style="width: 250px; height: 364px">
                <p class="">{{$movie->movieTitle}}</p>
                <div class="mb-3">
                    <a href="http://127.0.0.1:8000/movie/{{$movie->id}}">
                        <button type="button" class="btn btn-primary w-15">Vis mere</button>
                    </a>
                    <button type="button" class="btn btn-success w-15">+ Ønskeliste</button>
                </div>
            </div>
        @endforeach
    </div>
</div>
</body>
</html>
