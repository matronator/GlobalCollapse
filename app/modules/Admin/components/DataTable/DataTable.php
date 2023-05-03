<?php

declare(strict_types=1);

namespace App\Components\DataTable;

use Nette\Application\UI\Control;
use Nette\Database\Explorer;
use Nette\Database\Table\Selection;
use Nette\Http\Session;
use Nette\Utils\Arrays;
use Nette\Utils\Paginator;

class DataTable extends Control
{
    private $database;
    private $session;

    private Selection $dataSource;
    private Selection $original;
    private array $columns;
    private $data;
    private array $keys;
    private object $sort;

    private const DEFAULT_PAGE_SIZE = 20;

    public Paginator $paginator;

    public function __construct(Explorer $database, Session $session)
    {
        $this->database = $database;
        $this->session = $session;
        $this->paginator = new Paginator();
        
        $this->sort = (object) ['column' => $session->getSection('dataTable-sort')->get('column') ?? 'id', 'order' => $session->getSection('dataTable-sort')->get('order') ?? 'ASC'];
        $this->paginator->setItemsPerPage(self::DEFAULT_PAGE_SIZE);

    }

    public function setDataSource(Selection $dataSource)
    {
        $this->initTable($dataSource);
    }

    public function setTable(string $tableName): void
    {
        $dataSource = $this->database->table($tableName);
        $this->initTable($dataSource);
    }

    /** Don't pass `$pageSize` to reset back to default page size (20) */
    public function setPageSize(?int $pageSize = null): void
    {
        $this->paginator->setItemsPerPage($pageSize !== null ? $pageSize : self::DEFAULT_PAGE_SIZE);
        $this->original->limit($this->paginator->getLength(), $this->paginator->getOffset());
        $this->dataSource->limit($this->paginator->getLength(), $this->paginator->getOffset());
        $this->data = $this->dataSource->fetchAll();
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
        $this->template->paginator = $this->paginator;

        $this->template->render(__DIR__ . '/DataTable.latte');
    }

    public function handleSort(string $column, string $order = 'ASC')
    {
        $this->sort = (object) ['column' => $column, 'order' => $order];
        $this->session->getSection('dataTable-sort')->set('column', $column);
        $this->session->getSection('dataTable-sort')->set('order', $order);
        $this->paginator->setPage($this->session->getSection('items-paginator')->get('page'));
        $this->dataSource = $this->original;
        $this->data = $this->dataSource->order($this->sort->column . ' ' . $this->sort->order)->limit($this->paginator->getLength(), $this->paginator->getOffset())->fetchAll();
        $this->template->data = $this->data;
        $this->template->sort = $this->sort;

        $this->presenter->redrawControl('wrapper');
        $this->presenter->redrawControl('content');
        $this->redrawControl();
    }

    public function handleResetSort()
    {
        $this->sort = (object) ['column' => 'id', 'order' => 'ASC'];
        $this->session->getSection('dataTable-sort')->set('column', 'id');
        $this->session->getSection('dataTable-sort')->set('order', 'ASC');
        $this->dataSource = $this->original;
        $this->data = $this->dataSource->order('id ASC')->limit($this->paginator->getLength(), $this->paginator->getOffset())->fetchAll();
        $this->template->data = $this->data;
        $this->template->sort = $this->sort;

        $this->presenter->redrawControl('wrapper');
        $this->presenter->redrawControl('content');
        $this->redrawControl();
    }

    public function handleSetPage(int $page)
    {
        $this->paginator->setPage($page);
        $this->dataSource = $this->original;
        $this->data = $this->dataSource->order($this->sort->column . ' ' . $this->sort->order)->limit($this->paginator->getLength(), $this->paginator->getOffset())->fetchAll();
        $this->template->data = $this->data;

        $section = $this->session->getSection('items-paginator');
        $section->set('page', $page);
        
        $this->presenter->redrawControl('wrapper');
        $this->presenter->redrawControl('content');
        $this->redrawControl();
    }

    private function initTable(Selection $dataSource): void
    {
        $this->dataSource = $dataSource;
        $this->original = clone $dataSource;
        $this->paginator->setItemCount($this->original->count('id'));
        $this->paginator->setPage($this->session->getSection('items-paginator')->get('page') ?? 1);
        $this->dataSource->limit($this->paginator->getLength(), $this->paginator->getOffset());
        $this->data = $dataSource->fetchAll();
        $this->keys = array_keys(is_array($this->data) ? Arrays::first($this->data)->toArray() : $this->data->toArray());
    }
}
