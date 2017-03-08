<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 6.11.16.
 * Time: 21.43
 */

namespace App\MovModels;


use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/*
 * Klasa u usluzi rukovanje podacima iz baze podataka
 * za objekat OAuth2User.
 */
class OAuth2UserManager extends AbstractManager implements UserProviderInterface
{

    /**
     * @return mixed
     */
    /*
     * Vraca naziv klase u cijoj je usluzi.
     */
    public function getClassName()
    {
        return 'App\\MovModels\\OAuth2User';
    }

    /*
     * Vraca naziv tabele u MySQL bazi podataka.
     */
    public function getTableName() {
        return self::T_A_USER;
    }

    /*
     * Vraca naziv kljuca u Redis bazi podataka.
     */
    public function getKeyName()
    {
        return "OAUTH2_USER";
    }

    /*
     * Funkcija cita podatke o korisniku sa odredjenim
     * korisnickim imenom, odnosno email-om u nasem slucaju.
     */
    public function loadUserByUsername($username)
    {
        $models = $this->readModelOneBy(array(
            'username' => $username
        ));

        return is_array($models) ? reset($models) : $models;
    }

    public function refreshUser(UserInterface $user)
    {
        // TODO: Implement refreshUser() method.
    }

    public function supportsClass($class)
    {
        // TODO: Implement supportsClass() method.
    }


}