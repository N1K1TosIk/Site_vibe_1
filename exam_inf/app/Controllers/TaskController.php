<?php
require_once __DIR__ . '/../Models/Task.php';

class TaskController
{
    public function index(): void
    {
        $filters = [
            'category'   => $_GET['category']   ?? '',
            'year'       => $_GET['year']       ?? '',
            'difficulty' => $_GET['difficulty'] ?? '',
        ];

        $tasks    = Task::filter($filters);
        $options  = Task::filterOptions();

        include __DIR__ . '/../Views/tasks.php';
    }

    public function show(int $id): void
    {
        $task = Task::find($id);
        if (!$task) {
            http_response_code(404);
            echo 'Task not found';
            return;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userAnswer = trim($_POST['answer']);
            $isCorrect  = strcasecmp($userAnswer, $task['answer']) === 0;

            // Сохраняем прогресс в сессии
            $_SESSION['progress'][$task['id']] = $isCorrect ? 'correct' : 'wrong';

            if ($isCorrect) {
                echo '<div class="alert alert-success">Верно!</div>';
            } else {
                echo '<div class="alert alert-danger">Неверно. Правильный ответ: ' . htmlspecialchars($task['answer']) . '</div>';
            }
            echo '<div class="card card-body mt-3"><h5>Решение:</h5>' . $task['solution_html'] . '</div>';
        }
        include __DIR__ . '/../Views/task_detail.php';
    }
}
