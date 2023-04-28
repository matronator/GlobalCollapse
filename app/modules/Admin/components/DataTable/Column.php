<?php

declare(strict_types=1);

namespace App\Components\DataTable;

class Column
{
    public $name;
    private $label;
    private $sortable = false;
    private $html = '';

    public function __construct(string $name, string $label)
    {
        $this->name = $name;
        $this->label = $label;
    }

    public function setSortable(bool $sortable = true): self
    {
        $this->sortable = $sortable;
        return $this;
    }

    public function setHtml(string $html): self
    {
        $this->html = $html;
        return $this;
    }

    public function isSortable(): bool
    {
        return $this->sortable;
    }

    public function render(): string
    {
        return $this->html !== '' ? $this->html : $this->label;
    }

    public function __toString(): string
    {
        return $this->html !== '' ? $this->html : $this->label;
    }
}
