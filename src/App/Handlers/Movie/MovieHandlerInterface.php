<?php
namespace App\Handlers;
use Symfony\Component\HttpFoundation\Request;

/**
 * Created by PhpStorm.
 * User: marko
 * Date: 21.2.17.
 * Time: 14.48
 */

/*
 * Interfejs za klasu MovieHandler
 */
interface MovieHandlerInterface
{

    public function rate(Request $request, $movieId);
    public function getMovies(Request $request, $movieId = null);
    public function getComments(Request $request, $movieId);

}