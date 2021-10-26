<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Session;

class MovieAndSeriesController extends Controller
{
    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    function show_by_genre($genre)
    {
        $allMovies = $this->combine_movies_and_series($genre, '');
        Session::put('moviesAndSeriesByGenre', $allMovies);

        $arr = $this->combine_movies_and_series($genre, '&range=1-20');

        return view('showbygenre', ['movies' => $arr, 'genre' => $genre]);
    }

    /**
     * @param $genre
     * @param $programType
     * @param $range
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    function retrieve_by_genre($genre, $programType, $range)
    {
        $arr = [];

        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', 'https://feed.entertainment.tv.theplatform.eu/f/jGxigC/bb-all-pas?form=json' . $range . '&byTags=genre:' . $genre . '&byYear=2017&byProgramType=' . $programType);
        $contents = $response->getBody()->getContents();

        $decoded = json_decode($contents, true);
        $entry = $decoded['entries'];

        foreach ($entry as $entryKey => $entryValue) {
            $thumbnailArr = $entryValue['plprogram$thumbnails'];
            $img = $this->get_image($thumbnailArr);
            $bgCover = $this->get_cover($thumbnailArr);
            $id = preg_replace("/[^0-9]/", "", $entryValue['id']);

            $movieObj = new Movie([
                'img' => $img,
                'id' => $id,
                'bgCover' => $bgCover,
                'movieTitle' => $entryValue['title'],
                'description' => $entryValue['description'],
                'releaseYear' => $entryValue['plprogram$year'],
                'genre' => $genre,
                'credits' => $entryValue['plprogram$credits']
            ]);
            array_push($arr, $movieObj);
        }
        return $arr;
    }

    /**
     * @param $genre
     * @param $range
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    function combine_movies_and_series($genre, $range)
    {
        $arrOfMovies = $this->retrieve_by_genre($genre, 'movie', $range);
        $arrOfSeries = $this->retrieve_by_genre($genre, 'series', $range);

        $mergedArr = array_merge($arrOfMovies, $arrOfSeries);
        $uniqueArr = array_unique($mergedArr);

        return array_values($uniqueArr);
    }

    /**
     * @param $thumbnailArr
     * @return string|void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    function get_cover($thumbnailArr)
    {
        $client = new \GuzzleHttp\Client();

        foreach ($thumbnailArr as $thumbnailArrKey => $value) {
            if ($value['plprogram$width'] > 1000 && $value['plprogram$height'] > 600) {
                try {
                    $imgUrl = $value['plprogram$url'];
                    $client->request('GET', $imgUrl);
                    return $imgUrl;
                } catch (\Exception $e) {
                    return 'https://dummyimage.com/1080x700/000/fff.jpg&text=Not+found';
                }
            }
        }
    }

    /**
     * @param $thumbnailArr
     * @return string|void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    function get_image($thumbnailArr)
    {
        $client = new \GuzzleHttp\Client();
        foreach ($thumbnailArr as $thumbnailKey => $thumbnailValue) {
            try {
                $imgUrl = $thumbnailValue['plprogram$url'];
                $client->request('GET', $imgUrl);
                return $imgUrl;
            } catch (\Exception $e) {
                return 'https://dummyimage.com/250x364/000/fff.jpg&text=Not+found';
            }
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    function show_movie($id)
    {
        $arr = Session::get('moviesAndSeriesByGenre');
        $movie = null;

        foreach ($arr as $movieKey => $movieValue) {
            if ($movieValue->id == $id) {
                $movie = $movieValue;
                break;
            }
        }
        return view('movieinfo', ['movie' => $movie]);
    }

    /**
     * @param $genre
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    function show_all($genre)
    {
        $arr = Session::get('moviesAndSeriesByGenre');
        return view('showall', ['movies' => $arr, 'genre' => $genre]);
    }
}
