<?php
require_once __DIR__ . '/../Controllers/CategoryController.php';
include __DIR__ . '/layout/header.php';
?>
<h1 class="text-center mb-4">Выберите номер задания</h1>
<div class="list-group">
    <?php foreach (CategoryController::$categories as $num => $title): ?>
        <a class="list-group-item list-group-item-action" href="<?= BASE_URL ?>/public/?action=category&n=<?= $num ?>">
            <strong><?= $num ?>.</strong> <?= htmlspecialchars($title) ?>
        </a>
    <?php endforeach; ?>
</div>
<?php include __DIR__ . '/layout/footer.php'; ?>
