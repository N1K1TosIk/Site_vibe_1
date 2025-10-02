<?php
require_once __DIR__ . '/../Models/Task.php';

class ProgressController
{
    public function index(): void
    {
        $progress = $_SESSION['progress'] ?? [];
        $taskIds  = array_keys($progress);
        $tasks    = [];
        if ($taskIds) {
            $in  = implode(',', array_fill(0, count($taskIds), '?'));
            $sql  = "SELECT id, task_number, category, year FROM tasks WHERE id IN ($in)";
            $stmt = Database::getInstance()->prepare($sql);
            $stmt->execute($taskIds);
            $tasksData = $stmt->fetchAll();
            foreach ($tasksData as $t) {
                $tasks[$t['id']] = $t;
            }
        }

        include __DIR__ . '/../Views/progress.php';
    }
}
