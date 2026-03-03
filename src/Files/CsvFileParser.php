<?php

namespace TaskForce\Files;

use RuntimeException;
use SplFileObject;
use TaskForce\Exceptions\CsvColumnsException;
use TaskForce\Exceptions\SourceFileException;

class CsvFileParser
{
    protected string $file;
    protected SplFileObject $fileObj;
    protected SqlTableData $data;

    /**
     * Создаёт экземпляр класса CsvFileParser.
     *
     * @param string $file Имя файла.
     */
    public function __construct(string $file)
    {
        $this->file = $file;
    }

    public function import(): void
    {
        if (!file_exists($this->file)) {
            throw new SourceFileException('Переданный CSV Файл не существует');
        }

        try {
            $this->fileObj = new SplFileObject($this->file);
        } catch (RuntimeException $exception) {
            throw new SourceFileException('Не удалось открыть заданный файл');
        }

        $this->data = new SqlTableData(
            $this->fileObj->getBasename('.csv'),
            $this->getHeaderData(),
        );

        foreach ($this->getNextLine() as $line) {
            if (count($line) !== count($this->data->getColumns())) {
                throw new CsvColumnsException(
                    'Количество столбцов не совпадает с количеством данных',
                );
            }
            $this->data->addValues($line);
        }
    }

    public function getData(): SqlTableData
    {
        return $this->data;
    }

    private function getHeaderData(): ?array
    {
        $bom = pack('H*', 'EFBBBF');
        if (!preg_match("/^$bom/", $this->fileObj->fread(3))) {
            $this->fileObj->rewind();
        }

        return $this->fileObj->fgetcsv(escape: '');
    }

    private function getNextLine(): ?iterable
    {
        $result = null;

        while (!$this->fileObj->eof()) {
            yield $this->fileObj->fgetcsv(escape: '');
        }

        return $result;
    }
}
