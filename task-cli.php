<?php

declare(strict_types=1); // Best practice for modern PHP

// Define the file path
$filePath = __DIR__ . '/tasks.json';

/**
 * Read the JSON file and return the tasks as a PHP array.
 */
function loadTasks(string $filePath): array
{
    if (!file_exists($filePath)) {
        return [];
    }

    $jsonData = file_get_contents($filePath);
    $tasks = json_decode($jsonData, true);

    return is_array($tasks) ? $tasks : [];
}

/**
 * Save the tasks array back into the JSON file.
 */
function saveTasks(string $filePath, array $tasks): void
{
    $jsonData = json_encode($tasks, JSON_PRETTY_PRINT);
    file_put_contents($filePath, $jsonData);
}

/**
 * Add a new task to the list.
 */
function addTask(string $filePath, string $description): void
{
    $tasks = loadTasks($filePath);

    // Generate a new ID
    $newId = 1;
    if (!empty($tasks)) {
        $ids = array_column($tasks, 'id');
        $newId = max($ids) + 1;
    }

    $now = date('c');
    $newTask = [
        'id' => $newId,
        'description' => $description,
        'status' => 'todo',
        'createdAt' => $now,
        'updatedAt' => $now
    ];

    $tasks[] = $newTask;
    saveTasks($filePath, $tasks);
}

/**
 * Display tasks in the terminal. Optionally filter by status.
 */
function listTasks(string $filePath, ?string $filter = null): void
{
    $tasks = loadTasks($filePath);

    if (empty($tasks)) {
        echo "No tasks found. Add one with: php task-cli.php add \"Your task\"\n";
        return;
    }

    $tasksToDisplay = [];

    if ($filter) {
        foreach ($tasks as $task) {
            if ($task['status'] === $filter) {
                $tasksToDisplay[] = $task;
            }
        }
        if (empty($tasksToDisplay)) {
            echo "No tasks found with status: $filter\n";
            return;
        }
    } else {
        $tasksToDisplay = $tasks;
    }

    echo "ID | Status      | Description\n";
    echo "----------------------------------------\n";

    foreach ($tasksToDisplay as $task) {
        $id = str_pad((string)$task['id'], 2, ' ', STR_PAD_RIGHT);
        $status = str_pad($task['status'], 11, ' ', STR_PAD_RIGHT);
        $description = $task['description'];

        echo "$id | $status | $description\n";
    }
}

/**
 * Update the status of a specific task.
 */
function updateTaskStatus(string $filePath, int $id, string $newStatus): void
{
    $tasks = loadTasks($filePath);
    $taskFound = false;

    foreach ($tasks as &$task) {
        if ($task['id'] == $id) {
            $task['status'] = $newStatus;
            $task['updatedAt'] = date('c');
            $taskFound = true;
            break;
        }
    }

    if ($taskFound) {
        saveTasks($filePath, $tasks);
        echo "Task $id marked as $newStatus.\n";
    } else {
        echo "Error: Task with ID $id not found.\n";
    }
}

/**
 * Update the description of a specific task.
 */
function updateTaskDescription(string $filePath, int $id, string $newDescription): void
{
    $tasks = loadTasks($filePath);
    $taskFound = false;

    foreach ($tasks as &$task) {
        if ($task['id'] == $id) {
            $task['description'] = $newDescription;
            $task['updatedAt'] = date('c');
            $taskFound = true;
            break;
        }
    }

    if ($taskFound) {
        saveTasks($filePath, $tasks);
        echo "Task $id updated successfully.\n";
    } else {
        echo "Error: Task with ID $id not found.\n";
    }
}

/**
 * Delete a task from the list.
 */
function deleteTask(string $filePath, int $id): void
{
    $tasks = loadTasks($filePath);
    $taskFound = false;

    foreach ($tasks as $index => $task) {
        if ($task['id'] == $id) {
            unset($tasks[$index]);
            $taskFound = true;
            break;
        }
    }

    if ($taskFound) {
        $tasks = array_values($tasks);
        saveTasks($filePath, $tasks);
        echo "Task $id deleted successfully.\n";
    } else {
        echo "Error: Task with ID $id not found.\n";
    }
}

/**
 * Display the help menu with all available commands.
 */
function showHelp(): void
{
    echo "Task Tracker CLI - Available Commands:\n";
    echo "----------------------------------------\n";
    echo "  add \"description\"          Add a new task\n";
    echo "  update <id> \"description\"  Update a task's description\n";
    echo "  delete <id>                Delete a task\n";
    echo "  mark-in-progress <id>      Mark a task as in-progress\n";
    echo "  mark-done <id>             Mark a task as done\n";
    echo "  list                       List all tasks\n";
    echo "  list todo                  List tasks with 'todo' status\n";
    echo "  list in-progress           List tasks with 'in-progress' status\n";
    echo "  list done                  List tasks with 'done' status\n";
    echo "  help                       Show this help menu\n";
    echo "----------------------------------------\n";
}

// ==========================================
// MAIN LOGIC
// ==========================================

if (isset($argv[1])) {
    $command = $argv[1];

    if ($command === 'add') {
        if (!isset($argv[2])) {
            echo "Error: You must provide a description.\n";
            exit;
        }
        addTask($filePath, $argv[2]);
        echo "Task added successfully!\n";

    } elseif ($command === 'list') {
        $filter = isset($argv[2]) ? $argv[2] : null;
        listTasks($filePath, $filter);

    } elseif ($command === 'mark-in-progress') {
        if (!isset($argv[2])) {
            echo "Error: You must provide a task ID.\n";
            exit;
        }
        updateTaskStatus($filePath, (int)$argv[2], 'in-progress');

    } elseif ($command === 'mark-done') {
        if (!isset($argv[2])) {
            echo "Error: You must provide a task ID.\n";
            exit;
        }
        updateTaskStatus($filePath, (int)$argv[2], 'done');

    } elseif ($command === 'update') {
        if (!isset($argv[2]) || !isset($argv[3])) {
            echo "Error: You must provide a task ID and a new description.\n";
            echo "Usage: php task-cli.php update 1 \"New description\"\n";
            exit;
        }
        updateTaskDescription($filePath, (int)$argv[2], $argv[3]);

    } elseif ($command === 'delete') {
        if (!isset($argv[2])) {
            echo "Error: You must provide a task ID.\n";
            exit;
        }
        deleteTask($filePath, (int)$argv[2]);

    } elseif ($command === 'help') {
        showHelp();

    } else {
        echo "Unknown command: $command\n";
    }
} else {
    showHelp();
}
