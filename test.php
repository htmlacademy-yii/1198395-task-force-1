<?php

require_once __DIR__ . '/src/Task.php';

$task1 = new Task(1);

assert($task1->getActions('status_new', 1) === [Task::ACTION_CANCEL => 'Отменить задачу'],
    'Действия заказчика на новой задаче');

assert($task1->getActions('status_new', 2) === [Task::ACTION_START => 'Начать задачу'],
    'Действия исполнителя новой задачи');

assert($task1->updateStatus('action_start', 2) === true, 'Обновление статуса задачи');


assert($task1->getActions('status_running', 1) === [Task::ACTION_FINISH => 'Завершить задачу'],
    'Действия заказчика активной задачи');

assert($task1->getActions('status_running', 2) === [Task::ACTION_REJECT => 'Отказаться от задачи'],
    'Действия исполнителя активной задачи');
