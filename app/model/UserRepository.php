<?php

namespace App\Model;

use Nette;


class UserRepository
{
    /** @var Nette\Database\Context */
    private $database;

    public $roles = [
        'a' => 'Admin',
        'u' => 'User'
    ];

    private $navItems = [
        [
            'presenter' => 'Article',
            'title' => 'Články',
            'icon' => ' file-text'
        ],
        [
            'presenter' => 'Page',
            'title' => 'Stránky',
            'icon' => 'world'
        ],
        [
            'presenter' => 'User',
            'title' => 'Uživatelé',
            'icon' => 'users'
        ],
    ];

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }

    public function findAll()
    {
        return $this->database->table('user');
    }

    public function getNavItems()
    {
        return array_map(function($item) {
            return (object) $item;
        }, $this->navItems);
    }
}
