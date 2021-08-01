<?php

namespace App\Model;


use Nette\Security\Permission;

class Authorizator
{
    /**
     * @return Permission
     */
    public static function create()
    {
        $acl = new Permission;

        // roles
        $acl->addRole('a'); // admin
        $acl->addRole('u'); // user
        $acl->addRole('vip', 'u'); // user

        // resources
        $acl->addResource('Default');
        $acl->addResource('Page');
        $acl->addResource('Article');
        $acl->addResource('User');
        $acl->addResource('Vendor');
        $acl->addResource('Offers');
        $acl->addResource('Front:Default');
        $acl->addResource('Front:City');

        // rules
        $acl->allow('a', Permission::ALL, ['create', 'read', 'update', 'delete', 'use']);
        $acl->allow('u', ['Front:Default', 'Front:City'], ['read']);

        return $acl;
    }
}
