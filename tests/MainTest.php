<?php

namespace Tests;

use PhpClickHouseSchemaBuilder\Engine;
use PhpClickHouseSchemaBuilder\Expression;
use PhpClickHouseSchemaBuilder\Tables\MergeTree;
use PHPUnit\Framework\TestCase;

class MainTest extends TestCase
{

    public function testSimple()
    {
        $this->assertMergeTree(
            (new MergeTree('some_table'))
                ->dbName('some_db')
                ->columns(fn(MergeTree $t) => [
                    $t->string('col_one'),
                ])
                ->orderBy('col_one'),
            'simple_table'
        );
    }

    public function testColumns()
    {
        $this->assertMergeTree(
            (new MergeTree('some_table'))
                ->dbName('some_db')
                ->columns(fn(MergeTree $t) => [
                    $t->string('col_one')->default('5')->comment('some comment'),
                    $t->string('col_two')->default(new Expression('col_one')),
                ])
                ->orderBy('col_one'),
            'table_with_columns'
        );
    }

    public function testOnCluster()
    {
        $this->assertMergeTree(
            (new MergeTree('some_table'))
                ->dbName('some_db')
                ->columns(fn(MergeTree $t) => [
                    $t->string('col_one'),
                ])
                ->orderBy('col_one')
                ->onCluster('some_cluster'),
            'table_on_cluster'
        );
    }

    public function testReplacingMergeTree()
    {
        $this->assertMergeTree(
            (new MergeTree('some_table'))
                ->dbName('some_db')
                ->columns(fn(MergeTree $t) => [
                    $t->string('col_one'),
                ])
                ->orderBy('col_one')
                ->engine(Engine::REPLACING_MERGE_TREE, 'col_one'),
            'table_replacing_merge_tree'
        );
    }

    public function testReplicatedMergeTree()
    {
        $this->assertMergeTree(
            (new MergeTree('some_table'))
                ->dbName('some_db')
                ->columns(fn(MergeTree $t) => [
                    $t->string('col_one'),
                ])
                ->orderBy('col_one')
                ->engine((new Engine())->replicated()),
            'table_replicated_merge_tree'
        );
    }

    public function testReplicatedReplacingMergeTree()
    {
        $this->assertMergeTree(
            (new MergeTree('some_table'))
                ->dbName('some_db')
                ->columns(fn(MergeTree $t) => [
                    $t->string('col_one'),
                ])
                ->orderBy('col_one')
                ->engine((new Engine(Engine::REPLACING_MERGE_TREE))->replicated(), 'col_one'),
            'table_replicated_replacing_merge_tree'
        );
    }

    public function testPartitioned()
    {
        $this->assertMergeTree(
            (new MergeTree('some_table'))
                ->columns(fn(MergeTree $t) => [
                    $t->string('col_one'),
                    $t->datetime('at'),
                ])
                ->orderBy('col_one')
                ->partition('toDate(at)'),
            'partitioned_table'
        );
    }

    public function testOrderBy()
    {
        $this->assertMergeTree(
            (new MergeTree('some_table'))
                ->columns(fn(MergeTree $t) => [
                    $t->string('col_one'),
                    $t->datetime('at'),
                ])
                ->orderBy('col_one', 'at'),
            'table_with_order_by'
        );
    }

    public function testTTL()
    {
        $this->assertMergeTree(
            (new MergeTree('some_table'))
                ->columns(fn(MergeTree $t) => [
                    $t->string('col_one'),
                    $t->datetime('at'),
                ])
                ->orderBy('col_one')
                ->ttl('at', '1 month'),
            'table_with_ttl'
        );
    }

    public function testMergeTreeSettings()
    {
        $this->assertMergeTree(
            (new MergeTree('some_table'))
                ->columns(fn(MergeTree $t) => [
                    $t->string('col_one'),
                ])
                ->orderBy('col_one')
                ->settings(['ttl_only_drop_parts' => 1, 'index_granularity' => 8192]),
            'table_with_settings'
        );
    }

    protected function assertMergeTree(MergeTree $table, string $precompiledFile): void
    {
        $actual = $table->compile();
        echo "\n$actual\n";
        $expected = file_get_contents(__DIR__ . "/compilations/$precompiledFile.sql");
        $this->assertEquals(
            str_replace([' ', "\n"], '', $expected),
            str_replace([' ', "\n"], '', $actual),
        );
    }
}