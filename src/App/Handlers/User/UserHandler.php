<?php
namespace App\Handlers;
use App\Security\SaltGenerator;
use AuthBucket\OAuth2\Exception\InvalidRequestException;
use AuthBucket\OAuth2\Exception\ServerErrorException;
use AuthBucket\OAuth2\Model\InMemory\ModelManagerFactory;
use AuthBucket\OAuth2\Validator\Constraints\Password;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Created by PhpStorm.
 * User: marko
 * Date: 21.2.17.
 * Time: 13.25
 */
class UserHandler extends AbstractHandler implements UserHandlerInterface
{

    /*
     * Funckija koju poziva save funckija kontrolera i njen
     * rezultat vraca se korisniku.
     */
    public function save(Application $app, Request $request)
    {
        /*
         * Izlvacenje potrebnih podataka za registraciju
         * korisnika iz zahteva.
         */
        $email = trim($request->request->get('email'));
        $password = $request->request->get('password');
        $firstName = trim($request->request->get('first_name'));
        $lastName = trim($request->request->get('last_name'));

        // Provera da li je email u odgovarajucem formatu
        $errors = $this->validator->validate($email, [
            new NotBlank(),
            new Email()
        ]);
        if (count($errors) > 0) {
            throw new InvalidRequestException([
                'error_description' => $errors->get(0)->getMessage(),
            ]);
        }

        // Provera da li lozinka u odgovarajucem formatu.
        $errors = $this->validator->validate($password, [
            new NotBlank(),
            new Password()
        ]);
        if (count($errors) > 0) {
            throw new InvalidRequestException([
                'error_description' => $errors->get(0)->getMessage(),
            ]);
        }

        // Provera da li je poslato ime.
        $errors = $this->validator->validate($firstName, [
            new NotBlank()
        ]);
        if (count($errors) > 0) {
            throw new InvalidRequestException([
                'error_description' => $errors->get(0)->getMessage(),
            ]);
        }

        // Provera da li je poslato prezime
        $errors = $this->validator->validate($lastName, [
            new NotBlank()
        ]);
        if (count($errors) > 0) {
            throw new InvalidRequestException([
                'error_description' => $errors->get(0)->getMessage(),
            ]);
        }

        /*
         * Koristimo random string generator kako bismo generisali
         * salt za lozinku.
         */
        $salt = (new SaltGenerator())->generateSalt();
        // Zatim enkodiramo lozinku sha512 hashing algoritmom.
        $encodedPassword = $this->encodePassword($app, $password, $salt);

        // Kreiramo potrebne menadzere za dalji rad.
        $userManager = $this->modelManagerFactory->getModelManager('user');
        $oauth2UserManager = $this->modelManagerFactory->getModelManager('oauth2User');

        $userClass = $userManager->getClassName();
        $oauth2UserClass = $oauth2UserManager->getClassName();

        // Kreiramo potrebne objekte koji ce drzati podatke.
        $user = new $userClass();
        $oauth2User = new $oauth2UserClass();

        // Kreiramo novog korisnika.
        $user->setEmail($email)
            ->setFirstName($firstName)
            ->setLastName($lastName)
            ->setFullName($firstName." ".$lastName);

        // Kreiramo korisnikovu lozinku u salt.
        $oauth2User->setUsername($email)
            ->setPassword($encodedPassword)
            ->setSalt($salt);

        /*
         * Unosimo podatke u bazu,
         * a ukoliko bilo koji korak ne uspe,
         * povlacimo transakciju kako ne bi nastali
         * nepotpuni zapisi u bazi koji mogu stvoriti problem kasnije.
         */
        $userManager->startTransaction();
        try {
            // Zapisujemo korisnika u bazu
            $userManager = $this->modelManagerFactory->getModelManager('user');
            $user = $userManager->createModel($user);

            // Zapisujemo njegovu lozinku u bazu.
            $oauth2User->setId($user->getId());
            $oauth2UserManager->createModel($oauth2User);

            // Konacno, izvrsavamo transakciju.
            $userManager->commitTransaction();
        } catch (\Exception $e) {
            $userManager->rollbackTransaction();
            throw new ServerErrorException();
        }

        // Vracamo odgovor 200 OK.
        return new JsonResponse(null, 200);

    }

    public function delete(Request $request) {
        /*
         * Izvlacimo ID korisnika koji zeli obrisati nalog.
         * Ovu vrednost izvlacimo preko tokena.
         */
        $userId = $request->request->get("user_id");

        // Kreiramo menadzer objekta kao i objekat korisnika.
        $userManager = $this->modelManagerFactory->getModelManager('user');
        $userClass = $userManager->getClassName();
        $user = new $userClass();

        // Postavljamo njegovu vrednost deleted na 1, odnosno true.
        $user->setDeleted(1);

        // Azuriramo polje deleted u bazi podataka.
        $userManager->updateModel($user, array(
            'user_id' => $userId
        ));

        // Vracamo odgovor 200 OK.
        return new JsonResponse(null, 200);

    }

    /*
     * Funkcija koja vraca enkodiranu lozinku koristeci
     * MessageDigestPasswordEncoder frejmvorka.
     */
    public function encodePassword($app, $password, $salt) {
        // encoding password and salt
        return $app['security.default_encoder']->encodePassword($password, $salt);
    }

}