<?php
/**
 * Created by PhpStorm.
 * User: M4rk0
 * Date: 8/16/2016
 * Time: 5:06 PM
 */

namespace App\OAuth2Models;


use App\MovModels\AbstractManager;
use AuthBucket\OAuth2\Model\ScopeManagerInterface;

class ScopeManager extends AbstractManager implements ScopeManagerInterface
{

    /**
     * @return mixed
     */
    public function getClassName()
    {
        return 'App\\OAuth2Models\\Scope';
    }

    public function getTableName() {
        return self::T_A_SCOPES;
    }

}