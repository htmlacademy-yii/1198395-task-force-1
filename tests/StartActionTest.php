<?php

use PHPUnit\Framework\TestCase;
use TaskForce\Actions\StartAction;

require_once __DIR__ . '/../init.php';

class StartActionTest extends TestCase
{
    public function testStartActionIsAvailableForAuthorOnly(): void
    {
        $authorId = 1;
        $executorId = null;
        $userId = 1;

        $this->assertTrue(
            new StartAction()->checkRights($executorId, $authorId, $userId)
        );

        $this->assertFalse(
            new StartAction()->checkRights($executorId, $authorId, 2)
        );
    }
}