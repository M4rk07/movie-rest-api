<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 21.2.17.
 * Time: 17.27
 */

namespace App\MovModels;

/*
 * Klasa u usluzi rukovanje podacima iz baze podataka
 * za objekat Movie.
 */
class MovieManager extends AbstractManager
{
    // Nazivi polja u redis bazi podataka.
    const FIELD_NUM_OF_RATES = "num_of_rates";
    const FIELD_SUM_OF_RATES = "sum_of_rates";
    /**
     * @return mixed
     */
    /*
     * Vraca naziv klase u cijoj je usluzi.
     */
    public function getClassName()
    {
        return 'App\\MovModels\\Movie';
    }

    /*
     * Vraca naziv tabele u MySQL bazi podataka.
     */
    public function getTableName() {
        return self::T_MOVIE;
    }

    /*
     * Vraca naziv kljuca u Redis bazi podataka.
     */
    public function getKeyName()
    {
        return "MOVIE";
    }

    /*
     * Ova funkcija sluzi da inkrementira broj ocena
     * u Redis bazi podataka. Prihvata parametar ID filma
     * za koji se povecava broj ocena, kao i broj za koji
     * se inkremetira. Podrazumevana vrednost je 1.
     */
    public function incrNumOfRates($movieId, $incrBy = 1) {
        $this->redis->hincrby($this->getKeyName().":rates:".
            $movieId, self::FIELD_NUM_OF_RATES, $incrBy);
    }

    /*
     * Slicno kao prethodna funkcija, osim sto inkrementira
     * sumu svih ocena.
     */
    public function incrSumOfRates($movieId, $incrBy = 1) {
        $this->redis->hincrby($this->getKeyName().":rates:".
            $movieId, self::FIELD_SUM_OF_RATES, $incrBy);
    }

    /*
     * Funkcija prihvata objekat za koji je potrebno izvuci podatke
     * o broju i sumi ocena, a zatim se ti podaci izvlace iz Redis baze podataka.
     */
    public function getRates (Movie $movie) {

        $rates = $this->redis->hgetall($this->getKeyName().":rates:".$movie->getId());

        $movie->setValuesFromArray($rates);

        return $movie;

    }

}