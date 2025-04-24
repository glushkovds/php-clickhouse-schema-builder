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
                    $t->integer('col_int1')->default(0),
                    $t->integer('col_int2', 128),
                    $t->integer('col_int3', 256, false),
                    $t->uInt8('col_int4'),
                    $t->uInt16('col_int5'),
                    $t->uInt32('col_int6'),
                    $t->uInt64('col_int7'),
                    $t->uInt128('col_int8'),
                    $t->uInt256('col_int9'),
                    $t->int8('col_int10'),
                    $t->int8('col_int11'),
                    $t->int16('col_int12'),
                    $t->int32('col_int13'),
                    $t->int64('col_int14'),
                    $t->int128('col_int15'),
                    $t->int256('col_int16'),
                    $t->bool('col_bool')->default(false),
                    $t->date('col_date'),
                    $t->datetime('col_datetime1'),
                    $t->datetime('col_datetime2', 3),
                    $t->float('col_float1'),
                    $t->float32('col_float2'),
                    $t->float64('col_float3'),
                    $t->decimal('col_decimal1', 20, 3),
                    $t->decimal32('col_decimal2', 1),
                    $t->decimal64('col_decimal3', 2),
                    $t->decimal128('col_decimal4', 3),
                    $t->decimal256('col_decimal5', 4),
                    $t->uuid('col_uuid'),
                    $t->enum('col_enum1', ['a', 'b']),
                    $t->enum('col_enum2', ['b' => 1, 'c' => 2]),
                    $t->string('col_nullable')->nullable(),
                    $t->column('col_common1', 'Tuple(UInt8, String)'),
                    $t->column('col_common2', 'Tuple', [new Expression('UInt8'), new Expression('String')]),
                    $t->column('col_common3', 'FixedString', [5]),
                    $t->column('col_common4', 'Object', ['json']),
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