<?php

namespace PhpClickHouseSchemaBuilder;

use PhpClickHouseSchemaBuilder\Exceptions\IncompleteClickHouseDDLException;

class Engine implements Element
{
    public const MERGE_TREE = 'MergeTree';
    public const REPLACING_MERGE_TREE = 'ReplacingMergeTree';

    protected bool $isReplicated = false;
    protected ?string $dbName = null;
    protected ?string $tableName = null;
    protected string $replicaName = '{replica}';
    protected array $params = [];

    public function __construct(
        protected string $type = self::MERGE_TREE,
    ) {
    }

    public function compile(): string
    {
        $this->validate();
        if (!$this->isReplicated) {
            return "$this->type(" . implode(', ', $this->params) . ')';
        }
        $params = array_merge(
            [
                "'/clickhouse/tables/$this->dbName.$this->tableName'",
                "'$this->replicaName'",
            ],
            $this->params,
        );
        return "Replicated$this->type(" . implode(', ', $params) . ")";
    }

    protected function validate()
    {
        if (
            $this->isReplicated
            && (empty($this->dbName) || empty($this->tableName))
        ) {
            throw new IncompleteClickHouseDDLException("DB name and table name is required for ReplicatedMergeTree family table");
        }
    }

    public function replicated(bool $isReplicated = true): static
    {
        $this->isReplicated = $isReplicated;
        return $this;
    }


    public function params(...$params): Engine
    {
        $this->params = $params;
        return $this;
    }

    public function isReplicated(): bool
    {
        return $this->isReplicated;
    }

    public function setDbName(?string $dbName): static
    {
        $this->dbName = $dbName;
        return $this;
    }

    public function setTableName(?string $tableName): static
    {
        $this->tableName = $tableName;
        return $this;
    }

    public function setReplicaName(string $replicaName): Engine
    {
        $this->replicaName = $replicaName;
        return $this;
    }
}