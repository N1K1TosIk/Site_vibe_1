<?php include __DIR__ . '/layout/header.php'; ?>
<h2>Ваш прогресс</h2>
<?php if (!$progress): ?>
    <p>Вы ещё не решали задачи.</p>
<?php else: ?>
    <table class="table table-bordered">
        <thead><tr><th>Задача</th><th>Категория</th><th>Год</th><th>Статус</th></tr></thead>
        <tbody>
        <?php foreach ($progress as $taskId => $status):
            $info = $tasks[$taskId] ?? null;
            if (!$info) continue; ?>
            <tr>
                <td><a href="<?= BASE_URL ?>/public/?action=task&id=<?= $taskId ?>"><?= htmlspecialchars($info['task_number']) ?></a></td>
                <td><?= htmlspecialchars($info['category']) ?></td>
                <td><?= htmlspecialchars($info['year']) ?></td>
                <td><?= $status === 'correct' ? '<span class="badge bg-success">Верно</span>' : '<span class="badge bg-danger">Неверно</span>' ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
<?php include __DIR__ . '/layout/footer.php'; ?>
