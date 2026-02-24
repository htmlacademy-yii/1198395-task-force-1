<?php

namespace TaskForce;

use TaskForce\actions\AbstractAction;
use TaskForce\actions\CancelAction;
use TaskForce\actions\FinishAction;
use TaskForce\actions\RefuseAction;
use TaskForce\actions\RespondAction;
use TaskForce\actions\StartAction;

/**
 * Задание - центральная сущность приложения TaskForce.
 *
 * Хранит состояние задания, доступные действия с ним,
 * id заказчика и исполнителя.
 */
class Task
{
    public const string STATUS_NEW = 'status_new';
    public const string STATUS_CANCELED = 'status_canceled';
    public const string STATUS_ACTIVE = 'status_active';
    public const string STATUS_FINISHED = 'status_finished';
    public const string STATUS_FAILED = 'status_failed';

    public string $status;
    private ?int $executorId;
    private int $authorId;

    /**
     * Создаёт экземпляр класса Task.
     *
     * @param int    $authorId   Id заказчика.
     * @param string $status     Статус задания. По умолчанию задание
     *                           создаётся в статусе `STATUS_NEW`.
     * @param ?int   $executorId Id исполнителя. По умолчанию `null`.
     */
    public function __construct(
        int $authorId,
        string $status = self::STATUS_NEW,
        ?int $executorId = null,
    ) {
        $this->authorId = $authorId;
        $this->status = $status;
        $this->executorId = $executorId;
    }

    /**
     * Получает статус, в который перейдёт задание после примененного действия.
     *
     * @param AbstractAction $action Объект класса AbstractAction
     *
     * @return string Статус задания, либо пустая строка,
     * если действие не предусмотрено.
     */
    public function getNextStatus(
        AbstractAction $action,
    ): string {
        return match ($action->getName()) {
            new RespondAction()->getName() => self::STATUS_NEW,
            new StartAction()->getName() => self::STATUS_ACTIVE,
            new CancelAction()->getName() => self::STATUS_CANCELED,
            new FinishAction()->getName() => self::STATUS_FINISHED,
            new RefuseAction()->getName() => self::STATUS_FAILED,
            default => '',
        };
    }

    /**
     * Получает доступные действия над заданием для пользователя по статусу задания и Id.
     *
     * @param string $status Статус задания.
     * @param int $userId Id пользователя.
     *
     * @return array Массив с объектами-потомками класса AbstractAction.
     */
    public function getActions(string $status, int $userId): array
    {
        $actions = match ($status) {
            self::STATUS_NEW =>
            [
                new StartAction(),
                new CancelAction(),
                new RespondAction(),
            ],
            self::STATUS_ACTIVE =>
            [
                new FinishAction(),
                new RefuseAction(),
            ],
            default => [],
        };

        return array_filter($actions, function ($action) use ($userId) {
            return $action->checkRights(
                $this->executorId,
                $this->authorId,
                $userId,
            );
        });
    }

    /**
     * Применяет действие к заданию, если оно возможно для текущего статуса.
     *
     * @param AbstractAction $action Действие.
     * @param array          $data   Данные о пользователе, применяющем действие.
     *
     * @return bool `true` - действие применилось, `false` - действие невозможно.
     */
    public function applyAction(AbstractAction $action, array $data): bool
    {
        $result = false;

        if (isset($data['userId'])) {
            $currentActionsNames = array_map(
                function ($action) {
                    return $action->getName();
                },
                $this->getActions($this->status, $data['userId']),
            );

            if (in_array($action->getName(), $currentActionsNames)
            ) {
                if ($action->getName() === new StartAction()->getName()
                    && isset($data['executorId'])
                ) {
                    $this->executorId = $data['executorId'];
                }

                $this->status = $this->getNextStatus($action);
                $result = true;
            }
        }

        return $result;
    }
}
