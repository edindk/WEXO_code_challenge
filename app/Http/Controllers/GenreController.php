<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Session;

class GenreController extends Controller
{

    /**
     * @param $programType
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    function retrieve_all_byProgramType($programType)
    {
        $arr = [];

        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', 'https://feed.entertainment.tv.theplatform.eu/f/jGxigC/bb-all-pas?form=json&lang=da&byProgramType=' . $programType . '&fields=tags');
        $contents = $response->getBody()->getContents();

        $decoded = json_decode($contents, true);
        $entry = $decoded['entries'];

        foreach ($entry as $entryKey => $entryValue) {
            $tags = $entryValue['plprogram$tags'];
            if (is_array($tags)) {
                foreach ($tags as $tagKey => $tagValue) {
                    array_push($arr, $tagValue['plprogram$title']);
                }
            }
        }
        return $arr;
    }

    /**
     * @return array|mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    function combine_movies_and_series_for_all_genres()
    {
        $arrOfAllGenresObjs = [];

        if (Session::get('arrOfAllGenresObjs')) {
            $arrOfAllGenresObjs = Session::get('arrOfAllGenresObjs');
        } else {
            $arrOfMovieGenres = $this->retrieve_all_byProgramType('movie');
            $arrOfSeriesGenres = $this->retrieve_all_byProgramType('series');

            $mergedArr = array_merge($arrOfMovieGenres, $arrOfSeriesGenres);
            $uniqueArr = array_unique($mergedArr);

            $arrOfAllGenresObjs = $this->create_genre_objs(array_values($uniqueArr));

            Session::put('arrOfAllGenresObjs', $arrOfAllGenresObjs);
        }

        return $arrOfAllGenresObjs;
    }


    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    function show_homepage()
    {
        $arrOfAllGenres = $this->combine_movies_and_series_for_all_genres();
        return view('homepage', ['genres' => $arrOfAllGenres]);
    }

    /**
     * @param $arr
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    function create_genre_objs($arr)
    {
        $arrOfGenreObjs = [];
        for ($i = 0; $i < count($arr); $i++) {
            $client = new \GuzzleHttp\Client();
            $response = $client->request('GET', 'https://feed.entertainment.tv.theplatform.eu/f/jGxigC/bb-all-pas?form=json&byTags=genre:' . $arr[$i]);
            $contents = $response->getBody()->getContents();
            $decoded = json_decode($contents, true);

            if (isset($decoded['entryCount'])) {
                if ($decoded['entryCount'] != 0) {
                    $genreObj = new Genre([
                        'genreTitle' => $arr[$i],
                        'numbOfTitles' => $decoded['entryCount']
                    ]);
                    array_push($arrOfGenreObjs, $genreObj);
                }
            }
        }

        return $arrOfGenreObjs;
    }

}



