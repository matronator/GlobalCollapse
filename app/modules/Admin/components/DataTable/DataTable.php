<?php

declare(strict_types=1);

namespace App\Components\DataTable;

use Nette\Application\UI\Control;
use Nette\Database\Explorer;
use Nette\Database\Table\Selection;

class DataTable extends Control
{
    private $database;
    private Selection $dataSource;
    private array $columns;
    private $data;
    private array $keys;
    private object $sort;

    public function __construct(Explorer $database)
    {
        $this->database = $database;
        $this->sort = (object) ['column' => 'id', 'order' => 'ASC'];
    }

    public function setDataSource(Selection $dataSource)
    {
        $this->dataSource = $dataSource;
        $this->data = $dataSource->fetchAll();
        $this->keys = array_keys($this->data[1]->toArray());
    }

    public function setTable(string $tableName): void
    {
        $this->dataSource = $this->database->table($tableName);
        $this->data = $this->dataSource->fetchAll();
        $this->keys = array_keys($this->data[0]->toArray());
    }

    public function addColumn(string $name, string $label): Column
    {
        if (!in_array($name, $this->keys)) {
            throw new \Exception("Column with name $name doesn't exist");
        }

        if (isset($this->columns[$name])) {
            throw new \Exception("Column with name $name already exists");
        }

        $this->columns[$name] = new Column($name, $label);
        return $this->columns[$name];
    }

    public function render(): void
    {
        $this->template->keys = $this->keys;
        $this->template->columns = $this->columns;
        $this->template->data = $this->data;
        $this->template->sort = $this->sort;

        $this->template->render(__DIR__ . '/DataTable.latte');
    }

    public function handleSort(string $column, string $order = 'ASC')
    {
        $this->sort = (object) ['column' => $column, 'order' => $order];
        $this->data = $this->dataSource->order($column . ' ' . $order)->fetchAll();
        $this->template->data = $this->data;
        $this->template->sort = $this->sort;
        $this->presenter->redrawControl('wrapper');
        $this->presenter->redrawControl('content');
        $this->redrawControl();
    }

    public function handleResetSort()
    {
        $this->sort = (object) ['column' => 'id', 'order' => 'ASC'];
        $this->data = $this->dataSource->order('id ASC')->fetchAll();
        $this->template->data = $this->data;
        $this->template->sort = $this->sort;
        $this->presenter->redrawControl('wrapper');
        $this->presenter->redrawControl('content');
        $this->redrawControl();
    }
}
