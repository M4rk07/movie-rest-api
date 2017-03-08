<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 7.11.16.
 * Time: 00.00
 */

namespace App\MovModels;


use AuthBucket\OAuth2\Model\ModelInterface;

/*
 * Abstraktna klasa koju nasledjuju svi modeli.
 */
abstract class AbstractModel implements ModelInterface
{

    protected $id;
    protected $dateFormat = 'Y-m-d H:i:s';

    /*
     * Getter i setter za ID, sluzi samo za objekte koji poseduju ID.
     */
    public function getId(){
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    /*
     * Neke pomocne metode za konvertovanje datuma, kao i nizova u string.
     */
    public function createDateTimeFromString ($dateTimeString) {
        return \DateTime::createFromFormat($this->dateFormat, $dateTimeString);
    }

    public function createArrayFromString ($string) {
        return explode(" ", $string);
    }

    public function createStringFromArray ($array) {
        return implode(" ", $array);
    }

    abstract public function setValuesFromArray ($values);

    abstract public function getMysqlValues ();

    abstract public function getValuesAsArray ();

}