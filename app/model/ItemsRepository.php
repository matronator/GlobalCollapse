<?php

declare(strict_types=1);

namespace App\Model;

use Nette;

class ItemsRepository
{
    /** @var Nette\Database\Explorer */
    private $database;

    public const IMAGES_UPLOAD_DIR = '/data/items/';
    public const IMAGES_DIR = '/dist/front/images/items/';

    public function __construct(Nette\Database\Explorer $database)
    {
        $this->database = $database;
    }

    public function findAll()
    {
        return $this->database->table('items');
    }

    public function get(?int $id): ?Nette\Database\Table\ActiveRow
    {
        if (!$id) return null;
        return $this->findAll()->get($id);
    }

    public function getSlotByType(string $type, object $playerBody)
    {
        switch ($type) {
            case 'headgear':
                return [$this->get($playerBody->head), $this->get($playerBody->face)];
            case 'helmet':
                $face = $playerBody->face;
                if ($face) {
                    $headgear = $this->get($face);
                    if ($headgear->subtype === 'headgear') {
                        return [$headgear];
                    }
                }
                return [$this->get($playerBody->head)];
            case 'mask':
                return [$this->get($playerBody->face)];
            case 'chest':
                return [$this->get($playerBody->body)];
            case 'legs':
                return [$this->get($playerBody->legs)];
            case 'boots':
                return [$this->get($playerBody->feet)];
            case 'shoulders':
                return [$this->get($playerBody->shoulders)];
            case 'shield':
                $mainHand = $playerBody->melee ?? $playerBody->ranged;
                if ($mainHand) {
                    $mainHandItem = $this->get($mainHand);
                    if ($mainHandItem->type === 'two-handed-melee' || $mainHandItem->subtype === 'two-handed-ranged') {
                        return [$this->get($playerBody->shield), $mainHandItem];
                    }
                }
                return [$this->get($playerBody->shield)];
            case 'melee':
                return [$this->get($playerBody->melee)];
            case 'ranged':
                return [$this->get($playerBody->ranged)];
            case 'two-handed-melee':
                return [$this->get($playerBody->melee), $this->get($playerBody->shield)];
            case 'two-handed-ranged':
                return [$this->get($playerBody->ranged), $this->get($playerBody->shield)];
            default:
                return [];
        }
    }
}
