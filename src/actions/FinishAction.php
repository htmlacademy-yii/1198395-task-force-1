<?php

namespace TaskForce\actions;

class FinishAction extends AbstractAction
{
    public function getName(): string
    {
        return 'action_finish';
    }
    
    public function getDescription(): string
    {
        return 'Завершить';
    }

    public function checkRights(
        ?int $executorId,
        int $authorId,
        int $userId
    ): bool {
        return $userId === $authorId && $userId !== $executorId;
    }
}