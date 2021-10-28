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
            <h1>Ønskeliste</h1>
        </div>
    </div>
    <div class="row justify-content-md-center">
        <ul class="list-group w-100">
            @foreach($wishlist as $movie)
                <div><a href="http://127.0.0.1:8000/movie/{{$movie->id}}"><li class="list-group-item">{{$movie->movieTitle}}</li></a></div>
            @endforeach
        </ul>
    </div>
</div>
</body>
</html>
