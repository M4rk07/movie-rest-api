<?php
namespace App\Handlers;
use AuthBucket\OAuth2\Model\InMemory\ModelManagerFactory;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Created by PhpStorm.
 * User: marko
 * Date: 21.2.17.
 * Time: 14.50
 */
/*
 * Abstraktna klasa Handler koju nasledjuju svi Handler-i
 * u aplikaciji.
 * Preuzima fabriku menadzera objektima, kao i validator ogranicenja
 * nasledjen od frejmvorka.
 */
abstract class AbstractHandler
{

    protected $modelManagerFactory;
    protected $validator;

    public function __construct(ModelManagerFactory $modelManagerFactory,
                                ValidatorInterface $validator) {
        $this->modelManagerFactory = $modelManagerFactory;
        $this->validator = $validator;
    }

}