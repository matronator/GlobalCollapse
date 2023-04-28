<?php

declare(strict_types=1);

namespace App\Components\DataTable;

interface DataTableFactory
{
	public function create(): DataTable;
}
