<?php

namespace TaskForce\Files;

class CsvFileData
{
    protected string $name;
    protected array $columns;
    protected array $values = [];

    public function __construct(string $name, array $columns)
    {
        $this->name = $name;
        $this->columns = $columns;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getColumns(): array
    {
        return $this->columns;
    }

    public function getValues(): array
    {
        return $this->values;
    }

    public function addValues(array $values): void
    {
        $this->values[] = $values;
    }
}
