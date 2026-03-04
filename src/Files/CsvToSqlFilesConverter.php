<?php

namespace TaskForce\Files;

use FilesystemIterator;
use RuntimeException;
use SplFileInfo;
use TaskForce\Exceptions\CsvColumnsException;
use TaskForce\Exceptions\DestinationFileException;
use TaskForce\Exceptions\SourceDirectoryException;
use TaskForce\Exceptions\SourceFileException;

class CsvToSqlFilesConverter
{
    private string $sourceDir;
    private string $destinationDir;
    private FilesystemIterator $fileIterator;

    public function __construct(string $sourceDir, string $destinationDir)
    {
        $this->sourceDir = $sourceDir;
        $this->destinationDir = $destinationDir;
    }

    public function convert(): void
    {
        if (!is_dir($this->sourceDir)) {
            throw new SourceDirectoryException('Переданной директории не существует');
        }

        try {
            $this->fileIterator = new FilesystemIterator($this->sourceDir);
        } catch (RuntimeException $exception) {
            throw new SourceDirectoryException('Не удалось открыть переданную директорию');
        }

        foreach ($this->fileIterator as $fileInfo) {
            $this->convertFile($fileInfo);
        }
    }

    private function convertFile(SplFileInfo $fileInfo): void
    {
        $csvFileImporter = new CsvFileImporter($fileInfo->getPathname());

        try {
            $csvFileImporter->import();
        } catch (SourceFileException $exception) {
            throw new SourceDirectoryException($exception->getMessage());
        } catch (CsvColumnsException $exception) {
            throw new CsvColumnsException($exception->getMessage());
        }

        $sqlFileCreator = new SqlFileCreator(
            $fileInfo->getBasename('.csv'),
            $this->destinationDir,
            $csvFileImporter->getData(),
        );

        try {
            $sqlFileCreator->create();
        } catch (DestinationFileException $exception) {
            throw new DestinationFileException($exception->getMessage());
        }
    }
}
