<?php

namespace PhpClickHouseSchemaBuilder\Tables;

use PhpClickHouseSchemaBuilder\AddsColumns;
use PhpClickHouseSchemaBuilder\Column;
use PhpClickHouseSchemaBuilder\Element;
use PhpClickHouseSchemaBuilder\Engine;
use PhpClickHouseSchemaBuilder\Exceptions\IncompleteClickHouseDDLException;
use PhpClickHouseSchemaBuilder\Syntax;
use PhpClickHouseSchemaBuilder\TTL;

class MergeTree implements Element
{
    use AddsColumns;

    /** @var Column[] */
    protected array $columns = [];
    protected bool $ifNotExistsClause = false;
    protected ?string $onCluster = null;
    protected Engine $engine;
    protected ?string $partition = null;
    protected array $orderBy;
    protected ?TTL $ttl = null;
    protected ?string $dbName = null;
    protected array $settings = [];

    public function __construct(protected string $name)
    {
        $this->engine = new Engine();
    }

    public function compile(): string
    {
        $this->validate();
        return $this->compileHead() . ' ' . $this->compileColumns() . "\n" . $this->compileBottom();
    }

    protected function compileHead(): string
    {
        $ddl = ['CREATE TABLE'];
        if ($this->ifNotExistsClause) {
            $ddl[] = 'IF NOT EXISTS';
        }
        $ddl[] = Syntax::escapeName($this->name);
        if ($this->onCluster) {
            $ddl[] = "ON CLUSTER '$this->onCluster'";
        }
        return implode(' ', $ddl);
    }

    protected function compileColumns(): string
    {
        $ddl = ['('];
        $count = count($this->columns);
        foreach ($this->columns as $index => $column) {
            $ddl[] = '  ' . $column->compile() . ($index + 1 === $count ? '' : ',');
        }
        $ddl[] = ')';
        return implode("\n", $ddl);
    }

    protected function compileBottom(): string
    {
        $ddl = ['ENGINE = ' . $this->engine->compile()];
        if ($this->partition) {
            $ddl[] = "PARTITION BY $this->partition";
        }
        $ddl[] = 'ORDER BY (' . implode(', ', array_map([Syntax::class, 'escapeName'], $this->orderBy)) . ')';
        if ($this->ttl) {
            $ddl[] = 'TTL ' . $this->ttl->compile();
        }
        if ($this->settings) {
            $settings = [];
            foreach ($this->settings as $settingName => $settingValue) {
                $settings[] = "$settingName = $settingValue";
            }
            $ddl[] = 'SETTINGS ' . implode(', ', $settings);
        }
        return implode("\n", $ddl);
    }

    protected function validate(): void
    {
        if (!isset($this->engine)) {
            throw new IncompleteClickHouseDDLException("Engine is required");
        }
        if (empty($this->orderBy)) {
            throw new IncompleteClickHouseDDLException("At least one column must be in the ORDER BY clause");
        }
        if (empty($this->columns)) {
            throw new IncompleteClickHouseDDLException("At least one column required");
        }
    }

    public function ifNotExists(bool $addIfNotExistsClause = true): static
    {
        $this->ifNotExistsClause = $addIfNotExistsClause;
        return $this;
    }

    /**
     * @param Engine|string $engine Engine::REPLICATED_MERGE_TREE for example
     * @return $this
     */
    public function engine(Engine|string $engine, ...$params): static
    {
        if (is_string($engine)) {
            $engine = new Engine($engine);
        }
        $this->engine = $engine
            ->params(...$params)
            ->setTableName($this->name)
            ->setDbName($this->dbName);
        return $this;
    }

    public function orderBy(...$orderBy): static
    {
        $this->orderBy = $orderBy;
        return $this;
    }

    public function ttl(string $columnName, string $interval): static
    {
        $this->ttl = new TTL($columnName, $interval);
        return $this;
    }

    public function onCluster(?string $clusterName): static
    {
        $this->onCluster = $clusterName;
        return $this;
    }

    public function dbName(?string $dbName): static
    {
        $this->dbName = $dbName;
        return $this;
    }

    public function columns(callable|array $columns): static
    {
        $this->columns = is_array($columns) ? $columns : $columns($this);
        return $this;
    }

    public function column(string $name, string $type, array $typeParams = []): Column
    {
        if ($typeParams) {
            $ps = [];
            foreach ($typeParams as $param) {
                $ps[] = Syntax::escapeParam($param);
            }
            $type .= '(' . implode(', ', $ps) . ')';
        }
        $column = new Column($name, $type);
        $this->columns[] = $column;
        return $column;
    }

    public function partition(?string $partition): static
    {
        $this->partition = $partition;
        return $this;
    }

    public function settings(array $settings): static
    {
        $this->settings = $settings;
        return $this;
    }
}