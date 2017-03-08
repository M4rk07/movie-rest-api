<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 21.2.17.
 * Time: 13.20
 */

namespace App\Controllers;


use App\Handlers\UserHandlerInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class UserController
{

    /*
     * Ova promenljiva je objekat u kojem su implementirane
     * funkcije koje obradjuju zahtev i definisu biznis logiku.
     * Funkcije implementirane u toj klasi vrse proveru parametara,
     * nacin na koji ce podaci biti izvuceni iz baze podataka,
     * kao i nacin na koji ce biti prezentovani.
     */
    protected $userHandler;

    public function __construct(UserHandlerInterface $userHandler) {
        $this->userHandler = $userHandler;
    }

    /*
     * Poziva se na POST putanji /users
     */
    public function save(Application $app, Request $request) {
        /*
         * Funkcija koja obradjuje zahtev
         * i cija se povratna vrednost vraca korisniku
         */
        return $this->userHandler->save($app, $request);

    }

    /*
     * Poziva se na DELETE putanji /users
     */
    public function delete(Request $request) {
        /*
         * Funkcija koja obradjuje zahtev
         * i cija se povratna vrednost vraca korisniku
         */
        return $this->userHandler->delete($request);

    }

}