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

    public function __construct(
        protected string $name,
        protected string $type,
    ) {
    }

    public function compile(): string
    {
        $ddl = [
            Syntax::escapeName($this->name),
            $this->nullable ? "Nullable($this->type)" : $this->type,
        ];
        if (!is_null($this->default)) {
            $ddl[] = 'DEFAULT ' . Syntax::escapeParam($this->default);
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

    public function nullable($nullable = true): static
    {
        $this->nullable = $nullable;
        return $this;
    }
}