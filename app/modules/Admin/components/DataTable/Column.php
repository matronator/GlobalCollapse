<?php

declare(strict_types=1);

namespace App\Components\DataTable;

class Column
{
    public $name;
    private $label;
    private $sortable = false;
    private $html = '';
    public $renderer = null;
    public $htmlClass = '';

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

    public function setHtmlClass(string $class): self
    {
        $this->htmlClass = $class;
        return $this;
    }

    /** Pass with no arguments to revert back to default renderer */
    public function setRenderer(?callable $renderer = null): self
    {
        $this->renderer = $renderer;
        return $this;
    }

    public function isSortable(): bool
    {
        return $this->sortable;
    }

    public function render($row): string
    {
        return call_user_func($this->renderer, $row);
    }

    public function __toString(): string
    {
        return $this->html !== '' ? $this->html : $this->label;
    }
}
