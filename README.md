# ClickHouse schema builder written in PHP

## Usage
```php
$ddl = (new MergeTree('some_table'))
    ->dbName('some_db') // optional, for replicated table engine 
    ->onCluster('some_cluster')
    ->columns(fn(MergeTree $t) => [
        $t->string('col_one'),
        $t->datetime('at'),
    ])
    ->orderBy('col_one')
    ->partition('toDate(at)')
    ->ttl('at', '1 month')
    ->engine((new Engine(Engine::REPLACING_MERGE_TREE))->replicated(), 'col_one')
    ->settings(['ttl_only_drop_parts' => 1, 'index_granularity' => 8192])
    ->compile();
```