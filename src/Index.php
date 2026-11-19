<?php

namespace PhpClickHouseSchemaBuilder;

/**
 * Data skipping index.
 * @link https://clickhouse.com/docs/en/optimize/skipping-indexes
 */
class Index implements Element
{
    /**
     * @param string $expression column name or expression, e.g. length(col)
     * @param string $type e.g. minmax, set(100), bloom_filter, bloom_filter(0.01)
     */
    public function __construct(
        protected string $name,
        protected string $expression,
        protected string $type,
        protected ?int $granularity = null,
    ) {
    }

    public function compile(): string
    {
        $ddl = ['INDEX', Syntax::escapeName($this->name), $this->expression, 'TYPE', $this->type];
        if ($this->granularity !== null) {
            $ddl[] = "GRANULARITY $this->granularity";
        }
        return implode(' ', $ddl);
    }
}
