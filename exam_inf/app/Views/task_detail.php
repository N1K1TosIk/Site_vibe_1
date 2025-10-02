<?php include __DIR__ . '/layout/header.php'; ?>
<h2>Задание <?= htmlspecialchars($task['task_number']) ?></h2>
<div class="mb-3">
    <?= $task['text'] ?>
    <?php if ($task['image_url']): ?>
        <img src="<?= $task['image_url'] ?>" class="img-fluid mt-2"/>
    <?php endif; ?>
</div>

<form method="post" class="mb-3">
    <label for="answer" class="form-label">Ваш ответ:</label>
    <input type="text" id="answer" name="answer" class="form-control" required>
    <button type="submit" class="btn btn-primary mt-2">Проверить</button>
</form>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userAnswer = trim($_POST['answer']);
    if (strcasecmp($userAnswer, $task['answer']) === 0) {
        echo '<div class="alert alert-success">Верно!</div>';
    } else {
        echo '<div class="alert alert-danger">Неверно. Правильный ответ: ' . htmlspecialchars($task['answer']) . '</div>';
    }
    echo '<div class="card card-body mt-3"><h5>Решение:</h5>' . $task['solution_html'] . '</div>';
}
?>

<?php include __DIR__ . '/layout/footer.php'; ?>
