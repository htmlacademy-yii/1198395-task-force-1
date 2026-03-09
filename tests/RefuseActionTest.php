<?php

use PHPUnit\Framework\TestCase;
use TaskForce\Actions\RefuseAction;

class RefuseActionTest extends TestCase
{
    public function testRefuseActionIsAvailableForExecutorOnly(): void
    {
        $authorId = 1;
        $executorId = 2;
        $userId = 2;

        $this->assertTrue(
            new RefuseAction()->checkRights($executorId, $authorId, $userId),
        );

        $this->assertFalse(
            new RefuseAction()->checkRights($executorId, $authorId, 1),
        );
    }
}
