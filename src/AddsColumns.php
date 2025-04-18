<?php

namespace PhpClickHouseSchemaBuilder;

trait AddsColumns
{
    public function string(string $name): Column
    {
        $column = new Column($name, 'String');
        $this->columns[] = $column;
        return $column;
    }

    public function datetime(string $name): Column
    {
        $column = new Column($name, 'DateTime');
        $this->columns[] = $column;
        return $column;
    }
}