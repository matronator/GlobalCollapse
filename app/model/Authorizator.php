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

        // resources
        $acl->addResource('Default');
        $acl->addResource('Page');
        $acl->addResource('Article');
        $acl->addResource('User');

        // rules
        $acl->allow('a', Permission::ALL, ['create', 'read', 'update', 'delete', 'use']);

        return $acl;
    }
}