<?php
require_once __DIR__ . '/Database.php';

class Task
{
    /** Возвращает все задачи для списка */
    public static function all(): array
    {
        return self::filter([]);
    }

    /**
     * Возвращает задачи по фильтрам (значения могут быть null)
     */
    public static function filter(array $filters): array
    {
        $where = [];
        $params = [];
        if (!empty($filters['category'])) {
            $where[]  = 'category = ?';
            $params[] = $filters['category'];
        }
        if (!empty($filters['year'])) {
            $where[]  = 'year = ?';
            $params[] = (int)$filters['year'];
        }
        if (!empty($filters['difficulty'])) {
            $where[]  = 'difficulty = ?';
            $params[] = $filters['difficulty'];
        }

        $sql = 'SELECT id, task_number, category, theme, year, difficulty FROM tasks';
        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= ' ORDER BY year DESC, id DESC';

        $stmt = Database::getInstance()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Выборки для фильтров (distinct values)
     */
    public static function filterOptions(): array
    {
        $db = Database::getInstance();
        $categories  = $db->query('SELECT DISTINCT category FROM tasks WHERE category <> "" ORDER BY category')->fetchAll(PDO::FETCH_COLUMN);
        $years       = $db->query('SELECT DISTINCT year FROM tasks WHERE year IS NOT NULL ORDER BY year DESC')->fetchAll(PDO::FETCH_COLUMN);
        $difficulties = $db->query('SELECT DISTINCT difficulty FROM tasks WHERE difficulty <> "" ORDER BY difficulty')->fetchAll(PDO::FETCH_COLUMN);
        return compact('categories', 'years', 'difficulties');
    }

    /**
     * Возвращает следующую не решённую задачу в категории.
     * @param string $category
     * @param array  $excludeIds
     * @return array|null
     */
    public static function nextUnsolvedByCategory(string $category, array $excludeIds): ?array
    {
        $db  = Database::getInstance();
        $sql = 'SELECT * FROM tasks WHERE category = ?';
        $params = [$category];
        if ($excludeIds) {
            $placeholders = implode(',', array_fill(0, count($excludeIds), '?'));
            $sql .= " AND id NOT IN ($placeholders)";
            $params = array_merge($params, $excludeIds);
        }
        $sql .= ' ORDER BY RAND() LIMIT 1';

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch() ?: null;
    }

    /** Находит задачу по её ID */
    public static function find(int $id): ?array
    {
        $stmt = Database::getInstance()->prepare('SELECT * FROM tasks WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    /** Создаёт запись в базе (используется скраппером) */
    public static function create(array $data): void
    {
        // Подготовленный запрос, все ключи в массиве $data обязаны присутствовать
        $sql = 'INSERT INTO tasks (source_id, task_number, category, theme, text, image_url, answer, solution_html, difficulty, year)
                VALUES (:source_id, :task_number, :category, :theme, :text, :image_url, :answer, :solution_html, :difficulty, :year)';
        $stmt = Database::getInstance()->prepare($sql);
        $stmt->execute($data);
    }
}
