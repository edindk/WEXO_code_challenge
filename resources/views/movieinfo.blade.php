<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @include('layout.header')
    <title></title>
</head>
<body>
@include('layout.navbar')
<div class="container-fluid"
     style="background-size: cover; background-repeat: no-repeat; background-image: url({{$movie->bgCover}});">
    <div class="backdrop__gradient"></div>
    <div style="width: 100%; height: 750px">
        <div class="row h-100 d-flex justify-content-center align-items-center">
            <div class="col-lg-3 text-center">
                <img id="img" src="{{$movie->img}}" style="width: 250px; height: 364px">
            </div>
            <div class="col-lg-5">
                <h3 class="mb-0 mt-2">{{$movie->movieTitle}}</h3>
                <p>Genre: {{$movie->genre[0]['plprogram$title']}} / Udgivelsesår: {{$movie->releaseYear}}</p>
                <h4>Beskrivelse</h4>
                <p>{{$movie->description}}</p>
                <h4>Medvirkende</h4>
                <div class="row">
                    @foreach($movie->credits as $p)

                        <div class="col-sm-2">
                            <p class="mb-0">{{$p['plprogram$personName']}}</p>
                            <p>{{$p['plprogram$creditType']}}</p>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="offset-4"></div>
        </div>
    </div>
</div>
</body>
</html>
<style>
    h3, h4, p {
        color: #ffff;
    }

    p {
        font-size: 15px;
    }
</style>
