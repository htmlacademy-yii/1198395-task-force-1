<?php

use PHPUnit\Framework\TestCase;
use TaskForce\Actions\CancelAction;

require_once __DIR__ . '/../init.php';

final class CancelActionTest extends TestCase
{
    public function testCancelActionIsAvailableForAuthorOnly(): void
    {
        $authorId = 1;
        $executorId = null;
        $userId = 1;

        $this->assertTrue(
            new CancelAction()->checkRights($executorId, $authorId, $userId)
        );

        $this->assertFalse(
            new CancelAction()->checkRights($executorId, $authorId, 3)
        );
    }
}