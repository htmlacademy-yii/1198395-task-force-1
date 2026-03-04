<?php

namespace TaskForce\Files;

/**
 * Хранит данные из CSV файла.
 */
class CsvFileData
{
    public string $name {
        get => $this->name;
    }
    public array $columns {
        get => $this->columns;
    }
    public array $values
        = [] {
            get => $this->values;
            set(array $values) {
                $this->values[] = $values;
            }
        }

    /**
     * Создаёт экземпляр класса CsvFileData.
     *
     * @param string $name    Имя файла.
     * @param array  $columns Столбцы-названия рядов.
     */
    public function __construct(string $name, array $columns)
    {
        $this->name = $name;
        $this->columns = $columns;
    }
}
