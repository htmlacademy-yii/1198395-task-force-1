<?php

use PHPUnit\Framework\TestCase;
use TaskForce\Task;

require_once __DIR__ . '/../init.php';

final class TaskTest extends TestCase
{
    public function testInitialOwnerIdIsSetFromConstructor(): void
    {
        $ownerId = 1;

        $task = new Task($ownerId);

        $this->assertSame($ownerId, $task->getOwnerId());
    }

    public function testInitialTaskStatusIsNew(): void
    {
        $task = new Task(1);

        $expectedStatus = Task::STATUS_NEW;

        $this->assertSame($expectedStatus, $task->getStatus());
    }

    public function testNextStatusAfterStartIsRunning(): void
    {
        $task = new Task(1);

        $expectedStatus = Task::STATUS_RUNNING;
        $action = Task::ACTION_START;

        $this->assertSame($expectedStatus, $task->getNextStatus($action));
    }

    public function testNextStatusAfterCancelIsCanceled(): void
    {
        $task = new Task(1);

        $expectedStatus = Task::STATUS_CANCELED;
        $action = Task::ACTION_CANCEL;

        $this->assertSame($expectedStatus, $task->getNextStatus($action));
    }

    public function testNextStatusAfterFinishIsFinished(): void
    {
        $task = new Task(1);

        $expectedStatus = Task::STATUS_FINISHED;
        $action = Task::ACTION_FINISH;

        $this->assertSame($expectedStatus, $task->getNextStatus($action));
    }

    public function testNextStatusAfterRejectIsFailed(): void
    {
        $task = new Task(1);

        $expectedStatus = Task::STATUS_FAILED;
        $action = Task::ACTION_REJECT;

        $this->assertSame($expectedStatus, $task->getNextStatus($action));
    }

    public function testNextStatusReturnsFalseIfInvalidActionIsApplied(): void
    {
        $task = new Task(1);

        $status = 'some_invalid_status';

        $this->assertFalse($task->getNextStatus($status));
    }

    public function testNewTaskReturnsStartAndCancelActions(): void
    {
        $task = new Task(1);

        $expectedActions =
            [
                Task::ACTION_START => 'Начать задание',
                Task::ACTION_CANCEL => 'Отменить задание',
            ];
        $status = Task::STATUS_NEW;

        $this->assertSame($expectedActions, $task->getActions($status));
    }

    public function testRunningTaskReturnsFinishAndRejectActions(): void
    {
        $task = new Task(1);

        $expectedActions =
            [
                Task::ACTION_FINISH => 'Завершить задание',
                Task::ACTION_REJECT => 'Отказаться от задания',
            ];
        $status = Task::STATUS_RUNNING;

        $this->assertSame($expectedActions, $task->getActions($status));
    }

    public function testFinishedTaskReturnsEmptyActions(): void
    {
        $task = new Task(1);

        $status = Task::STATUS_FINISHED;

        $this->assertEmpty($task->getActions($status));
    }

    public function testCanceledTaskReturnsEmptyActions(): void
    {
        $task = new Task(1);

        $status = Task::STATUS_CANCELED;

        $this->assertEmpty($task->getActions($status));
    }

    public function testFailedTaskReturnsEmptyActions(): void
    {
        $task = new Task(1);

        $status = Task::STATUS_FAILED;

        $this->assertEmpty($task->getActions($status));
    }

    public function testInvalidStatusTaskReturnsFalse(): void
    {
        $task = new Task(1);

        $status = 'some_invalid_status';

        $this->assertFalse($task->getActions($status));
    }

    public function testAppliedInvalidActionReturnsFalse(): void
    {
        $task = new Task(1);

        $action = 'some_invalid_action';

        $this->assertFalse($task->applyAction($action));
    }

    public function testAppliedInvalidActionDoesNotChangeStatus(): void
    {
        $task = new Task(1);
        $currentStatus = $task->getStatus();

        $action = 'some_invalid_action';

        $task->applyAction($action);

        $this->assertSame($currentStatus, $task->getStatus());
    }

    public function testStartActionChangesTaskStatusToRunning(): void
    {
        $task = new Task(1);

        $action = Task::ACTION_START;
        $expectedStatus = Task::STATUS_RUNNING;

        $task->applyAction($action);

        $this->assertSame($expectedStatus, $task->getStatus());
    }

    public function testCancelActionChangesTaskStatusToCanceled(): void
    {
        $task = new Task(1);

        $action = Task::ACTION_CANCEL;
        $expectedStatus = Task::STATUS_CANCELED;

        $task->applyAction($action);

        $this->assertSame($expectedStatus, $task->getStatus());
    }

    public function testFinishActionChangesTaskStatusToFinished(): void
    {
        $task = new Task(1);

        $actions = [Task::ACTION_START, Task::ACTION_FINISH];
        $expectedStatus = Task::STATUS_FINISHED;

        foreach ($actions as $action) {
            $task->applyAction($action);
        }

        $this->assertSame($expectedStatus, $task->getStatus());
    }

    public function testRejectActionChangesTaskStatusToFailed(): void
    {
        $task = new Task(1);

        $actions = [Task::ACTION_START, Task::ACTION_REJECT];
        $expectedStatus = Task::STATUS_FAILED;

        foreach ($actions as $action) {
            $task->applyAction($action);
        }

        $this->assertSame($expectedStatus, $task->getStatus());
    }
}
