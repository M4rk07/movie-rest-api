<?php

namespace App\MovModels;
use AuthBucket\OAuth2\Exception\InvalidRequestException;
use AuthBucket\OAuth2\Model\ModelInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Created by PhpStorm.
 * User: marko
 * Date: 3.11.16.
 * Time: 00.02
 */
class User extends AbstractModel
{
    protected $email;
    protected $firstName;
    protected $lastName;
    protected $fullName;
    protected $dateRegistered;
    protected $deleted;

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param mixed $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param mixed $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFullName()
    {
        return $this->fullName;
    }

    /**
     * @param mixed $fullName
     */
    public function setFullName($fullName)
    {
        $this->fullName = $fullName;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDateRegistered()
    {
        return $this->dateRegistered;
    }

    /**
     * @param mixed $dateRegistered
     */
    public function setDateRegistered($dateRegistered)
    {
        $this->dateRegistered = $this->createDateTimeFromString($dateRegistered);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * @param mixed $deleted
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
        return $this;
    }

    /*
     * Funkcija koja preuzima asocijativni niz,
     * a zatim dodeljuje vrednosti niza odgovarajucim poljima objekta.
     */
    public function setValuesFromArray($values)
    {

        if(isset($values['user_id'])) $this->setId($values['user_id']);
        if(isset($values['email'])) $this->setEmail($values['email']);
        if(isset($values['first_name'])) $this->setFirstName($values['first_name']);
        if(isset($values['last_name'])) $this->setLastName($values['last_name']);
        if(isset($values['full_name'])) $this->setFullName($values['full_name']);
        if(isset($values['date_registered'])) $this->setDateRegistered($values['date_registered']);
        if(isset($values['deleted'])) $this->setDeleted($values['deleted']);

    }

    /*
     * Funkcija koja preuzima iz objekta samo polja koja su vezana
     * za MySQL bazu podataka, i vraca njihov asocijativni niz.
     */
    public function getMysqlValues () {
        $keyValues = array ();

        empty($this->id) ? : $keyValues['user_id'] = $this->id;
        empty($this->email) ? : $keyValues['email'] = $this->email;
        empty($this->firstName) ? : $keyValues['first_name'] = $this->firstName;
        empty($this->lastName) ? : $keyValues['last_name'] = $this->lastName;
        empty($this->fullName) ? : $keyValues['full_name'] = $this->fullName;
        empty($this->deleted) ? : $keyValues['deleted'] = $this->deleted;

        return $keyValues;
    }

    /*
     * Funkcija koja vraca sva polja objekta kao asocijativni niz.
     */
    public function getValuesAsArray(array $fields = null)
    {
        $keyValues = $this->getMysqlValues();

        return $keyValues;
    }

}