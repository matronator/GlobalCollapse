<?php

declare(strict_types=1);

namespace App\Model;

use App\Model\Entity\Item;
use Math;
use Nette;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;
use Strings;
use Tracy\Debugger;

class MarketRepository
{
    /** @var Nette\Database\Explorer */
    private $database;

    private StatisticsRepository $statisticsRepository;

    private array $itemsConfig;
    private array $marketConfig;

    public const FEE_MIN = 0;
    public const FEE_MAX = 20;
    public const MARKET_LEVEL_MULTIPLIER = 15;

    public const RARITY_COST_MULTIPLIER = [
        Item::RARITY_COMMON => 1,
        Item::RARITY_RARE => 3,
        Item::RARITY_EPIC => 5,
        Item::RARITY_LEGENDARY => 10,
    ];

    private InventoryRepository $inventoryRepository;

    public function __construct(array $itemsConfig, array $marketConfig, Nette\Database\Explorer $database, InventoryRepository $inventoryRepository, StatisticsRepository $statisticsRepository)
    {
        $this->itemsConfig = $itemsConfig;
        $this->marketConfig = $marketConfig;
        $this->database = $database;
        $this->inventoryRepository = $inventoryRepository;
        $this->statisticsRepository = $statisticsRepository;
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
        $level = (int) max(min(round($playerLevel / self::MARKET_LEVEL_MULTIPLIER), 20), 1);
        return $this->getMarket($level);
    }

    public function getItemPrice(ActiveRow $marketItem): int
    {
        return (int) round($marketItem->item->cost + ($marketItem->item->cost * ($marketItem->market->fee / 100)));
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

    public function getItemQuantityByRarity(string $rarity): int
    {
        $rarityToQuantity = [
            Item::RARITY_COMMON => 15,
            Item::RARITY_RARE => 8,
            Item::RARITY_EPIC => 4,
            Item::RARITY_LEGENDARY => 2,
        ];

        return $rarityToQuantity[$rarity];
    }

    public function getItemStatChanceByRarity(string $rarity, int $statNumber): bool
    {
        $statNumber = max($statNumber, 1);
        $chanceToGetNewStat = [
            Item::RARITY_COMMON => max(1 / $statNumber, 0),
            Item::RARITY_RARE => max(20 / $statNumber, 0),
            Item::RARITY_EPIC => max(40 / $statNumber, 0),
            Item::RARITY_LEGENDARY => max(60 / $statNumber, 0),
        ];

        $num = $chanceToGetNewStat[$rarity];
        $array = [];
        for ($i = 0; $i <= 100 - $num; $i++) {
            if ($i <= $num) {
                $array[] = true;
            } else {
                $array[] = false;
            }
        }
        shuffle($array);

        return $array[array_rand($array)];
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

        $price = $this->getItemPrice($marketItem) * $count;

        $this->statisticsRepository->findByUser($player->id)->update([
            'money_to_market+=' => $price,
            'items_bought+=' => $count,
        ]);

        $player->update([
            'money-=' => $price,
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

    public function sellItem(ActiveRow $inventoryItem, int $marketId, int $count = 1)
    {
        $playerInventory = $inventoryItem->player_inventory;
        $item = $inventoryItem->item;

        if (!$playerInventory || !$item) {
            return false;
        }

        $player = $playerInventory->user;

        $price = $this->getMarketSellPrice($item->id, $marketId) * $count;

        $this->statisticsRepository->findByUser($player->id)->update([
            'money_from_market+=' => $price,
            'items_sold+=' => $count,
        ]);

        $player->update([
            'money+=' => $price,
        ]);

        $inventoryItem->update([
            'quantity-=' => $count,
        ]);

        $item->update([
            'sold+=' => 1,
        ]);

        if ($inventoryItem->quantity <= 0) {
            $inventoryItem->delete();
        }

        return true;
    }

    public function generateItemName($item): string
    {
        $adjectives = $this->itemsConfig['naming'][$item->rarity]['adjectives'];
        $suffixes = $this->itemsConfig['naming'][$item->rarity]['suffixes'] ?? [];

        if ($item->rarity === Item::RARITY_COMMON) {
            $adjective = rand(0,3) <= 2 ? Strings::capitalize($adjectives[array_rand($adjectives)]) . ' ' : '';
            $suffix = $suffixes === [] ? '' : Strings::capitalize($suffixes[array_rand($suffixes)]);
            if ($suffix !== '') {
                $suffix = rand(0,3) <= 2 ? ' ' . $suffix : '';
            }
        } else {
            $adjective = Strings::capitalize($adjectives[array_rand($adjectives)]) . ' ';
            $suffix = $suffixes === [] ? '' : ' ' . Strings::capitalize($suffixes[array_rand($suffixes)]);
        }

        return $adjective . $item->name . $suffix;
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
        $minChildren = $this->findAllItems()->where('is_generated', 0)->min('children');

        foreach ($slots as $type => $count) {
            for ($i = 0; $i < $count; $i++) {
                if ($minChildren < 5 && rand(0, 3) < 2) {
                    $itemToCopy = $this->findAllItems()->where('is_generated', 0)->where('rarity', Math::getRarity())->where('type', $type)
                        ->where('children <= ?', (int) $minChildren)->order('RAND()')->limit(1)->fetch();
                    if ($itemToCopy) {
                        if ($market->level > 1) {
                            $selectedItem = $this->generateItem($itemToCopy, $market);
                        } else {
                            $selectedItem = $itemToCopy;
                        }
                    } else {
                        $itemToCopy = $this->findAllItems()->where('is_generated', 0)->where('rarity', 'common')->where('type', $type)
                            ->where('children <= ?', (int) $minChildren)->order('RAND()')->limit(1)->fetch();
                        if ($itemToCopy) {
                            if ($market->level > 1) {
                                $selectedItem = $this->generateItem($itemToCopy, $market);
                            } else {
                                $selectedItem = $itemToCopy;
                            }
                        } else {
                            $selectedItem = $this->generateItem($this->getRandomItem($type, $market), $market);
                        }
                    }
                } else {
                    $selectedItem = $this->getRandomItem($type, $market, true);
                }

                $itemCount = $type === 'misc' ? min(rand($market->level * 2, $market->level * 6), $this->getItemQuantityByRarity($selectedItem->rarity) * 4) : min(rand(1, $market->level * 3), $this->getItemQuantityByRarity($selectedItem->rarity));

                $this->findAllMarketItems()->insert([
                    'market_id' => $marketId,
                    'items_id' => $selectedItem->id,
                    'market_slot' => $type,
                    'count' => $itemCount,
                ]);
            }
        }

        return true;
    }

    /**
     * @param string $type
     * @return mixed
     */
    private function getRandomItem(string $type, ActiveRow $market, bool $isGenerated = false)
    {
        $rarity = Math::getRarity();
        $items = $this->findAllItems()->where('is_generated', $isGenerated)->where('rarity', $rarity)->where('type', $type)
            ->order('RAND()')->fetchAll();
        if (!$items) {
            $items = $this->findAllItems()->where('is_generated', $isGenerated)->where('rarity', 'common')->where('type', $type)
                ->order('RAND()')->fetchAll();
            if (!$items) {
                $copy = $this->findAllItems()->where('is_generated', 0)->where('rarity', $rarity)->where('type', $type)
                    ->order('RAND()')->limit(1)->fetch();
                if (!$copy) {
                    $copy = $this->findAllItems()->where('is_generated', 0)->where('rarity', 'common')->where('type', $type)
                        ->order('RAND()')->limit(1)->fetch();
                }
                $generated = $this->generateItem($copy, $market);
                $items = [$generated];
            }
        }
        $itemsForMarket = [];
        foreach ($items as $itm) {
            for ($j = 0; $j < $itm->market_drop_rate; $j++) {
                $itemsForMarket[] = $itm;
            }
        }

        return $itemsForMarket[array_rand($itemsForMarket)];
    }

    public function getMarketSellPrice(int $itemId, int $marketId): int
    {
        $item = $this->database->table('items')->get($itemId);
        if (!$item) {
            return 0;
        }

        $market = $this->database->table('market')->get($marketId);
        if (!$market) {
            return 0;
        }

        return (int) round(($item->cost * 0.75) * (1 - ($market->fee / 100)));
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
        $item['cost'] = (int) round(Math::random($item['cost'], $item['cost'] * 2, 0.7) * $market->level * self::RARITY_COST_MULTIPLIER[$item['rarity']]);
        if ($item['type'] === Item::TYPE_ARMOR) {
            $item['armor'] = (int) round(Math::random(max($item['armor'] / 2, 1), $item['armor'] * 2, 0.55) * $market->level * max(1, floor(max((self::RARITY_COST_MULTIPLIER[$item['rarity']] * 2) / 3, 1))));
        } else if ($item['type'] === Item::TYPE_WEAPON) {
            $item['attack'] = (int) round(Math::random(max($item['attack'] / 2, 1), $item['attack'] * 2, 0.55) * $market->level * max(1, floor(max((self::RARITY_COST_MULTIPLIER[$item['rarity']] * 2) / 3, 1))));
        }
        $statCount = 0;
        foreach (Item::ITEM_STATS as $stat) {
            if (isset($item[$stat])) {
                $statCount++;
            }
        }
        foreach (Item::ITEM_STATS as $stat) {
            $getNewStat = $this->getItemStatChanceByRarity($item['rarity'], $statCount);
            $wasSet = isset($item[$stat]);
            if ($wasSet || $getNewStat) {
                if ($stat !== 'xp_boost' && $stat !== 'energy_max') {
                    $item[$stat] = (int) round(max(Math::random(max($item[$stat] / 2, 1), $item[$stat] * 2, 0.55), 1) * $market->level * max(1, floor(max((self::RARITY_COST_MULTIPLIER[$item['rarity']] * 2) / 3, 1))));
                } else if ($stat === 'xp_boost') {
                    $item[$stat] = round(max(Math::random(max($item[$stat] / 2, 1), $item[$stat] * 2, 0.55), 1) * (max($market->level / 8, 0.25)) * max(1, floor(max((self::RARITY_COST_MULTIPLIER[$item['rarity']] * 2) / 3, 1))), 4);
                } else {
                    $item[$stat] = (int) round($item[$stat] * (max($market->level / 7, 5)) * max(1, floor(max((self::RARITY_COST_MULTIPLIER[$item['rarity']] * 2) / 3, 1))));
                }

                if (!$wasSet && $getNewStat) {
                    $statCount++;
                }
            }
        }
        if ($market->level >= 20) {
            $item['unlock_at'] = (int) round(Math::random($item['unlock_at'] / 1.5, $item['unlock_at'] * 1.5, 0.75) * (max($market->level / 3, 1)));
        } else {
            $item['unlock_at'] = (int) min(round(Math::random($item['unlock_at'] / 1.5, $item['unlock_at'] * 1.5, 0.75) * (max($market->level / 3, 1))), $market->level * self::MARKET_LEVEL_MULTIPLIER);
        }

        return $this->findAllItems()->insert($item);
    }
}
