<?php

declare(strict_types=1);

namespace Rahul900day\Csv\Sheet;

use ArrayAccess;
use Illuminate\Support\Arr;
use Rahul900day\Csv\Csv;
use Rahul900day\Csv\Exceptions\ColumnDoesNotExists;
use Rahul900day\Csv\Sheet\Row\Column;

class Row implements ArrayAccess
{
    public function __construct(protected array $record, protected bool|array $sanitize)
    {
    }

    public function getColumn(string|int $column): Column
    {
        $value = Arr::get($this->record, $column);

        return new Csv::$columnClass($value, $this->sanitize);
    }

    public function __get(string $name): Column
    {
        return $this->getColumn($name);
    }

    public function has(string|int $key): bool
    {
        return array_key_exists($key, $this->record);
    }

    public function offsetExists(mixed $offset): bool
    {
        return $this->has($offset);
    }

    public function offsetGet(mixed $offset): ?string
    {
        return $this->getColumn($offset)->getValue();
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->validateKeyExists($offset, 'Unable set, column does not exists in the column list.');

        Arr::set($this->record, $offset, (string) $value);
    }

    public function offsetUnset(mixed $offset): void
    {
        $this->validateKeyExists($offset, 'Unable unset, column does not exists in the column list.');

        Arr::set($this->record, $offset, '');
    }

    public function toArray(): array
    {
        $attributes = [];
        foreach($this->record as $name => $row){
            $attributes[$name] = (new Csv::$columnClass($row, $this->sanitize))->getValue();
        }
        return $attributes;
    }

    protected function validateKeyExists(string $key, ?string $message = null): void
    {
        if ($this->has($key)) {
            return;
        }

        throw new ColumnDoesNotExists($message);
    }
}
