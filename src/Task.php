<?php

namespace TaskForce;

/**
 * Задание - центральная сущность приложения TaskForce.
 *
 * Хранит состояние задания, доступные действия с ним, id заказчика и исполнителя.
 */
class Task
{
    public const string STATUS_NEW = 'status_new';
    public const string STATUS_CANCELED = 'status_canceled';
    public const string STATUS_RUNNING = 'status_running';
    public const string STATUS_FINISHED = 'status_finished';
    public const string STATUS_FAILED = 'status_failed';

    public const string ACTION_START = 'action_start';
    public const string ACTION_CANCEL = 'action_cancel';
    public const string ACTION_REJECT = 'action_reject';
    public const string ACTION_FINISH = 'action_finish';

    private string $status = self::STATUS_NEW;
    private int $contractorId = 0;
    private int $ownerId;

    /**
     * Создаёт экземпляр класса Task.
     *
     * @param int $ownerId Id заказчика.
     */
    public function __construct(int $ownerId)
    {
        $this->ownerId = $ownerId;
    }

    /**
     * Получает текущий статус задания.
     *
     * @return string Текущий статус.
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * Получает id исполнителя задания.
     *
     * @return int Id исполнителя.
     */
    public function getContractorId(): int
    {
        return $this->contractorId;
    }

    /**
     * Получает id заказчика задания.
     *
     * @return int Id заказчика задачи.
     */
    public function getOwnerId(): int
    {
        return $this->ownerId;
    }

    /**
     * Получает статус, в который перейдёт задание после примененного действия.
     *
     * @param string $action Действие с заданием.
     *
     * @return string|false Статус задания, либо `false`, если действие невозможно.
     */
    public function getNextStatus(string $action): string|false
    {
        return match($action) {
            self::ACTION_START => self::STATUS_RUNNING,
            self::ACTION_CANCEL => self::STATUS_CANCELED,
            self::ACTION_FINISH => self::STATUS_FINISHED,
            self::ACTION_REJECT => self::STATUS_FAILED,
            default => false,
        };
    }

    /**
     * Получает доступные действия для переданного статуса задания.
     *
     * @param string $status Статус задания.
     *
     * @return array|false Массив с действиями, либо `false`, если переданного статуса нет в классе.
     */
    public function getActions(string $status): array|false
    {
        return match($status) {
            self::STATUS_NEW =>
                [
                    self::ACTION_START => 'Начать задание',
                    self::ACTION_CANCEL => 'Отменить задание',
                ],
            self::STATUS_RUNNING =>
                [
                    self::ACTION_FINISH => 'Завершить задание',
                    self::ACTION_REJECT => 'Отказаться от задания',
                ],
            self::STATUS_CANCELED, self::STATUS_FINISHED, self::STATUS_FAILED => [],
            default => false,
        };
    }

    /**
     * Получает доступные действия для текущего статуса задания.
     *
     * @return array|false Доступные действия.
     */
    public function getCurrentActions(): array|false
    {
        return $this->getActions($this->status);
    }

    /**
     * Применяет действие к заданию, если оно возможно для текущего статуса.
     *
     * @param string $action Действие.
     *
     * @return bool `true` - действие применилось, `false` - действие невозможно.
     */
    public function applyAction(string $action): bool
    {
        $result = false;

        $currentActions = $this->getCurrentActions();

        if (array_key_exists($action, $currentActions)) {
            $this->status = $this->getNextStatus($action);
            $result = true;
        }

        return $result;
    }
}
