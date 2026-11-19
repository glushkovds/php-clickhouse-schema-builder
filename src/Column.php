<?php

namespace PhpClickHouseSchemaBuilder;

class Column implements Element
{
    /**
     * @var Expression|string|int|float|bool|null
     */
    protected mixed $default = null;
    protected ?string $comment = null;
    protected bool $nullable = false;
    protected bool $lowCardinality = false;
    protected ?string $codec = null;

    public function __construct(
        protected string $name,
        protected string $type,
    ) {
    }

    public function compile(): string
    {
        $ddl = [
            Syntax::escapeName($this->name),
            $this->compileType(),
        ];
        if (!is_null($this->default)) {
            $ddl[] = 'DEFAULT ' . Syntax::escapeParam($this->default);
        }
        if ($this->comment) {
            $ddl[] = 'COMMENT ' . Syntax::escapeString($this->comment);
        }
        if ($this->codec) {
            $ddl[] = "CODEC($this->codec)";
        }
        return implode(' ', $ddl);
    }

    protected function compileType(): string
    {
        $type = $this->nullable ? "Nullable($this->type)" : $this->type;
        return $this->lowCardinality ? "LowCardinality($type)" : $type;
    }

    /**
     * @param Expression|string|int|float|bool|null $default
     * @return $this
     */
    public function default(mixed $default = null): static
    {
        $this->default = $default;
        return $this;
    }

    public function comment(?string $comment): static
    {
        $this->comment = $comment;
        return $this;
    }

    /**
     * Dictionary encoding, for columns with up to ~10 000 distinct values.
     * @link https://clickhouse.com/docs/en/sql-reference/data-types/lowcardinality
     */
    public function lowCardinality(bool $lowCardinality = true): static
    {
        $this->lowCardinality = $lowCardinality;
        return $this;
    }

    public function nullable($nullable = true): static
    {
        $this->nullable = $nullable;
        return $this;
    }

    /**
     * @param string|null $codec codecs without CODEC(), e.g. 'DoubleDelta, ZSTD(1)'
     */
    public function codec(?string $codec): static
    {
        $this->codec = $codec;
        return $this;
    }
}
