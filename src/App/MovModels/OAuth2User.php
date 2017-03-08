<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 6.11.16.
 * Time: 21.43
 */

namespace App\MovModels;

use AuthBucket\OAuth2\Model\ModelInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class OAuth2User extends \App\MovModels\AbstractModel implements UserInterface
{

    protected $username;
    protected $password;
    protected $salt;
    protected $roles = array();

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * @param mixed $salt
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
        return $this;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @param array $roles
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;
        return $this;
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    /*
     * Funkcija koja preuzima asocijativni niz,
     * a zatim dodeljuje vrednosti niza odgovarajucim poljima objekta.
     */
    public function setValuesFromArray($values)
    {
        $this->id = isset($values['user_id']) ? $values['user_id'] : "";
        $this->username = isset($values['username']) ? $values['username'] : "";
        $this->password = isset($values['password']) ? $values['password'] : "";
        $this->salt = isset($values['salt']) ? $values['salt'] : "";
    }

    /*
     * Funkcija koja preuzima iz objekta samo polja koja su vezana
     * za MySQL bazu podataka, i vraca njihov asocijativni niz.
     */
    public function getMysqlValues()
    {
        $keyValues = array ();

        empty($this->id) ? : $keyValues['user_id'] = $this->id;
        empty($this->username) ? : $keyValues['username'] = $this->username;
        empty($this->password) ? : $keyValues['password'] = $this->password;
        empty($this->salt) ? : $keyValues['salt'] = $this->salt;

        return $keyValues;
    }

    /*
     * Funkcija koja vraca sva polja objekta kao asocijativni niz.
     */
    public function getValuesAsArray()
    {
        $keyValues = $this->getMysqlValues();

        return $keyValues;
    }


}