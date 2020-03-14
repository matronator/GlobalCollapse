<?php

namespace App\Model;

use Nette;
use Nette\Database\Table\ActiveRow;
use Nette\Application\BadRequestException;
use Nette\Database\Context;
use Nette\Utils\ArrayHash;
use Nette\Utils\Random;

const USER_ROLE_ADMIN = 'a';
const USER_ROLE_USER = 'u';

class UserRepository
{
    /** @var Nette\Database\Context */
    private Context $database;

    public array $userRoles = [
        USER_ROLE_ADMIN => 'Admin',
        USER_ROLE_USER => 'User',
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

    public function getUser(?int $id = null): ?ActiveRow
    {
        if (!$id)
            return null;
        return $this->findAll()
            ->wherePrimary($id)
            ->fetch();
    }

    public function deleteUser(?int $id = null): ?ActiveRow
    {
        $user = $this->getUser($id);
        if (!$user)
            throw new BadRequestException('Uživatel nexistuje');
        $this->findAll()->wherePrimary($id)->delete();
        return $user;
    }


    public function createUser(ArrayHash $values): object
    {
        $userMail = $this->findAll()->where('email', $values->email)->fetch();
        $userName = $this->findAll()->where('username', $values->username)->fetch();
        if ($userMail)
            throw new BadRequestException('Account with this email address already exists.');
        if ($userName)
            throw new BadRequestException('Username taken.');
        $values->registration = new \DateTime();
        $values->date_log = new \DateTime();
        $values->ip = $_SERVER["REMOTE_ADDR"];
        $user = $this->findAll()->insert($values);
        // $this->mailService->sendPasswordLink($user);
        return $user;
    }

    public function updateUser(int $id, ArrayHash $values): ActiveRow
    {
        $this->findAll()->wherePrimary($id)->update($values);
        return $this->getUser($id);
    }

    public function getNavItems()
    {
        return array_map(function($item) {
            return (object) $item;
        }, $this->navItems);
    }
}
