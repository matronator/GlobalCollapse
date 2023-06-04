<?php

declare(strict_types=1);

namespace App\Model;

use App\Model\Entity\Item;
use Math;
use Nette;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;
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

    private InventoryRepository $inventoryRepository;

    public function __construct(array $itemsConfig, array $marketConfig, Nette\Database\Explorer $database, InventoryRepository $inventoryRepository)
    {
        $this->itemsConfig = $itemsConfig;
        $this->marketConfig = $marketConfig;
        $this->database = $database;
        $this->inventoryRepository = $inventoryRepository;
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
        return $this->database->table('items')->where('available', true);
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
            $market = $this->findAll()->get($id); // This might return null
            if ($market) {
                return $market;
            }
        }

        /** @var ActiveRow|null $market */
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
                    if ($itemToCopy) {
                        $selectedItem = $this->generateItem($itemToCopy, $market);
                    } else {
                        $selectedItem = $this->selectItem($type);
                    }
                } else {
                    $selectedItem = $this->selectItem($type);
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

    public function buyItem(ActiveRow $marketItem, int $playerId, int $count = 1)
    {
        $market = $this->findAll()->get($marketItem->market_id);
        $playerInventory = $this->inventoryRepository->findByUser($playerId)->fetch();
        $item = $marketItem->item;

        if (!$market || !$playerInventory || !$item) {
            return false;
        }

        $player = $playerInventory->user;

        $emptySlot = $this->inventoryRepository->findEmptySlot($player->id);

        if (!$emptySlot) {
            return false;
        }

        $this->inventoryRepository->findAllInventoryItems($playerInventory->id)->insert([
            'player_inventory_id' => $playerInventory->id,
            'item_id' => $item->id,
            'slot' => $emptySlot,
            'quantity' => $count,
        ]);

        $player->update([
            'money-=' => $item->cost,
        ]);

        $marketItem->update([
            'count-=' => $count,
        ]);

        $item->update([
            'purchased+=' => 1,
        ]);

        if ($marketItem->count <= 0) {
            $marketItem->delete();
        }

        return true;
    }

    public function generateItemName($item): string
    {
        $adjectives = $this->itemsConfig['naming'][$item->rarity]['adjectives'];
        $suffixes = $this->itemsConfig['naming'][$item->rarity]['suffixes'] ?? [];

        $adjective = Strings::capitalize($adjectives[array_rand($adjectives)]);
        $suffix = $suffixes === [] ? '' : Strings::capitalize($suffixes[array_rand($suffixes)]);

        return $adjective . ' ' . $item->name . ' ' . $suffix;
    }

    /**
     * @param string $type
     * @return mixed
     */
    private function selectItem(string $type)
    {
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

        return $itemsForMarket[array_rand($itemsForMarket)];
    }

    /**
     * @param ActiveRow $itemToCopy
     * @param ActiveRow $market
     * @return array|bool|int|iterable|ActiveRow|Selection|\Traversable
     */
    private function generateItem(ActiveRow $itemToCopy, ActiveRow $market)
    {
        $itemId = $itemToCopy->id;
        $itemToCopy->update(['children+=' => 1]);
        $item = $this->findAllItems()->get($itemId)->toArray();
        unset($item['id']);
        $item['rarity'] = Math::getRarity();
        $item['name'] = $this->generateItemName((object)$item);
        $item['is_generated'] = 1;
        $item['children'] = 0;
        $item['cost'] = Math::random($item['cost'] / 2, $item['cost'] * 2, 0.5) * $market->level * self::RARITY_COST_MULTIPLIER[$item['rarity']];
        if ($item['type'] === Item::TYPE_ARMOR) {
            $item['armor'] = (int) round(Math::random($item['armor'] / 2, $item['armor'] * 2, 1) * $market->level * max(1, floor(self::RARITY_COST_MULTIPLIER[$item['rarity']] / 3)));
        } else if ($item['type'] === Item::TYPE_WEAPON) {
            $item['attack'] = (int) round(Math::random($item['attack'] / 2, $item['attack'] * 2, 1) * $market->level * max(1, floor(self::RARITY_COST_MULTIPLIER[$item['rarity']] / 3)));
        }
        foreach (Item::ITEM_STATS as $stat) {
            if (isset($item[$stat]) && $item[$stat] !== null) {
                if ($stat !== 'xp_boost' && $stat !== 'energy_max') {
                    $item[$stat] = (int) round(max(Math::random($item[$stat] / 2, $item[$stat] * 2, 1), 1) * $market->level * max(1, floor(self::RARITY_COST_MULTIPLIER[$item['rarity']] / 3)));
                } else if ($stat === 'xp_boost') {
                    $item[$stat] = round(max(Math::random($item[$stat] / 2, $item[$stat] * 2, 1), 1) * (max($market->level / 8, 0.25)) * max(1, floor(self::RARITY_COST_MULTIPLIER[$item['rarity']] / 3)), 4);
                } else {
                    $item[$stat] = (int) round($item[$stat] * (max($market->level / 7, 5)) * max(1, floor(self::RARITY_COST_MULTIPLIER[$item['rarity']] / 3)));
                }
            }
        }
        $item['unlock_at'] = (int) round(Math::random($item['unlock_at'] / 1.5, $item['unlock_at'] * 1.5, 1.2) * (max($market->level / 3, 1)));

        return $this->findAllItems()->insert($item);
    }
}
