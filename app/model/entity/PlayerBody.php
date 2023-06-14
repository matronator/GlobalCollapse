<?php

declare(strict_types=1);

namespace App\Model\Entity;

class PlayerBody
{
    public const SLOT_HEAD = 'head';
    public const SLOT_FACE = 'face';
    public const SLOT_BODY = 'body';
    public const SLOT_SHOULDERS = 'shoulders';
    public const SLOT_MELEE = 'melee';
    public const SLOT_RANGED = 'ranged';
    public const SLOT_SHIELD = 'shield';
    public const SLOT_LEGS = 'legs';
    public const SLOT_FEET = 'feet';

    public const BODY_SLOTS = [
        self::SLOT_HEAD => self::SLOT_HEAD,
        self::SLOT_FACE => self::SLOT_FACE,
        self::SLOT_BODY => self::SLOT_BODY,
        self::SLOT_SHOULDERS => self::SLOT_SHOULDERS,
        self::SLOT_MELEE => self::SLOT_MELEE,
        self::SLOT_RANGED => self::SLOT_RANGED,
        self::SLOT_SHIELD => self::SLOT_SHIELD,
        self::SLOT_LEGS => self::SLOT_LEGS,
        self::SLOT_FEET => self::SLOT_FEET,
    ];

    public const ALLOWED_ITEMS = [
        self::SLOT_HEAD => [
            Item::TYPE_ARMOR => [
                Item::ARMOR_SUBTYPE_HELMET,
            ],
        ],
        self::SLOT_FACE => [
            Item::TYPE_ARMOR => [
                Item::ARMOR_SUBTYPE_MASK,
                Item::ARMOR_SUBTYPE_HEADGEAR,
            ],
        ],
        self::SLOT_BODY => [
            Item::TYPE_ARMOR => [
                Item::ARMOR_SUBTYPE_CHEST,
            ],
        ],
        self::SLOT_SHOULDERS => [
            Item::TYPE_ARMOR => [
                Item::ARMOR_SUBTYPE_SHOULDERS,
            ],
        ],
        self::SLOT_MELEE => [
            Item::TYPE_WEAPON => [
                Item::WEAPON_SUBTYPE_MELEE,
                Item::WEAPON_SUBTYPE_TWO_HANDED_MELEE,
            ],
        ],
        self::SLOT_RANGED => [
            Item::TYPE_WEAPON => [
                Item::WEAPON_SUBTYPE_RANGED,
                Item::WEAPON_SUBTYPE_TWO_HANDED_RANGED,
            ],
        ],
        self::SLOT_SHIELD => [
            Item::TYPE_ARMOR => [
                Item::ARMOR_SUBTYPE_SHIELD,
            ],
            ITEM::TYPE_WEAPON => [
                Item::WEAPON_SUBTYPE_TWO_HANDED_RANGED,
                Item::WEAPON_SUBTYPE_TWO_HANDED_MELEE,
            ]
        ],
        self::SLOT_LEGS => [
            Item::TYPE_ARMOR => [
                Item::ARMOR_SUBTYPE_LEGS,
            ],
        ],
        self::SLOT_FEET => [
            Item::TYPE_ARMOR => [
                Item::ARMOR_SUBTYPE_BOOTS,
            ],
        ],
    ];
}
