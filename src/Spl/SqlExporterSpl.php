<?php

namespace TaskForce\Spl;

use RuntimeException;
use SplFileObject;

use TaskForce\Exceptions\DestinationFileException;

class SqlExporterSpl
{
    protected SqlTableData $data;
    protected string $destination;
    protected SplFileObject $fileObj;

    public function __construct(string $destination, SqlTableData $data)
    {
        $this->destination = $destination;
        $this->data = $data;
    }

    public function export(): void
    {
        if (file_exists($this->destination)) {
            throw new DestinationFileException(
                'Переданный файл уже существует'
            );
        }

        try {
            $this->fileObj = new SplFileObject($this->destination, 'w');
        } catch (RuntimeException $exception) {
            throw new DestinationFileException(
                'Не удалось создать заданный файл'
            );
        }
    }

    private function addInsertQuery(): string
    {
        return 'INSERT INTO ' . $this->data->getName();
    }

    private function addColumns(): string
    {
        $result = '(';
        $isFirst = true;
        foreach ($this->data->getColumns() as $column) {
            if ($isFirst) {
                $result .= $column . ', ';
                $isFirst = false;
            } else {
                $result .= $column;
            }
        }
        $result .= ')';
        return $result;
    }
}
