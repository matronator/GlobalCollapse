<?php

declare(strict_types=1);

namespace App\Model;

use App\Model\Entity\Item;
use Math;
use Nette;
use Strings;

class MarketRepository
{
    /** @var Nette\Database\Explorer */
    private $database;

    private array $itemsConfig;
    private array $marketConfig;

    public const FEE_MIN = 0;
    public const FEE_MAX = 20;

    public const RARITY_COST_MULTIPLIER = [
        Item::RARITY_COMMON => 1,
        Item::RARITY_RARE => 3,
        Item::RARITY_EPIC => 5,
        Item::RARITY_LEGENDARY => 10,
    ];

    public function __construct(array $itemsConfig, array $marketConfig, Nette\Database\Explorer $database)
    {
        $this->itemsConfig = $itemsConfig;
        $this->marketConfig = $marketConfig;
        $this->database = $database;
    }

    public function findAll()
    {
        return $this->database->table('market');
    }

    public function findAllMarketItems()
    {
        return $this->database->table('market_items');
    }

    public function findAllItems()
    {
        return $this->database->table('items');
    }

    public function findAllItemsInMarket(int $marketId)
    {
        return $this->findAllMarketItems()->where('market_id', $marketId);
    }

    public function getMarketByPlayerLevel(int $playerLevel)
    {
        $level = (int) max(min(round($playerLevel / 15), 20), 1);
        return $this->getMarket($level);
    }

    public function getMarket(int $level = 1, ?int $id = null)
    {
        if ($id) {
            $market = $this->findAll()->get($id);
            if ($market) {
                return $market;
            }
        }

        $market = $this->findAll()->order('id DESC')->limit(1)->where('level', $level)->fetch();
        if (!$market) {
            $market = $this->createMarket($level);
            $this->fillMarketWithItems($market->id);
        }

        return $market;
    }

    public function createMarket(int $level = 1): Nette\Database\Table\ActiveRow
    {
        return $this->findAll()->insert([
            'fee' => Math::random(self::FEE_MIN, self::FEE_MAX, 0.75),
            'level' => $level,
        ]);
    }

    public function fillMarketWithItems(int $marketId)
    {
        $market = $this->findAll()->get($marketId);

        if (!$market) {
            return false;
        }
        
        $slots = $this->getMarketSlotsCount();
        $minChildren = $this->findAllItems()->min('children');

        foreach ($slots as $type => $count) {
            for ($i = 0; $i < $count; $i++) {
                if (Math::random(0, 3) < 2 && $minChildren < 5) {
                    $itemToCopy = $this->findAllItems()->where('is_generated', 0)->where('rarity', 'common')->where('type', $type)
                        ->where('children <= ?', (int) $minChildren)->order('RAND()')->limit(1)->fetch();
                    $itemId = $itemToCopy->id;
                    $itemToCopy->update(['children+=' => 1]);
                    $item = $this->findAllItems()->get($itemId)->toArray();
                    unset($item['id']);
                    $item['rarity'] = Math::getRarity();
                    $item['name'] = $this->generateItemName((object) $item);
                    $item['is_generated'] = 1;
                    $item['children'] = 0;
                    $item['cost'] = Math::random($item['cost'] / 2, $item['cost'] * 2, 0.5) * Math::random(self::RARITY_COST_MULTIPLIER[$item['rarity']] * 0.5, self::RARITY_COST_MULTIPLIER[$item['rarity']] * 2, 0.8);
                    if ($item['type'] === Item::TYPE_ARMOR) {
                        $item['armor'] = (int) round(Math::random($item['armor'] / 2, $item['armor'] * 2, 1) * max(1, floor(self::RARITY_COST_MULTIPLIER[$item['rarity']] / 2)));
                    } else if ($item['type'] === Item::TYPE_WEAPON) {
                        $item['attack'] = (int) round(Math::random($item['attack'] / 2, $item['attack'] * 2, 1) * max(1, floor(self::RARITY_COST_MULTIPLIER[$item['rarity']] / 2)));
                    }
                    $item['unlock_at'] = Math::random($item['unlock_at'] / 1.5, $item['unlock_at'] * 1.5, 1.2);

                    $selectedItem = $this->findAllItems()->insert($item);
                } else {
                    $rarity = Math::getRarity();
                    $items = $this->findAllItems()->where('is_generated', 0)->where('rarity', $rarity)->where('type', $type)
                        ->order('market_drop_rate')->fetchAll();
                    if (!$items) {
                        $items = $this->findAllItems()->where('is_generated', 0)->where('rarity', 'common')->where('type', $type)
                            ->order('market_drop_rate')->fetchAll();
                    }
                    $itemsForMarket = [];
                    foreach ($items as $itm) {
                        for ($j = 0; $j < $itm->market_drop_rate; $j++) {
                            $itemsForMarket[] = $itm;
                        }
                    }

                    $selectedItem = $itemsForMarket[array_rand($itemsForMarket)];
                }
                
                $this->findAllMarketItems()->insert([
                    'market_id' => $marketId,
                    'items_id' => $selectedItem->id,
                    'market_slot' => $type,
                    'count' => min($market->level * 5, 20),
                ]);
            }
        }
    }

    public function getMarketSlotsCount()
    {
        $slots = $this->marketConfig['slots'];

        $armorSlots = Math::random($slots['armor'][0], $slots['armor'][1]);
        $weaponSlots = Math::random($slots['weapon'][0], $slots['weapon'][1]);
        $miscSlots = Math::random($slots['misc'][0], $slots['misc'][1]);

        return [
            "armor" => $armorSlots,
            "weapon" => $weaponSlots,
            // "misc" => $miscSlots,
        ];
    }
    
    public function generateItemName($item): string
    {
        $adjectives = $this->itemsConfig['naming'][$item->rarity]['adjectives'];
        $suffixes = $this->itemsConfig['naming'][$item->rarity]['suffixes'] ?? [];
        
        $adjective = Strings::capitalize($adjectives[array_rand($adjectives)]);
        $suffix = $suffixes === [] ? '' : Strings::capitalize($suffixes[array_rand($suffixes)]);

        return $adjective . ' ' . $item->name . ' ' . $suffix;
    }
}
