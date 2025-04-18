<?php

namespace PhpClickHouseSchemaBuilder;

interface Element
{
    public function compile(): string;
}