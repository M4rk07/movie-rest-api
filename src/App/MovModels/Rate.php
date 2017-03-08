<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 21.2.17.
 * Time: 14.43
 */

namespace App\MovModels;


class Rate extends AbstractModel
{

    protected $userId;
    protected $movieId;
    protected $rate;
    protected $comment;

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param mixed $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMovieId()
    {
        return $this->movieId;
    }

    /**
     * @param mixed $movieId
     */
    public function setMovieId($movieId)
    {
        $this->movieId = $movieId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRate()
    {
        return $this->rate;
    }

    /**
     * @param mixed $rate
     */
    public function setRate($rate)
    {
        $this->rate = $rate;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param mixed $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
        return $this;
    }

    /*
     * Funkcija koja preuzima asocijativni niz,
     * a zatim dodeljuje vrednosti niza odgovarajucim poljima objekta.
     */
    public function setValuesFromArray($values)
    {

        if(isset($values['user_id'])) $this->setUserId($values['user_id']);
        if(isset($values['movie_id'])) $this->setMovieId($values['movie_id']);
        if(isset($values['rate'])) $this->setRate($values['rate']);
        if(isset($values['comment'])) $this->setComment($values['comment']);

    }

    /*
     * Funkcija koja preuzima iz objekta samo polja koja su vezana
     * za MySQL bazu podataka, i vraca njihov asocijativni niz.
     */
    public function getMysqlValues () {
        $keyValues = array ();

        empty($this->userId) ? : $keyValues['user_id'] = $this->userId;
        empty($this->movieId) ? : $keyValues['movie_id'] = $this->movieId;
        empty($this->rate) ? : $keyValues['rate'] = $this->rate;
        empty($this->comment) ? : $keyValues['comment'] = $this->comment;

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