<?php

namespace TaskForce;

abstract class AbstractAction
{
    protected string $name;
    protected string $description;

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    abstract public function checkRights(int $executorId, int $authorId, int $userId): bool;
}

class RespondAction extends AbstractAction
{
    public function __construct()
    {
        $this->name = 'action_respond';
        $this->description = 'Откликнуться';
    }

    public function checkRights(?int $executorId, int $authorId, int $userId): bool
    {
        return is_null($executorId) && $userId !== $authorId;
    }
}

class CancelAction extends AbstractAction
{
    public function __construct()
    {
        $this->name = 'action_cancel';
        $this->description = 'Отменить';
    }

    public function checkRights(?int $executorId, int $authorId, int $userId): bool
    {
        return is_null($executorId) && $userId === $authorId;
    }
}

class StartAction extends AbstractAction
{
    public function __construct()
    {
        $this->name = 'action_start';
        $this->description = 'Начать';
    }

    public function checkRights(?int $executorId, int $authorId, int $userId): bool
    {
        return is_null($executorId) && $userId === $authorId;
    }
}

class GiveUpAction extends AbstractAction
{
    public function __construct()
    {
        $this->name = 'action_give_up';
        $this->description = 'Отказаться';
    }

    public function checkRights(?int $executorId, int $authorId, int $userId): bool
    {
        return $userId === $executorId && $userId !== $authorId;
    }
}

class FinishAction extends AbstractAction
{
    public function __construct()
    {
        $this->name = 'action_finish';
        $this->description = 'Завершить';
    }

    public function checkRights(?int $executorId, int $authorId, int $userId): bool
    {
        return $userId === $authorId && $userId !== $executorId;
    }
}
