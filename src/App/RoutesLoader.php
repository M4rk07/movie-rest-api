<?php

namespace App;

use Silex\Application;

/*
 * Klasa u kojoj definisemo putanje REST API-ja.
 */
class RoutesLoader
{
    private $app;

    // Konstruktor koji preuzima app promenljivu dostupnu celoj aplikaciji
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function bindRoutesToControllers()
    {
        $api = $this->app["controllers_factory"];
        $apiAuth = $this->app["controllers_factory"];

        // OAuth 2.0 putanje
        $this->app->get('/api/oauth2/authorize', 'authbucket_oauth2.oauth2_controller:authorizeAction')
            ->bind('api_oauth2_authorize');

        $this->app->post('/api/oauth2/token', 'authbucket_oauth2.oauth2_controller:tokenAction')
            ->bind('api_oauth2_token');

        $this->app->match('/api/oauth2/debug', 'authbucket_oauth2.oauth2_controller:debugAction')
            ->bind('api_oauth2_debug');

        // Putanje za resurs Users
        $api->post('/users', 'mov.user_controller:save');
        $apiAuth->delete('/users', 'mov.user_controller:delete');

        // Putanje za resur Movies
        $api->get('/movies', 'mov.movie_controller:getAll');
        $api->get('/movies/{movieId}', 'mov.movie_controller:getOne');
        $api->get('/movies/{movieId}/comments', 'mov.movie_controller:getComments');
        $apiAuth->post('/movies/{movieId}/rate', 'mov.movie_controller:rate');

        /*
         * Dodavanje prefiksa slobodnim putanjama, i putanjama
         * za koje je potrebna autentifikacija.
         */
        $this->app->mount($this->app["api.endpoint"].'/'.$this->app["api.version"], $api);
        $this->app->mount($this->app["api.endpoint"].'/'.$this->app["api.version"].'/auth', $apiAuth);
    }
}

