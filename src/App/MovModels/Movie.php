<?php

namespace App\MovModels;


class Movie extends AbstractModel
{

    protected $movieName;
    protected $coverUrl;
    protected $genre;
    protected $numOfRates;
    protected $sumOfRates;

    /**
     * @return mixed
     */
    public function getMovieName()
    {
        return $this->movieName;
    }

    /**
     * @param mixed $movieName
     */
    public function setMovieName($movieName)
    {
        $this->movieName = $movieName;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCoverUrl()
    {
        return $this->coverUrl;
    }

    /**
     * @param mixed $coverUrl
     */
    public function setCoverUrl($coverUrl)
    {
        $this->coverUrl = $coverUrl;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getGenre()
    {
        return $this->genre;
    }

    /**
     * @param mixed $genre
     */
    public function setGenre($genre)
    {
        $this->genre = $genre;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNumOfRates()
    {
        return $this->numOfRates;
    }

    /**
     * @param mixed $numOfRates
     */
    public function setNumOfRates($numOfRates)
    {
        $this->numOfRates = $numOfRates;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSumOfRates()
    {
        return $this->sumOfRates;
    }

    /**
     * @param mixed $sumOfRates
     */
    public function setSumOfRates($sumOfRates)
    {
        $this->sumOfRates = $sumOfRates;
        return $this;
    }

    /*
     * Funkcija koja preuzima asocijativni niz,
     * a zatim dodeljuje vrednosti niza odgovarajucim poljima objekta.
     */
    public function setValuesFromArray($values)
    {

        if(isset($values['movie_id'])) $this->setId($values['movie_id']);
        if(isset($values['movie_name'])) $this->setMovieName($values['movie_name']);
        if(isset($values['cover_url'])) $this->setCoverUrl($values['cover_url']);
        if(isset($values['genre'])) $this->setGenre($values['genre']);
        if(isset($values['num_of_rates']))
            $values['num_of_rates'] == null ?
                $this->setNumOfRates(0) : $this->setNumOfRates($values['num_of_rates']);
        if(isset($values['sum_of_rates']))
            $values['sum_of_rates'] == null ?
                $this->setSumOfRates(0) : $this->setSumOfRates($values['sum_of_rates']);

    }

    /*
     * Funkcija koja preuzima iz objekta samo polja koja su vezana
     * za MySQL bazu podataka, i vraca njihov asocijativni niz.
     */
    public function getMysqlValues () {
        $keyValues = array ();

        empty($this->id) ? : $keyValues['movie_id'] = $this->id;
        empty($this->movieName) ? : $keyValues['movie_name'] = $this->movieName;
        empty($this->coverUrl) ? : $keyValues['cover_url'] = $this->coverUrl;
        empty($this->genre) ? : $keyValues['genre'] = $this->genre;

        return $keyValues;
    }

    /*
     * Funkcija koja vraca sva polja objekta kao asocijativni niz.
     */
    public function getValuesAsArray(array $fields = null)
    {
        $keyValues = $this->getMysqlValues();

        empty($this->numOfRates) ? : $keyValues['num_of_rates'] = $this->numOfRates;
        empty($this->sumOfRates) ? : $keyValues['sum_of_rates'] = $this->sumOfRates;

        return $keyValues;
    }

}