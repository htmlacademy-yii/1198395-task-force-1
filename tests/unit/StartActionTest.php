<?php

namespace unit;

use PHPUnit\Framework\TestCase;
use TaskForce\Actions\StartAction;

class StartActionTest extends TestCase
{
    public function testStartActionIsAvailableForAuthorOnly(): void
    {
        $authorId = 1;
        $executorId = null;
        $userId = 1;

        $this->assertTrue(
            new StartAction()->checkRights($executorId, $authorId, $userId),
        );

        $this->assertFalse(
            new StartAction()->checkRights($executorId, $authorId, 2),
        );
    }
}
