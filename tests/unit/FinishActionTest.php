<?php

namespace unit;

use PHPUnit\Framework\TestCase;
use TaskForce\Actions\FinishAction;

final class FinishActionTest extends TestCase
{
    public function testFinishActionIsAvailableForAuthorOnly(): void
    {
        $authorId = 1;
        $executorId = 2;
        $userId = 1;

        $this->assertTrue(
            new FinishAction()->checkRights($executorId, $authorId, $userId),
        );

        $this->assertFalse(
            new FinishAction()->checkRights($executorId, $authorId, 3),
        );
    }
}
