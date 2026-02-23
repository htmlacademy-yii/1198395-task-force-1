<?php

namespace TaskForce\actions;

abstract class AbstractAction
{
    abstract public function getName(): string;

    abstract public function getDescription(): string;

    abstract public function checkRights(
        int $executorId,
        int $authorId,
        int $userId
    ): bool;
}
