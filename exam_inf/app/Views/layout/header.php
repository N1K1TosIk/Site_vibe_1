<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>ЕГЭ Информатика тренажёр</title>
    <!-- Bootswatch Cosmo theme for modern look -->
    <link href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.2/dist/cosmo/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding-bottom: 60px; }
        footer { background:#f8f9fa; border-top:1px solid #e9ecef; position:fixed; bottom:0; left:0; right:0; height:60px; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="<?= BASE_URL ?>/public/">ЕГЭ Информатика</a>
        <a class="nav-link text-white" href="<?= BASE_URL ?>/public/?action=tasks">Все задачи</a>
        <a class="nav-link text-white" href="<?= BASE_URL ?>/public/?action=progress">Прогресс</a>
    </div>
</nav>
<div class="container">
