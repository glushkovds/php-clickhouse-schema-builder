<?php

namespace PhpClickHouseSchemaBuilder;

class Column implements Element
{
    public function __construct(
        protected string $name,
        protected string $type,
        /**
         * @var Expression|string|int|float|bool|null
         */
        protected mixed $default = null,
        protected ?string $comment = null,
    ) {
    }

    public function compile(): string
    {
        $ddl = [Syntax::escape($this->name), $this->type];
        if (!is_null($this->default)) {
            $ddl[] = 'DEFAULT ' . match (true) {
                    is_string($this->default) => "'$this->default'",
                    $this->default instanceof Expression => $this->default->value,
                    default => $this->default,
                };
        }
        if ($this->comment) {
            $ddl[] = "COMMENT '$this->comment'";
        }
        return implode(' ', $ddl);
    }

    /**
     * @param Expression|string|int|float|bool|null $default
     * @return $this
     */
    public function default(mixed $default = null): Column
    {
        $this->default = $default;
        return $this;
    }

    public function comment(?string $comment): Column
    {
        $this->comment = $comment;
        return $this;
    }
}