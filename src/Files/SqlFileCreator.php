<?php

namespace TaskForce\Files;

use RuntimeException;
use SplFileObject;

use TaskForce\Exceptions\DestinationFileException;

class SqlFileCreator
{
    protected CsvFileData $data;
    protected string $filePath;
    protected SplFileObject $fileObj;

    public function __construct(string $fileName, string $dirPath, CsvFileData $data)
    {
        $this->filePath = $dirPath . $fileName . '.sql';
        $this->data = $data;
    }

    public function create(): void
    {
        if (file_exists($this->filePath)) {
            throw new DestinationFileException(
                'Переданный файл уже существует',
            );
        }

        try {
            $this->fileObj = new SplFileObject(
                $this->filePath,
                'w',
            );
        } catch (RuntimeException $exception) {
            throw new DestinationFileException(
                'Не удалось создать заданный файл',
            );
        }

        $this->fileObj->fwrite(
            $this->addInsertQuery() . $this->addColumns() . $this->addValues(),
        );
    }

    private function addInsertQuery(): string
    {
        return 'INSERT INTO `' . $this->data->getName() . '` ';
    }

    private function addColumns(): string
    {
        $result = '(';
        $isFirst = true;
        foreach ($this->data->getColumns() as $column) {
            if ($isFirst) {
                $result .= '`' . $column . '`';
                $isFirst = false;
            } else {
                $result .= ', `' . $column . '`';
            }
        }
        $result .= ")\n";
        return $result;
    }

    private function addValues(): string
    {
        $result = 'VALUES ';
        $isFirst = true;
        foreach ($this->data->getValues() as $value) {
            if ($isFirst) {
                $result .= '(';
                $isFirst = false;
            } else {
                $result .= ",\n (";
            }
            $isFirstRow = true;
            foreach ($value as $row) {
                if ($isFirstRow) {
                    $result .= '\'' . $row . '\'';
                    $isFirstRow = false;
                } else {
                    $result .= ', \'' . $row . '\'';
                }
            }
            $result .= ')';
        }
        $result .= ';';
        return $result;
    }
}
