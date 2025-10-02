<?php include __DIR__ . '/layout/header.php'; ?>
<h2>Список задач</h2>

<form class="row g-3 mb-4" method="get" action="">
    <input type="hidden" name="action" value="tasks">
    <div class="col-md-3">
        <label class="form-label">Категория</label>
        <select name="category" class="form-select">
            <option value="">Все</option>
            <?php foreach ($options['categories'] as $cat): ?>
                <option value="<?= htmlspecialchars($cat) ?>" <?= $filters['category']===$cat ? 'selected' : '' ?>><?= htmlspecialchars($cat) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-2">
        <label class="form-label">Год</label>
        <select name="year" class="form-select">
            <option value="">Все</option>
            <?php foreach ($options['years'] as $y): ?>
                <option value="<?= $y ?>" <?= $filters['year']==$y ? 'selected' : '' ?>><?= $y ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">Сложность</label>
        <select name="difficulty" class="form-select">
            <option value="">Все</option>
            <?php foreach ($options['difficulties'] as $diff): ?>
                <option value="<?= htmlspecialchars($diff) ?>" <?= $filters['difficulty']===$diff ? 'selected' : '' ?>><?= htmlspecialchars($diff) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-2 align-self-end">
        <button type="submit" class="btn btn-primary">Применить</button>
    </div>
</form>

<table class="table table-striped">
<thead><tr>
<th>#</th><th>Категория</th><th>Тема</th><th>Год</th><th>Сложность</th><th>Статус</th>
</tr></thead>
<tbody>
<?php foreach ($tasks as $row): ?>
<tr>
    <td><a href="<?= BASE_URL ?>/public/?action=task&id=<?= $row['id'] ?>"><?= htmlspecialchars($row['task_number']) ?></a></td>
    <td><?= htmlspecialchars($row['category']) ?></td>
    <td><?= htmlspecialchars($row['theme']) ?></td>
    <td><?= htmlspecialchars($row['year']) ?></td>
    <td><?= htmlspecialchars($row['difficulty']) ?></td>
    <td>
        <?php
        $stat = $_SESSION['progress'][$row['id']] ?? null;
        if ($stat === 'correct') {
            echo '<span class="badge bg-success">✔</span>';
        } elseif ($stat === 'wrong') {
            echo '<span class="badge bg-danger">✘</span>';
        }
        ?>
    </td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<?php include __DIR__ . '/layout/footer.php'; ?>
