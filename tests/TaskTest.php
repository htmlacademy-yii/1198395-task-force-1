<?php

use PHPUnit\Framework\TestCase;
use TaskForce\Actions\CancelAction;
use TaskForce\Actions\FinishAction;
use TaskForce\Actions\RefuseAction;
use TaskForce\Actions\RespondAction;
use TaskForce\Actions\StartAction;
use TaskForce\Task;

final class TaskTest extends TestCase
{
    public function testInitialTaskStatusIsSetFromConstructor(): void
    {
        $this->assertSame(
            Task::STATUS_NEW,
            new Task(1, Task::STATUS_NEW)->status,
        );
        $this->assertSame(
            Task::STATUS_ACTIVE,
            new Task(1, Task::STATUS_ACTIVE, 2)->status,
        );
        $this->assertSame(
            Task::STATUS_CANCELED,
            new Task(1, Task::STATUS_CANCELED)->status,
        );
        $this->assertSame(
            Task::STATUS_FAILED,
            new Task(1, Task::STATUS_FAILED, 2)->status,
        );
        $this->assertSame(
            Task::STATUS_FINISHED,
            new Task(1, Task::STATUS_FINISHED, 2)->status,
        );
    }

    public function testNextStatusAfterRespondIsNew(): void
    {
        $this->assertSame(
            Task::STATUS_NEW,
            new Task(1)->getNextStatus(new RespondAction()),
        );
    }

    public function testNextStatusAfterStartIsActive(): void
    {
        $this->assertSame(
            Task::STATUS_ACTIVE,
            new Task(1)->getNextStatus(new StartAction()),
        );
    }

    public function testNextStatusAfterCancelIsCanceled(): void
    {
        $this->assertSame(
            Task::STATUS_CANCELED,
            new Task(1)->getNextStatus(new CancelAction()),
        );
    }

    public function testNextStatusAfterFinishIsFinished(): void
    {
        $this->assertSame(
            Task::STATUS_FINISHED,
            new Task(1)->getNextStatus(new FinishAction()),
        );
    }

    public function testNextStatusAfterRefuseIsFailed(): void
    {
        $this->assertSame(
            Task::STATUS_FAILED,
            new Task(1)->getNextStatus(new RefuseAction()),
        );
    }

    public function testNewTaskReturnsStartAndCancelActionsForAuthor(): void
    {
        try {
            $taskActions = new Task(1)->getActions(Task::STATUS_NEW, 1);
        } catch (Exception $exception) {
            $this->fail($exception->getMessage());
        }

        $this->assertSame(
            [new StartAction()->getName(), new CancelAction()->getName()],
            array_map(function ($action) {
                return $action->getName();
            }, $taskActions),
        );
    }

    public function testNewTaskReturnsRespondActionForExecutor(): void
    {
        try {
            $taskActions = new Task(1)->getActions(Task::STATUS_NEW, 2);
        } catch (Exception $exception) {
            $this->fail($exception->getMessage());
        }

        $this->assertEqualsCanonicalizing(
            [new RespondAction()->getName()],
            array_map(function ($action) {
                return $action->getName();
            }, $taskActions),
        );
    }

    public function testActiveTaskReturnsFinishActionForAuthor(): void
    {
        try {
            $taskActions = new Task(1, Task::STATUS_ACTIVE)->getActions(
                Task::STATUS_ACTIVE,
                1,
            );
        } catch (Exception $exception) {
            $this->fail($exception->getMessage());
        }


        $this->assertEqualsCanonicalizing(
            [new FinishAction()->getName()],
            array_map(function ($action) {
                return $action->getName();
            }, $taskActions),
        );
    }

    public function testActiveTaskReturnsRefuseActionForExecutor(): void
    {
        try {
            $taskActions = new Task(1, Task::STATUS_ACTIVE, 2)->getActions(
                Task::STATUS_ACTIVE,
                2,
            );
        } catch (Exception $exception) {
            $this->fail($exception->getMessage());
        }


        $this->assertEqualsCanonicalizing(
            [new RefuseAction()->getName()],
            array_map(function ($action) {
                return $action->getName();
            }, $taskActions),
        );
    }

    public function testFinishedTaskReturnsEmptyActions(): void
    {
        try {
            $taskActions = new Task(1, Task::STATUS_FINISHED)->getActions(
                Task::STATUS_FINISHED,
                1,
            );
        } catch (Exception $exception) {
            $this->fail($exception->getMessage());
        }
        $this->assertEmpty($taskActions);
    }

    public function testCanceledTaskReturnsEmptyActions(): void
    {
        try {
            $taskActions = new Task(1, Task::STATUS_CANCELED)->getActions(
                Task::STATUS_CANCELED,
                1,
            );
        } catch (Exception $exception) {
            $this->fail($exception->getMessage());
        }

        $this->assertEmpty($taskActions);
    }

    public function testFailedTaskReturnsEmptyActions(): void
    {
        try {
            $taskActions = new Task(1, Task::STATUS_FAILED)->getActions(
                Task::STATUS_FAILED,
                1,
            );
        } catch (Exception $exception) {
            $this->fail($exception->getMessage());
        }

        $this->assertEmpty($taskActions);
    }

    public function testRespondActionAppliesOnlyOnNewTasks(): void
    {
        try {
            $isAppliedOnNewTask = new Task(1)->applyAction(
                new RespondAction(),
                ['userId' => 2, 'executorId' => 1],
            );
        } catch (Exception $exception) {
            $this->fail($exception->getMessage());
        }
        $this->assertTrue($isAppliedOnNewTask);

        try {
            $isAppliedOnActiveTask = new Task(
                1, Task::STATUS_ACTIVE
            )->applyAction(
                new RespondAction(),
                ['userId' => 2, 'executorId' => 1],
            );
        } catch (Exception $exception) {
            $this->fail($exception->getMessage());
        }
        $this->assertFalse($isAppliedOnActiveTask);
    }

    public function testRespondActionAppliesOnlyWhenExecutorIsNotSet(): void
    {
        try {
            $isAppliedWhenExecutorIsNotSet = new Task(
                1, Task::STATUS_NEW, 3
            )->applyAction(
                new RespondAction(),
                ['userId' => 2, 'executorId' => 1],
            );
        } catch (Exception $exception) {
            $this->fail($exception->getMessage());
        }
        $this->assertFalse($isAppliedWhenExecutorIsNotSet);
    }

    public function testStartActionChangesTaskStatusToActive(): void
    {
        $task = new Task(1);
        try {
            $task->applyAction(
                new StartAction(),
                ['userId' => 1, 'executorId' => 2],
            );
        } catch (Exception $exception) {
            $this->fail($exception->getMessage());
        }
        $this->assertSame(
            Task::STATUS_ACTIVE,
            $task->status,
        );
    }

    public function testCancelActionChangesTaskStatusToCanceled(): void
    {
        $task = new Task(1);
        try {
            $task->applyAction(
                new CancelAction(),
                ['userId' => 1],
            );
        } catch (Exception $exception) {
            $this->fail($exception->getMessage());
        }
        $this->assertSame(
            Task::STATUS_CANCELED,
            $task->status,
        );
    }

    public function testFinishActionChangesTaskStatusToFinished(): void
    {
        $task = new Task(1, Task::STATUS_ACTIVE);
        try {
            $task->applyAction(
                new FinishAction(),
                ['userId' => 1],
            );
        } catch (Exception $exception) {
            $this->fail($exception->getMessage());
        }
        $this->assertSame(
            Task::STATUS_FINISHED,
            $task->status,
        );
    }

    public function testRefuseActionChangesTaskStatusToFailed(): void
    {
        $task = new Task(1, Task::STATUS_ACTIVE, 2);
        try {
            $task->applyAction(
                new RefuseAction(),
                ['userId' => 2],
            );
        } catch (Exception $exception) {
            $this->fail($exception->getMessage());
        }
        $this->assertSame(
            Task::STATUS_FAILED,
            $task->status,
        );
    }
}
