<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 21.2.17.
 * Time: 13.37
 */

namespace App\Handlers;


use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

// Interfejs klase UserHandler
interface UserHandlerInterface
{

    public function save(Application $app, Request $request);
    public function delete(Request $request);

}