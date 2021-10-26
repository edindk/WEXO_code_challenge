<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use Illuminate\Routing\Controller;

class GenreController extends Controller
{
    /**
     * @param $programType
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    function retrieve_all_byProgramType($programType)
    {
        $arrOfGenres = [];

        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', 'https://feed.entertainment.tv.theplatform.eu/f/jGxigC/bb-all-pas?form=json&lang=da&byProgramType=' . $programType);
        $contents = $response->getBody()->getContents();

        $decoded = json_decode($contents, true);
        $entry = $decoded['entries'];

        foreach ($entry as $entryKey => $entryValue) {
            $tags = $entryValue['plprogram$tags'];
            if (is_array($tags)) {
                foreach ($tags as $tagKey => $tagValue) {
                    array_push($arrOfGenres, $tagValue['plprogram$title']);
                }
            }
        }
        return $arrOfGenres;
    }

    /**
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    function combine_movies_and_series()
    {
        $arrOfMovieGenres = $this->retrieve_all_byProgramType('movie');
        $arrOfSeriesGenres = $this->retrieve_all_byProgramType('series');

        $mergedArr = array_merge($arrOfMovieGenres, $arrOfSeriesGenres);
        $uniqueArr = array_unique($mergedArr);

        return array_values($uniqueArr);
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    function create_genre_obj()
    {
        $arrOfGenres = $this->combine_movies_and_series();
        $arrOfGenreObj = [];

        for ($i = 0; $i < count($arrOfGenres); $i++) {
            $client = new \GuzzleHttp\Client();
            $response = $client->request('GET', 'https://feed.entertainment.tv.theplatform.eu/f/jGxigC/bb-all-pas?form=json&byTags=genre:' . $arrOfGenres[$i]);
            $contents = $response->getBody()->getContents();
            $decoded = json_decode($contents, true);

            if (isset($decoded['entryCount'])) {
                if ($decoded['entryCount'] != 0) {
                    $genreObj = new Genre([
                        'genreTitle' => $arrOfGenres[$i],
                        'numbOfTitles' => $decoded['entryCount']
                    ]);
                    array_push($arrOfGenreObj, $genreObj);
                }
            }
        }
        return view('homepage', ['genres' => $arrOfGenreObj]);
    }

}



