<?php

namespace TaskForce\actions;

class CancelAction extends AbstractAction
{
    public function getName(): string
    {
        return 'action_cancel';
    }

    public function getDescription(): string
    {
        return 'Отменить';
    }

    public function checkRights(
        ?int $executorId,
        int $authorId,
        int $userId
    ): bool {
        return is_null($executorId) && $userId === $authorId;
    }
}
