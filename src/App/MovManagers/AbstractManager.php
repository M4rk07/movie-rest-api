<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 6.11.16.
 * Time: 18.52
 */

namespace App\MovModels;


use AuthBucket\OAuth2\Exception\ServerErrorException;
use AuthBucket\OAuth2\Model\ModelInterface;
use AuthBucket\OAuth2\Model\ModelManagerInterface;
use Doctrine\DBAL\Connection;
use Predis\Client;
use Symfony\Component\HttpFoundation\JsonResponse;

/*
 * Abstraktna klasa koju nasledjuje svaki menadzer podataka.
 * U njoj su definisani svi nazivi tabela u bazi, kao i metode
 * za citanje, pisanje, azuriranje i brisanje podataka iz baze.
 */
abstract class AbstractManager implements ModelManagerInterface
{
    // Database connection holder
    protected $db;
    protected $redis;

    // Nazivi tabela u bazi podataka vezani za aplikaciju
    const T_USER = "mov_user";
    const T_RATE = "mov_rate";
    const T_MOVIE = "mov_movie";

    // Nazivi tabela vezani za OAuth 2.0 protokol
    const T_A_USER = "oauth2_user";
    const T_A_ACCESS_TOKEN = "oauth2_access_token";
    const T_A_REFRESH_TOKEN = "oauth2_refresh_token";
    const T_A_CLIENTS = "oauth2_client";
    const T_A_CODES = "oauth2_code";
    const T_A_AUTHORIZE = "oauth2_authorize";
    const T_A_SCOPES = "oauth2_scope";

    /*
     * Konstruktor preuzima konekciju sa MySQL bazom i Redis.
     */
    public function __construct(Connection $db, Client $redis = null)
    {
        $this->db = $db;
        $this->redis = $redis;
    }

    // Metoda za zapocinjanje transakcije.
    public function startTransaction() {
        if($this->db->isTransactionActive()) throw new ServerErrorException();
        $this->db->beginTransaction();
    }

    // Metoda za izvrsenje transakcije.
    public function commitTransaction() {
        if($this->db->isTransactionActive())
            $this->db->commit();
    }

    // Metoda za povlacenje transakcije.
    public function rollbackTransaction() {
        if($this->db->isTransactionActive())
            $this->db->rollBack();
    }

    /*
     * Ova funkcija sluzi za kreiranje novog modela u bazi podataka.
     */
    public function createModel(ModelInterface $model, $ignore = false)
    {

        // Preuzimanje svih polja objekta kao asocijativni niz
        $fields = $model->getMysqlValues();

        $queryBuilder = $this->db->createQueryBuilder();

        $queryBuilder->insert($this->getTableName());

        $counter = 0;
        foreach($fields as $key => $value) {
            $key = $this->makeColumnName($key);
            $queryBuilder->setValue($key, '?');
            $queryBuilder->setParameter($counter++, $value);
        }

        $result = $queryBuilder->execute();

        $model->setId($this->db->lastInsertId());

        return $result > 0 ? $model : null;

    }

    /*
     * Funkcija za citanje svih zapisa iz baze odredjenog objekta.
     * Moze se proslediti parametar za ogranicenje koliko zapisa
     * se izvlaci.
     */
    public function readModelAll($limit = null)
    {
        $all = $this->db->fetchAll("SELECT * FROM " . $this->getTableName()
            . ($limit == null ? "" : " LIMIT ".$limit));
        return $this->makeObjects($all);
    }

    /*
     * Citanje zapisa po nekom kriterijumu.
     * Potrebno je proslediti kriterijum, a opciono se moze
     * naznaciti i grupisanje po nekom kriterijumu,
     * zatim ogranicenje koliko zapisa najvise izvuci,
     * koliko rastojanje od prvog u nizu, i koja polja je
     * potrebno procitati.
     */
    public function readModelBy(array $criteria, array $orderBy = null,
                                $limit = null, $offset = null,
                                array $fields = null, $ascDesc = 'ASC')
    {

        $queryBuilder = $this->db->createQueryBuilder();

        if(empty($fields)) $fields = "*";

        $queryBuilder->select($fields)
            ->from($this->getTableName());

        $counter = 0;
        foreach($criteria as $key => $value) {
            $key = $this->makeColumnName($key);
            if(is_array($value)) {
                foreach($value as $val) {
                    $queryBuilder->orWhere($key . "=?");
                    $queryBuilder->setParameter($counter++, $val);
                }
            }
            else {
                $queryBuilder->andWhere($key . "=?");
                $queryBuilder->setParameter($counter++, $value);
            }
        }

        if(isset($limit))
            $queryBuilder->setMaxResults($limit);
        if(isset($orderBy))
            $queryBuilder->orderBy($orderBy, $ascDesc);
        if(isset($offset))
            $queryBuilder->setFirstResult($offset);

        $all = $queryBuilder->execute()->fetchAll();

        $models = $this->makeObjects($all);
        return $models;
    }

    /*
     * Citanje samo jednog zapisa.
     * Ovo se postize pozivanjem prethodne funkcije sa limitom 1.
     */
    public function readModelOneBy(array $criteria, array
                                    $orderBy = null, array $fields = null)
    {
        $models = $this->readModelBy($criteria, $orderBy, 1, 0, $fields);

        return is_array($models) && !empty($models) ? reset($models) : null;
    }

    /*
     * Metoda za azuriranje nekog zapisa u bazi.
     */
    public function updateModel(ModelInterface $model,
                                array $criteria = null)
    {
        // Preuzimanje asocijativnog niza
        $fields = $model->getMysqlValues();

        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder->update($this->getTableName());

        $counter = 0;
        foreach($fields as $key => $value) {
            $key = $this->makeColumnName($key);
            $queryBuilder->set($key, "?");
            $queryBuilder->setParameter($counter++, $value);
        }

        foreach($criteria as $key => $value) {
            $key = $this->makeColumnName($key);
            $queryBuilder->andWhere($key, "=?");
            $queryBuilder->setParameter($counter++, $value);
        }

        $result = $queryBuilder->execute();

        return $result > 0 ? $model : null;
    }

    /*
     * Metoda za brisanje odredjenog zapisa iz baze.
     */
    public function deleteModel(ModelInterface $model, $criteria = null)
    {
        // Preuzimanje objekta kao asocijativnog niza
        $criterias = $model->getMysqlValues();

        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder->delete($this->getTableName());

        $counter = 0;
        foreach($criterias as $key => $value) {
            $key = $this->makeColumnName($key);
            $queryBuilder->andWhere($key, "=?");
            $queryBuilder->setParameter($counter++, $value);
        }

        $result = $queryBuilder->execute();

        return $result > 0 ? $model : null;
    }

    /*
     * Funkcija koja sluzi da na osnovu asocijativnog niza,
     * napravi odgovarajuce objekte i vrati niz objekata.
     */
    public function makeObjects(array $result)
    {
        // Promenljiva koja ce sadrzati niz objekata
        $modelObjects = array();

        // Kreiranje objekta iz niza i ubacivanje u modelObjects
        foreach ($result as $modelValues) {
            $className = $this->getClassName();
            $object = new $className();
            $object->setValuesFromArray($modelValues);

            array_push($modelObjects, $object);
        }

        // Vracanje niza objekata
        return $modelObjects;
    }

    // Potrebna preimenovanja nekih polja zbog vec
    // utvrdjene biblioteke za OAuth 2.0
    public function makeColumnName ($key) {
        if(strcmp($key, "accessToken") == 0) $key = "access_token";
        else if(strcmp($key, "clientId") == 0) $key = "client_id";
        else if(strcmp($key, "tokenType") == 0) $key = "token_type";
        else if(strcmp($key, "clientSecret") == 0) $key = "client_secret";
        else if(strcmp($key, "redirectUri") == 0) $key = "redirect_uri";
        else if(strcmp($key, "refreshToken") == 0) $key = "refresh_token";

        return $key;
    }

}