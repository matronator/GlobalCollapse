<?php

namespace App\Model;

use Nette\Security\Permission;


class Authorizator
{
    /**
     * @return Permission
     */
    public static function create(): Permission
    {
        $acl = new Permission;

        // roles
        $acl->addRole('a'); // admin
        $acl->addRole('u'); // user

        // resources
        $acl->addResource('Admin:Default');
        $acl->addResource('Admin:Page');
        $acl->addResource('Admin:Article');
        $acl->addResource('Admin:User');
        $acl->addResource('Front:Article');
        $acl->addResource('Front:Game');
        $acl->addResource('Front:Store');

        // rules
        $acl->allow('a', Permission::ALL, ['create', 'read', 'update', 'delete', 'use']);
        $acl->allow('u', ['Front:Article', 'Front:Game', 'Front:Store'], ['read']);

        return $acl;
    }
}
