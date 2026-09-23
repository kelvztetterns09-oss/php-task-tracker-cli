# PHP Task Tracker CLI

A command-line Task Tracker built in pure PHP with no external dependencies. Tasks are stored locally in a tasks.json file.

This project was built as part of the https://roadmap.sh/projects/task-tracker

## Requirements

- PHP 8.0 or higher

## Installation

git clone https://github.com/kelvztetterns09-oss/php-task-tracker-cli.git
cd php-task-tracker-cli

## Usage

Run the script from your terminal using PHP.

### Commands

| Command | Description |
|---|---|
| add "description" | Add a new task |
| update <id> "description" | Update a task's description |
| delete <id> | Delete a task |
| mark-in-progress <id> | Mark a task as in-progress |
| mark-done <id> | Mark a task as done |
| list | List all tasks |
| list todo | List tasks with todo status |
| list in-progress | List tasks with in-progress status |
| list done | List tasks with done status |
| help | Show the help menu |

### Examples

Add a task:
php task-cli.php add "Buy groceries"

List all tasks:
php task-cli.php list

Mark task 1 as done:
php task-cli.php mark-done 1

List only completed tasks:
php task-cli.php list done

Delete task 2:
php task-cli.php delete 2

## Storage

Tasks are saved in tasks.json in the project root directory. The file is created automatically the first time you add a task.

Each task contains the following fields:

id
description
status
createdAt
updatedAt

## License

MIT