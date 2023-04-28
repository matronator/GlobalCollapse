<?php

declare(strict_types=1);

namespace App\Model\Entity;

class Item
{
    public const TYPE_WEAPON = 'weapon';
    public const TYPE_ARMOR = 'armor';
    public const TYPE_SERUM = 'serum';
    public const TYPE_OTHER = 'other';

    public const ITEM_TYPES = [
        self::TYPE_WEAPON => self::TYPE_WEAPON,
        self::TYPE_ARMOR => self::TYPE_ARMOR,
        self::TYPE_SERUM => self::TYPE_SERUM,
        self::TYPE_OTHER => self::TYPE_OTHER,
    ];

    public const ARMOR_SUBTYPE_HELMET = 'helmet';
    public const ARMOR_SUBTYPE_MASK = 'mask';
    public const ARMOR_SUBTYPE_HEADGEAR = 'headgear';
    public const ARMOR_SUBTYPE_CHEST = 'chest';
    public const ARMOR_SUBTYPE_SHIELD = 'shield';
    public const ARMOR_SUBTYPE_SHOULDERS = 'shoulders';
    public const ARMOR_SUBTYPE_LEGS = 'legs';
    public const ARMOR_SUBTYPE_BOOTS = 'boots';

    public const ARMOR_SUBTYPES = [
        self::ARMOR_SUBTYPE_HELMET => self::ARMOR_SUBTYPE_HELMET,
        self::ARMOR_SUBTYPE_MASK => self::ARMOR_SUBTYPE_MASK,
        self::ARMOR_SUBTYPE_HEADGEAR => self::ARMOR_SUBTYPE_HEADGEAR,
        self::ARMOR_SUBTYPE_CHEST => self::ARMOR_SUBTYPE_CHEST,
        self::ARMOR_SUBTYPE_SHIELD => self::ARMOR_SUBTYPE_SHIELD,
        self::ARMOR_SUBTYPE_SHOULDERS => self::ARMOR_SUBTYPE_SHOULDERS,
        self::ARMOR_SUBTYPE_LEGS => self::ARMOR_SUBTYPE_LEGS,
        self::ARMOR_SUBTYPE_BOOTS => self::ARMOR_SUBTYPE_BOOTS,
    ];

    public const WEAPON_SUBTYPE_MELEE = 'melee';
    public const WEAPON_SUBTYPE_RANGED = 'ranged';
    public const WEAPON_SUBTYPE_TWO_HANDED = 'two-handed';

    public const WEAPON_SUBTYPES = [
        self::WEAPON_SUBTYPE_MELEE => self::WEAPON_SUBTYPE_MELEE,
        self::WEAPON_SUBTYPE_RANGED => self::WEAPON_SUBTYPE_RANGED,
        self::WEAPON_SUBTYPE_TWO_HANDED => self::WEAPON_SUBTYPE_TWO_HANDED,
    ];

    public const ITEM_SUBTYPES = [
        self::TYPE_WEAPON => self::WEAPON_SUBTYPES,
        self::TYPE_ARMOR => self::ARMOR_SUBTYPES,
        self::TYPE_SERUM => [
            self::TYPE_SERUM,
        ],
        self::TYPE_OTHER => [
            self::TYPE_OTHER,
        ],
    ];
}