<?php

namespace PhpClickHouseSchemaBuilder;

class TTL implements Element
{
    public function __construct(
        protected string $columnName, protected string $interval,
    ) {
    }

    public function compile(): string
    {
        return Syntax::escape($this->columnName) . ' + INTERVAL ' . $this->interval;
    }
}