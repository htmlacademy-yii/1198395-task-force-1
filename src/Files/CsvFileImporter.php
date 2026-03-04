<?php

namespace TaskForce\Files;

use RuntimeException;
use SplFileObject;
use TaskForce\Exceptions\CsvColumnsException;
use TaskForce\Exceptions\SourceFileException;

class CsvFileImporter
{
    protected string $filePath;
    protected SplFileObject $fileObj;
    protected CsvFileData $data;

    /**
     * Создаёт экземпляр класса CsvFileParser.
     *
     * @param string $filePath Путь к файлу.
     */
    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    public function import(): void
    {
        if (!file_exists($this->filePath)) {
            throw new SourceFileException('Переданный файл не существует');
        }

        try {
            $this->fileObj = new SplFileObject($this->filePath);
        } catch (RuntimeException $exception) {
            throw new SourceFileException('Не удалось открыть заданный файл');
        }

        $this->data = new CsvFileData(
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

    public function getData(): CsvFileData
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
