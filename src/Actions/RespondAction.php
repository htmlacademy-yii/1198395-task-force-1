<?php

namespace TaskForce\Actions;

class RespondAction extends AbstractAction
{
    public function getName(): string
    {
        return 'action_respond';
    }

    public function getDescription(): string
    {
        return 'Откликнуться';
    }

    public function checkRights(
        ?int $executorId,
        int $authorId,
        int $userId
    ): bool {
        return is_null($executorId) && $userId !== $authorId;
    }
}