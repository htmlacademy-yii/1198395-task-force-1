<?php

use PHPUnit\Framework\TestCase;
use TaskForce\Actions\RespondAction;

class RespondActionTest extends TestCase
{
    public function testRespondActionIsAvailableForExecutorOnly(): void
    {
        $authorId = 1;
        $executorId = null;
        $userId = 2;

        $this->assertTrue(
            new RespondAction()->checkRights($executorId, $authorId, $userId),
        );

        $this->assertFalse(
            new RespondAction()->checkRights($executorId, $authorId, 1),
        );
    }
}
