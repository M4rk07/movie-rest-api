<?php
namespace App\Provider;
use App\Controllers\MovieController;
use App\Controllers\UserController;
use App\Handlers\MovieHandler;
use App\Handlers\UserHandler;
use AuthBucket\OAuth2\Model\InMemory\ModelManagerFactory;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Api\BootableProviderInterface;
use Silex\Api\EventListenerProviderInterface;
use Silex\Application;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Created by PhpStorm.
 * User: marko
 * Date: 20.2.17.
 * Time: 15.53
 */

/*
 * Ova klasa predstavlja snabdevac servisa.
 * Njena definicija je predodredjena Silex frejmvorkom.
 */
class MovServiceProvider implements ServiceProviderInterface, EventListenerProviderInterface, BootableProviderInterface
{
    public function register(Container $app)
    {
        /*
         * Definisanje naziva modela.
         */
        $app['mov.model'] = [
            'user' => 'App\\MovModels\\User',
            'oauth2User' => 'App\\MovModels\\OAuth2User',
            'rate' => 'App\\MovModels\\Rate',
            'movie' => 'App\\MovModels\\Movie'
        ];

        /*
         * Ova promenljiva sadrzi funkciju koja vraca fabriku menadzera.
         */
        $app['mov.model_manager.factory'] = $app->factory(function ($app) {
            return new ModelManagerFactory(
                $app['mov.model'],
                $app['db'],
                $app['predis']
            );
        });

        /*
         * Naredne dve promenljive sadrze funkcije koje
         * vracaju handler-e za odredjene kontrolere.
         */
        $app['mov.user.handler'] = function ($app) {
            return new UserHandler(
                $app['mov.model_manager.factory'],
                $app['validator']
            );
        };

        $app['mov.movie.handler'] = function ($app) {
            return new MovieHandler(
                $app['mov.model_manager.factory'],
                $app['validator']
            );
        };

        /*
         * Naredne dve funkcije vracaju objekte kontrolere.
         */
        $app['mov.user_controller'] = function () use ($app) {
            return new UserController(
                $app['mov.user.handler']
            );
        };

        $app['mov.movie_controller'] = function () use ($app) {
            return new MovieController(
                $app['mov.movie.handler']
            );
        };


    }

    public function subscribe(Container $app, EventDispatcherInterface $dispatcher)
    {
        $dispatcher->addSubscriber($app['authbucket_oauth2.exception_listener']);
    }

    public function boot(Application $app)
    {
        // TODO: Implement boot() method.
    }


}