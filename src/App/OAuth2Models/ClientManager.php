<?php
/**
 * Created by PhpStorm.
 * User: M4rk0
 * Date: 8/16/2016
 * Time: 4:39 PM
 */

namespace App\OAuth2Models;

use App\MovModels\AbstractManager;
use AuthBucket\OAuth2\Model\ClientManagerInterface;

class ClientManager extends AbstractManager implements ClientManagerInterface
{

    /**
     * @return mixed
     */
    public function getClassName()
    {
        return 'App\\OAuth2Models\\Client';
    }

    public function getTableName() {
        return self::T_A_CLIENTS;
    }

}