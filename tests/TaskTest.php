<?php

use PHPUnit\Framework\TestCase;
use TaskForce\actions\CancelAction;
use TaskForce\actions\FinishAction;
use TaskForce\actions\StartAction;
use TaskForce\actions\RefuseAction;
use TaskForce\actions\RespondAction;
use TaskForce\Task;

require_once __DIR__ . '/../init.php';

final class TaskTest extends TestCase
{
    public function testInitialTaskStatusIsSetFromConstructor(): void
    {
        $this->assertSame(
            Task::STATUS_NEW,
            new Task(1, Task::STATUS_NEW)->status
        );
        $this->assertSame(
            Task::STATUS_ACTIVE,
            new Task(1, Task::STATUS_ACTIVE)->status
        );
        $this->assertSame(
            Task::STATUS_CANCELED,
            new Task(1, Task::STATUS_CANCELED)->status
        );
        $this->assertSame(
            Task::STATUS_FAILED,
            new Task(1, Task::STATUS_FAILED)->status
        );
        $this->assertSame(
            Task::STATUS_FINISHED,
            new Task(1, Task::STATUS_FINISHED)->status
        );
    }

    public function testNextStatusAfterRespondIsNew(): void
    {
        $this->assertSame(
            Task::STATUS_NEW,
            new Task(1)->getNextStatus(new RespondAction())
        );
    }

    public function testNextStatusAfterStartIsActive(): void
    {
        $this->assertSame(
            Task::STATUS_ACTIVE,
            new Task(1)->getNextStatus(new StartAction())
        );
    }

    public function testNextStatusAfterCancelIsCanceled(): void
    {
        $this->assertSame(
            Task::STATUS_CANCELED,
            new Task(1)->getNextStatus(new CancelAction())
        );
    }

    public function testNextStatusAfterFinishIsFinished(): void
    {
        $this->assertSame(
            Task::STATUS_FINISHED,
            new Task(1)->getNextStatus(new FinishAction())
        );
    }

    public function testNextStatusAfterRefuseIsFailed(): void
    {
        $this->assertSame(
            Task::STATUS_FAILED,
            new Task(1)->getNextStatus(new RefuseAction())
        );
    }

    public function testNewTaskReturnsStartAndCancelActionsForAuthor(): void
    {
        $taskActions = new Task(1)->getActions(1);

        $this->assertSame(
            [new StartAction()->getName(), new CancelAction()->getName()],
            array_map(function ($action) {
                return $action->getName();
            }, $taskActions)
        );
    }

    public function testNewTaskReturnsRespondActionForExecutor(): void
    {
        $taskActions = new Task(1)->getActions(2);

        $this->assertEqualsCanonicalizing(
            [new RespondAction()->getName()],
            array_map(function ($action) {
                return $action->getName();
            }, $taskActions)
        );
    }

    public function testActiveTaskReturnsFinishActionForAuthor(): void
    {
        $taskActions = new Task(1, Task::STATUS_ACTIVE)->getActions(1);

        $this->assertEqualsCanonicalizing(
            [new FinishAction()->getName()],
            array_map(function ($action) {
                return $action->getName();
            }, $taskActions)
        );
    }

    public function testActiveTaskReturnsRefuseActionForExecutor(): void
    {
        $taskActions = new Task(1, Task::STATUS_ACTIVE, 2)->getActions(2);

        $this->assertEqualsCanonicalizing(
            [new RefuseAction()->getName()],
            array_map(function ($action) {
                return $action->getName();
            }, $taskActions)
        );
    }

    public function testFinishedTaskReturnsEmptyActions(): void
    {
        $this->assertEmpty(
            new Task(1, Task::STATUS_FINISHED)->getActions(1)
        );
    }

    public function testCanceledTaskReturnsEmptyActions(): void
    {
        $this->assertEmpty(
            new Task(1, Task::STATUS_CANCELED)->getActions(1)
        );
    }

    public function testFailedTaskReturnsEmptyActions(): void
    {
        $this->assertEmpty(
            new Task(1, Task::STATUS_FAILED)->getActions(1)
        );
    }

    public function testRespondActionAppliesOnlyOnNewTasks(): void
    {
        $this->assertTrue(
            new Task(1)->applyAction(
                new RespondAction(),
                ['userId' => 2, 'executorId' => 2]
            )
        );
        $this->assertFalse(
            new Task(1, Task::STATUS_ACTIVE)->applyAction(
                new RespondAction(),
                ['userId' => 2, 'executorId' => 2]
            )
        );
    }

    public function testRespondActionAppliesOnlyWhenExecutorIsNotSet(): void
    {
        $this->assertFalse(
            new Task(1, Task::STATUS_NEW, 3)->applyAction(
                new RespondAction(),
                ['userId' => 2, 'executorId' => 2]
            )
        );
    }

    public function testStartActionChangesTaskStatusToActive(): void
    {
        $task = new Task(1);
        $task->applyAction(new StartAction(), ['userId' => 1, 'executorId' => 2]
        );
        $this->assertSame(
            Task::STATUS_ACTIVE,
            $task->status
        );
    }

    public function testCancelActionChangesTaskStatusToCanceled(): void
    {
        $task = new Task(1);
        $task->applyAction(new CancelAction(), ['userId' => 1]
        );
        $this->assertSame(
            Task::STATUS_CANCELED,
            $task->status
        );
    }

    public function testFinishActionChangesTaskStatusToFinished(): void
    {
        $task = new Task(1, Task::STATUS_ACTIVE);
        $task->applyAction(new FinishAction(), ['userId' => 1]
        );
        $this->assertSame(
            Task::STATUS_FINISHED,
            $task->status
        );
    }

    public function testRefuseActionChangesTaskStatusToFailed(): void
    {
        $task = new Task(1, Task::STATUS_ACTIVE, 2);
        $task->applyAction(new RefuseAction(), ['userId' => 2]
        );
        $this->assertSame(
            Task::STATUS_FAILED,
            $task->status
        );
    }
}
