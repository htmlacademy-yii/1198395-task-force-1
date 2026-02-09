<?php

class Task
{
    public const string STATUS_NEW = 'new';
    public const string STATUS_CANCELED = 'canceled';
    public const string STATUS_RUNNING = 'running';
    public const string STATUS_FINISHED = 'finished';
    public const string STATUS_FAILED = 'failed';
    public const string ACTION_REJECT = 'action_reject';
    public const string ACTION_FINISH = 'action_finish';
    public const string ACTION_CANCEL = 'action_cancel';
    public const string ACTION_START = 'action_start';

    private string $status = self::STATUS_NEW;
    private int $doerId;
    private int $authorId;

    public function __construct(int $authorId, int $doerId = 0)
    {
        $this->doerId = $doerId;
        $this->authorId = $authorId;
    }

    public function getCurrentStatus(): string
    {
        return $this->status;
    }

    public function getNextStatus(string $action): string
    {
        return match(true) {
            $action === self::ACTION_START => self::STATUS_RUNNING,
            $action === self::ACTION_CANCEL => self::STATUS_CANCELED,
            $action === self::ACTION_FINISH => self::STATUS_FINISHED,
            $action === self::ACTION_REJECT => self::STATUS_FAILED,
            default => 'Несуществующее действие',
        };
    }

    public function getActionsForStatus(string $status): array|string
    {
        return match(true) {
            $status === self::STATUS_NEW =>
                [
                    self::ACTION_START => 'Начать задание',
                    self::ACTION_CANCEL => 'Отменить задание',
                ],
            $status === self::STATUS_RUNNING =>
                [
                    self::ACTION_FINISH => 'Завершить задание',
                    self::ACTION_REJECT => 'Отказаться от задания',
                ],
            $status === self::STATUS_CANCELED => 'Задание отменено',
            $status === self::STATUS_FINISHED => 'Задание завершено',
            $status === self::STATUS_FAILED => 'Исполнитель отказался от задания',
            default => 'Несуществующее состояние',
        };
    }

    public function getCurrentActions(): array|string
    {
        return $this->getActionsForStatus($this->status);
    }
}
