<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 21.2.17.
 * Time: 14.47
 */

namespace App\Controllers;


use App\Handlers\MovieHandlerInterface;
use Symfony\Component\HttpFoundation\Request;

class MovieController
{
    /*
     * Ova promenljiva je objekat u kojem su implementirane
     * funkcije koje obradjuju zahtev i definisu biznis logiku.
     * Funkcije implementirane u toj klasi vrse proveru parametara,
     * nacin na koji ce podaci biti izvuceni iz baze podataka,
     * kao i nacin na koji ce biti prezentovani.
     */
    protected $movieHandler;

    public function __construct(MovieHandlerInterface $movieHandler)
    {
        $this->movieHandler = $movieHandler;
    }

    /*
     * Poziva se na POST putanji /movies/{movieId}/rate
     */
    public function rate (Request $request, $movieId) {

        /*
         * Funkcija koja obradjuje zahtev
         * i cija se povratna vrednost vraca korisniku
         */
        return $this->movieHandler->rate($request, $movieId);

    }

    /*
     * Poziva se na GET putanji /movies
     */
    public function getAll (Request $request) {

        /*
         * Funkcija koja obradjuje zahtev
         * i cija se povratna vrednost vraca korisniku
         */
        return $this->movieHandler->getMovies($request);

    }

    /*
     * Poziva se na GET putanji /movies/{movieId}
     */
    public function getOne(Request $request, $movieId) {

        /*
         * Funkcija koja obradjuje zahtev
         * i cija se povratna vrednost vraca korisniku
         */
        return $this->movieHandler->getMovies($request, $movieId);

    }

    /*
     * Poziva se na GET putanji /movies/{movieId}/comments
     */
    public function getComments(Request $request, $movieId) {

        /*
         * Funkcija koja obradjuje zahtev
         * i cija se povratna vrednost vraca korisniku
         */
        return $this->movieHandler->getComments($request, $movieId);

    }

}