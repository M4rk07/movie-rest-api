<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 21.2.17.
 * Time: 15.02
 */

namespace App\MovModels;

/*
 * Klasa u usluzi rukovanje podacima iz baze podataka
 * za objekat Rate.
 */
class RateManager extends AbstractManager
{

    /**
     * @return mixed
     */
    /*
     * Vraca naziv klase u cijoj je usluzi.
     */
    public function getClassName()
    {
        return 'App\\MovModels\\Rate';
    }

    /*
     * Vraca naziv tabele u MySQL bazi podataka.
     */
    public function getTableName() {
        return self::T_RATE;
    }

    /*
     * Vraca naziv kljuca u Redis bazi podataka.
     */
    public function getKeyName()
    {
        return "RATE";
    }

}