<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Session;
use function PHPUnit\Framework\isEmpty;

class MovieAndSeriesController extends Controller
{
    /**
     * @param string $genre
     * @param $programType
     * @param string $range
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    function get_movies_and_series_by_genre_and_range($genre = '', $programType, $range = '')
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', 'https://feed.entertainment.tv.theplatform.eu/f/jGxigC/bb-all-pas?form=json&' . $range . '&byTags=genre:' . $genre . '&byProgramType=' . $programType);
        $contents = $response->getBody()->getContents();

        $decoded = json_decode($contents, true);
        $entry = $decoded['entries'];

        return $this->create_movie_objs($entry);
    }


    /**
     * @param $entry
     * @return array
     */
    function create_movie_objs($entry)
    {
        $arrOfMovieObjs = [];

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
                'genre' => $entryValue['plprogram$tags'],
                'credits' => $entryValue['plprogram$credits']
            ]);

            array_push($arrOfMovieObjs, $movieObj);
        }
        return $arrOfMovieObjs;
    }


    /**
     * @param string $genre
     * @param string $range
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    function combine_movies_and_series($genre = '', $range = '')
    {
        $arrOfMovies = $this->get_movies_and_series_by_genre_and_range($genre, 'movie', $range);
        $arrOfSeries = $this->get_movies_and_series_by_genre_and_range($genre, 'series', $range);

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
        $dummyImg = 'https://dummyimage.com/250x364/000/fff.jpg&text=Not+found';
        $client = new \GuzzleHttp\Client();

        if (empty($thumbnailArr)) {
            return $dummyImg;
        } else {
            foreach ($thumbnailArr as $thumbnailKey => $thumbnailValue) {
                $imgUrl = $thumbnailValue['plprogram$url'];
                try {
                    $client->request('GET', $imgUrl);
                    return $imgUrl;
                } catch (\Exception $e) {
                    return $dummyImg;
                }
            }
        }

    }


    /**
     * @param $genre
     * @param string $range
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    function show_by_genre($genre, $range = '')
    {
        $arrOfMovieObjs = $this->combine_movies_and_series($genre, $range);
        return view('showbygenre', ['movies' => $arrOfMovieObjs, 'genre' => $genre]);
    }

    /**
     * @param $id
     * @return Movie
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    function get_single_movie_by_id($id)
    {
        $client = new \GuzzleHttp\Client();

        $response = $client->request('GET', 'https://feed.entertainment.tv.theplatform.eu/f/jGxigC/bb-all-pas/' . $id . '?form=json');
        $contents = $response->getBody()->getContents();

        $decoded = json_decode($contents, true);

        return $this->create_single_movie_obj($decoded);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    function show_movie_info($id)
    {
        $movie = $this->get_single_movie_by_id($id);
        return view('movieinfo', ['movie' => $movie]);
    }

    /**
     * @param $entry
     * @return Movie
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    function create_single_movie_obj($entry)
    {
        $thumbnailArr = $entry['plprogram$thumbnails'];
        $img = $this->get_image($thumbnailArr);
        $bgCover = $this->get_cover($thumbnailArr);
        $id = preg_replace("/[^0-9]/", "", $entry['id']);

        return new Movie([
            'img' => $img,
            'id' => $id,
            'bgCover' => $bgCover,
            'movieTitle' => $entry['title'],
            'description' => $entry['description'],
            'releaseYear' => $entry['plprogram$year'],
            'genre' => $entry['plprogram$tags'],
            'credits' => $entry['plprogram$credits']
        ]);
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

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    function show_wishlist()
    {
        $wishlist = Session::get('wishlist');
        return view('wishlist', ['wishlist' => $wishlist]);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    function add_to_wishlist($id)
    {
        $movie = $this->get_single_movie_by_id($id);

        $wishlist = [];

        if (Session::get('wishlist')) {
            $wishlist = Session::get('wishlist');
            array_push($wishlist, $movie);
            Session::forget('wishlist');
        } else {
            array_push($wishlist, $movie);
        }
        Session::put('wishlist', $wishlist);

        return view('wishlist', ['wishlist' => $wishlist]);
    }
}
