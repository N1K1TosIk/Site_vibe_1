<?php include __DIR__ . '/layout/header.php'; ?>
<h2>Категория <?= $n ?>. <?= htmlspecialchars(CategoryController::$categories[$n]) ?></h2>
<?php if (isset($feedback)): ?>
    <div class="alert <?= str_contains($feedback, 'Верно') ? 'alert-success' : 'alert-danger' ?>"><?= $feedback ?></div>
<?php endif; ?>
<?php if (!$nextTask): ?>
    <div class="alert alert-info">Все задачи этой категории решены в текущей сессии. Поздравляем!</div>
    <a href="<?= BASE_URL ?>/public/?action=tasks" class="btn btn-secondary">К списку задач</a>
<?php else: ?>
    <div class="mb-3">
        <?= $nextTask['text'] ?>
        <?php if ($nextTask['image_url']): ?>
            <img src="<?= $nextTask['image_url'] ?>" class="img-fluid mt-2"/>
        <?php endif; ?>
    </div>

    <form method="post">
        <input type="hidden" name="task_id" value="<?= $nextTask['id'] ?>">
        <label class="form-label">Ваш ответ:</label>
        <input type="text" name="answer" class="form-control" required autofocus>
        <button type="submit" class="btn btn-primary mt-2">Ответить</button>
    </form>
<?php endif; ?>
<?php include __DIR__ . '/layout/footer.php'; ?>
