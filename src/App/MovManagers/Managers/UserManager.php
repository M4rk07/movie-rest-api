<?php

namespace App\MovModels;
use App\Algos\Algo;
use App\MateyModels\Activity;
use App\MateyModels\User;
use App\Security\SaltGenerator;
use App\Services\BaseService;
use App\Services\CloudStorageService;
use AuthBucket\OAuth2\Exception\InvalidRequestException;
use AuthBucket\OAuth2\Exception\ServerErrorException;
use AuthBucket\OAuth2\Model\ModelInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Created by PhpStorm.
 * User: marko
 * Date: 3.11.16.
 * Time: 00.14
 */

/*
 * Klasa u usluzi rukovanje podacima iz baze podataka
 * za objekat User.
 */
class UserManager extends AbstractManager
{

    /**
     * @return mixed
     */
    /*
     * Vraca naziv klase u cijoj je usluzi.
     */
    public function getClassName()
    {
        return 'App\\MovModels\\User';
    }

    /*
     * Vraca naziv tabele u MySQL bazi podataka.
     */
    public function getTableName() {
        return self::T_USER;
    }

    /*
     * Vraca naziv kljuca u Redis bazi podataka.
     */
    public function getKeyName()
    {
        return "USER";
    }

    /*
     * Funckija koja cita podatke o korisniku
     * na osnovu zadatog email-a.
     */
    public function loadUserByEmail($email)
    {
        $result = $this->db->fetchAll("SELECT *
        FROM ".self::T_USER."
        WHERE email = ? LIMIT 1",
            array($email));

        $models = $this->makeObjects($result);

        return is_array($models) ? reset($models) : $models;
    }


}