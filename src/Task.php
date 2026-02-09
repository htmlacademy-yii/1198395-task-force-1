<?php

class Task
{
    public const string STATUS_NEW = 'status_new';
    public const string STATUS_CANCELED = 'status_canceled';
    public const string STATUS_RUNNING = 'status_running';
    public const string STATUS_FINISHED = 'status_finished';
    public const string STATUS_FAILED = 'status_failed';
    public const string ACTION_REJECT = 'action_reject';
    public const string ACTION_FINISH = 'action_finish';
    public const string ACTION_CANCEL = 'action_cancel';
    public const string ACTION_START = 'action_start';

    private string $status = self::STATUS_NEW;
    private int|null $doerId;
    private int $authorId;

    public function __construct(int $authorId)
    {
        $this->doerId = null;
        $this->authorId = $authorId;
    }

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

    public function getActions(string $status, int $userId): array|string|false
    {
        $result = false;

        switch ($status) {
            case self::STATUS_NEW:
                $result = $userId === $this->authorId
                    ? [self::ACTION_CANCEL => 'Отменить задачу']
                    : [self::ACTION_START => 'Начать задачу'];
                break;
            case self::STATUS_RUNNING:
                if ($userId === $this->authorId) {
                    $result = [self::ACTION_FINISH => 'Завершить задачу'];
                    break;
                }

                if ($userId === $this->doerId) {
                    $result = [self::ACTION_REJECT => 'Отказаться от задачи'];
                    break;
                }
                break;
            case self::STATUS_CANCELED || self::STATUS_FINISHED || self::STATUS_FAILED:
                $result = 'Нет доступных действий';
                break;
        }

        return $result;
    }

    public function updateStatus(string $action, int $userId): bool
    {
        $result = false;
        $actions = $this->getActions($this->status, $userId);

        if(is_array($actions) && array_key_exists($action, $actions)) {
            if ($this->status === self::STATUS_NEW && $action === self::ACTION_START) {
                $this->doerId = $userId;
            }
            $this->status = $this->getNextStatus($action);
            $result = true;
        }

        return $result;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getDoerId(): int|null
    {
        return $this->doerId;
    }
}
