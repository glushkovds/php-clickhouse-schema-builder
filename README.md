![Tests](https://github.com/glushkovds/php-clickhouse-schema-builder/actions/workflows/test.yml/badge.svg)

# ClickHouse schema builder written in PHP

## Requirements

- PHP 8.0

## Installation

**1.**  Install via composer

```sh
$ composer require glushkovds/php-clickhouse-schema-builder
```

## Usage
```php
use PhpClickHouseSchemaBuilder\Tables\MergeTree;

$ddl = (new MergeTree('some_table'))
    ->dbName('some_db') // optional, for replicated table engine 
    ->onCluster('some_cluster')
    ->columns(fn(MergeTree $t) => [
        $t->string('col_one')->default('5')->comment('some comment'),
        $t->datetime('at'),
    ])
    ->orderBy('col_one')
    ->partition('toDate(at)')
    ->ttl('at', '1 month')
    ->engine((new Engine(Engine::REPLACING_MERGE_TREE))->replicated(), 'col_one')
    ->settings(['ttl_only_drop_parts' => 1, 'index_granularity' => 8192])
    ->compile();
```