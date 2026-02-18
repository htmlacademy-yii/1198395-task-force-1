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

    public const string ACTION_RESPOND = 'action_respond';
    public const string ACTION_START = 'action_start';
    public const string ACTION_CANCEL = 'action_cancel';
    public const string ACTION_GIVE_UP = 'action_give_up';
    public const string ACTION_FINISH = 'action_finish';

    private string $status;
    private ?int $executorId;
    private int $authorId;

    /**
     * Создаёт экземпляр класса Task.
     *
     * @param int $authorId Id заказчика.
     * @param ?string $status Статус задания. По умолчанию задание создаётся в статусе `STATUS_NEW`.
     * @param ?int $executorId Id исполнителя. По умолчанию `null`.
     */
    public function __construct(int $authorId, ?string $status = self::STATUS_NEW, ?int $executorId = null)
    {
        $this->authorId = $authorId;
        $this->status = $status;
        $this->executorId = $executorId;
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
     * @return int|null Id исполнителя или null.
     */
    public function getExecutorId(): int|null
    {
        return $this->executorId;
    }

    /**
     * Получает id заказчика задания.
     *
     * @return int Id заказчика задания.
     */
    public function getOwnerId(): int
    {
        return $this->authorId;
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
        return match ($action) {
            self::ACTION_RESPOND => self::STATUS_NEW,
            self::ACTION_START => self::STATUS_RUNNING,
            self::ACTION_CANCEL => self::STATUS_CANCELED,
            self::ACTION_FINISH => self::STATUS_FINISHED,
            self::ACTION_GIVE_UP => self::STATUS_FAILED,
            default => false,
        };
    }

    /**
     * Получает доступные действия для переданного статуса задания.
     *
     * @param string $status Статус задания.
     *
     * @return array Массив с действиями.
     */
    public function getActions(string $status): array
    {
        return match ($status) {
            self::STATUS_NEW =>
            [
                self::ACTION_START => 'Начать задание',
                self::ACTION_CANCEL => 'Отменить задание',
                self::ACTION_RESPOND => 'Откликнуться на задание'
            ],
            self::STATUS_RUNNING =>
            [
                self::ACTION_FINISH => 'Завершить задание',
                self::ACTION_GIVE_UP => 'Отказаться от задания',
            ],
            default => [],
        };
    }

    /**
     * Получает доступные действия для текущего статуса задания.
     *
     * @return array Доступные действия.
     */
    public function getCurrentActions(): array
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
