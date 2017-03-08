<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 21.2.17.
 * Time: 14.49
 */

namespace App\Handlers;


use AuthBucket\OAuth2\Exception\InvalidRequestException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\Type;

class MovieHandler extends AbstractHandler implements MovieHandlerInterface
{
    /*
     * Poziva je rate funkcija kontorlera, i njena
     * povratna vrednost se vraca korisniku.
     */
    public function rate(Request $request, $movieId)
    {
        /*
         * Preuzimamo parametre iz HTTP zahteva.
         * user_id parametar se odredjuje na osnovu prosledjenog tokena.
         * Mozemo iz baze lako proveriti kom korisniku token pripada.
         */
        $userId = $request->request->get("user_id");
        $rateReq = $request->request->get('rate');
        $comment = $request->request->get('comment');

        /*
         * Proveravamo parametar rateReq u kojem se nalazi ocena
         * kao integer vrednost. Mora biti najmanje, 1 a najvise 10.
         */
        $errors = $this->validator->validate($rateReq, [
            new NotBlank(),
            new Range(array(
                'min' => 1,
                'max' => 10,
                'minMessage' => "Number can not be lower than 1.",
                'maxMessage' => "Number can not be higher than 10."
            ))
        ]);
        /*
         * Proveravamo da li je doslo do prestupa prilikom provere.
         * Ukoliko jeste baca se InvalidRequestException.
         */
        if (count($errors) > 0) {
            throw new InvalidRequestException([
                'error_description' => $errors->get(0)->getMessage(),
            ]);
        }

        /*
         * Isti postupak za parametar ID filma koji je prosledjen.
         * Mora biti numericka vrednost da bi se nastavilo sa
         * izvrsavanjem koda.
         */
        $errors = $this->validator->validate($movieId, [
            new NotBlank(),
            new Type(array(
                'type' => 'numeric'
            ))
        ]);
        if (count($errors) > 0) {
            throw new InvalidRequestException([
                'error_description' => $errors->get(0)->getMessage(),
            ]);
        }

        /*
         * Kreiramo objekat u usluzi menadzera sa podacima iz baze podataka,
         * objekta Rate.
         */
        $rateManager = $this->modelManagerFactory->getModelManager('rate');
        /*
         * Kreiramo objekat rate kako bismo smestili podatke u njega,
         * a zatim uneli u bazu podataka.
         */
        $rateClass = $rateManager->getClassName();
        $rate = new $rateClass();

        /*
         * Unosimo podatke u promenljive objekta.
         */
        $rate->setUserId($userId)
            ->setMovieId($movieId)
            ->setRate($rateReq);

        /*
         * Iz razloga sto je komentar opcioni parametar,
         * njega unosimo samo ukoliko je korisnik poslao komentar.
         */
        if(!empty($comment)) $rate->setComment($comment);

        /*
         * Pokusavamo da kreiramo novi zapis u bazi.
         * Ukoliko nesto podje lose, ili je zapis duplikat
         * po primarnom kljucu, bacicemo Exception.
         */
        try {
            $rateManager->createModel($rate);
        } catch (\Exception $e) {
            throw new InvalidRequestException();
        }

        /*
         * Kreiramo menadzer objekta Movie.
         */
        $movieManager = $this->modelManagerFactory->getModelManager('movie');
        /*
         * Dve naredne metode koje se pozivaju inkrementuju broj ocena
         * i sumu ocena, podaci koji su potrebni pri kalkulaciji
         * prosecne ocene.
         */
        $movieManager->incrNumOfRates($movieId);
        $movieManager->incrSumOfRates($movieId, $rateReq);

        /*
         * Odgovaramo sa 200 OK.
         */
        return new JsonResponse(null, 200);

    }

    /*
     * Pozivaju je funkcije kontrolera getAll i getOne, a u zavisnosti
     * od toga da li je prosledjen i parametar movieId, iz baze ce se
     * izvlaciti svi filmovi, ili samo film za koji je prosledjen ID.
     */
    public function getMovies (Request $request, $movieId = null) {

        // Menadzer objekta Movie.
        $movieManager = $this->modelManagerFactory->getModelManager('movie');
        /*
         * Izvlacimo iz zahteva sort parametar,
         * cija vrednost moze biti 'rate' ili 'alpha',
         * a podrazumevana vrednost je 'alpha'.
         */
        $sort = $request->get('sort');

        /*
         * Ovim if uslovom se odlucuje da li se izvlace svi filmovi
         * iz baze, ili samo jedan.
         */
        if($movieId == null) {
            // Citanje svih filmova iz baze.
            $movies = $movieManager->readModelAll();
            /*
             * Ako nije procitan nijedan film, postavljamo moviesArray na prazan niz.
             * U suprotnom, saljemo rezultat na obradu pre prezentacije, pozivanjem
             * funkcije completeMovies.
             */
            if(empty($movies)) $moviesArray = array();
            else $moviesArray = $this->completeMovies($movies, $movieManager, $sort);
        }
        else {
            // Citanje jednog filma iz baze.
            $movie = $movieManager->readModelOneBy(array(
                'movie_id' => $movieId
            ));
            /*
             * Isti postupak kao u prethodnom slucaju.
             */
            if(empty($movie)) $moviesArray = array();
            else $moviesArray = $this->completeMovies(array($movie), $movieManager, $sort);
        }

        /*
         * Vracanje prezentacije kao JSON niz objekata,
         * sa status kodom 200.
         */
        return new JsonResponse($moviesArray, 200);

    }

    /*
     * Funckija koja se poziva getComments funkcijom kontrolera.
     */
    public function getComments(Request $request, $movieId) {

        // Menadzer objekta Rate
        $rateManager = $this->modelManagerFactory->getModelManager('rate');
        /*
         * Iz baze se citaju svi zapisi u tabeli mov_rate,
         * gde je movie_id jednak ID-ju filma za koji se zahtevaju
         * komentari.
         */
        $rates = $rateManager->readModelBy(array(
            'movie_id' => $movieId
        ));

        // Menadzer objekta User
        $userManager = $this->modelManagerFactory->getModelManager('user');

        // Priprema promenljive koja ce sadrzati konacnu prezentaciju.
        $ratesArray = array();

        /*
         * Prolazimo kroz ceo rezultat dobijen citanjem iz baze podataka
         * i za svaki od njih citamo podatke o korisniku koji je ostavio
         * tu ocenu, a zatim dodajemo korisnika u objekat.
         */
        foreach($rates as $rate) {
            // Uzimamo vrednosti objekta kao asocijativni niz.
            $rateProps = $rate->getValuesAsArray();

            // Citamo korisnika iz baze koji je ostavio tu ocenu.
            $user = $userManager->readModelOneBy(array(
                'user_id' => $rate->getUserId()
            ));

            /*
             * Pretvaramo objekat User u asocijativni niz i
             * dodajemo procitanog korisnika u polje 'user' JSON objekta.
             */
            $rateProps['user'] = $user->getValuesAsArray();
            /*
             * Ceo objekat - podaci o jednoj oceni, dodajemo u
             * konacan niz objekata za prezentaciju.
             */
            $ratesArray[] = $rateProps;
        }

        // Saljemo listu komentara nazad korisniku.
        return new JsonResponse($ratesArray, 200);

    }

    /*
     * Funkcija koja vrsi obradu liste objekata.
     * Racuna prosecnu ocenu za svaki film i sortira listu
     * po potrebi.
     */
    public function completeMovies (array $movies, $movieManager, $sort) {
        // Priprema promenljive koja sadrzi konacnu listu.
        $moviesArray = array();

        /*
         * Prolazimo kroz sve objekte, a zatim za svaki film
         * racunamo prosecnu ocenu i dodajemo u konacan rezultat.
         */
        foreach($movies as $movie) {
            // Preuzimamo objekat kao asocijativni niz.
            $movieProps = $movie->getValuesAsArray();
            // Izvlacimo podatke o broju ocena i njihovoj sumi.
            $movie = $movieManager->getRates($movie);

            /*
             * Ukoliko je broj ocena 0, odmah mozemo staviti prosecnu
             * jednaku takodje 0.
             */
            if($movie->getNumOfRates() == 0) $movieProps['rate'] = 0;
            else {
                /*
                 * Racunamo prosecnu ocenu i zaokruzujemo na 2 decimale.
                 */
                $average = round((float)((int)$movie->getSumOfRates() / (int)$movie->getNumOfRates()), 2);
                $movieProps['rate'] = $average;
            }
            // Dodavanje objekta u konacan niz objekata.
            $moviesArray[] = $movieProps;
        }

        /*
         * Ukoliko je potrebno, pre vracanja konacnog niza objekata,
         * izvrsava se sortiranje niza po oceni ili abecedno.
         */
        if($sort == 'rate') usort($moviesArray, array($this, "compareRate"));
        else usort($moviesArray, array($this, "compareName"));

        // Vracanje konacnog rezultata.
        return $moviesArray;
    }

    /*
     * Funckija u usluzi funkcije usort za sortiranje po abecednom
     * redosledu.
     */
    public function compareName ($first, $second) {
        return strcmp($first['movie_name'], $second['movie_name']);
    }

    // Funckija u usluzi funckije usort za sortiranje po oceni opadajuce.
    public function compareRate ($first, $second) {
        if($first['rate'] == $second['rate']) return 0;
        return ($first['rate'] > $second['rate']) ? -1 : 1;
    }

}