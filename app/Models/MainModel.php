<?php

namespace app\Models;

use core\Db;

/**
 * Основна модель для взаємодії з базою даних.
 * Цей клас призначено як батьківський для інших моделей.
 */
class MainModel
{
    /**
     * @var Db
     */
    protected Db $db;

	/**
     * @var string $table.
     */
    protected string $table;

    /**
     * Ініціалізація з'єднання з БД.
     */
    public function __construct()
    {
        $this->db = Db::getInstance();
    }

    /**
     * Отримати записи з бази даних.
     *
     * @param string $table Назва таблиці
     * @param array $conditions Масив умов (опційно)
     * @return array
     */
    public function select(string $table, array $conditions = []): array
    {
		$this->table = $table;
        $sql = "SELECT * FROM `$this->table`";
		$params = [];

        if (!empty($conditions)) {
            $where = [];
            foreach ($conditions as $key => $value) {
                $where[] = "`$key` = :$key";
                $params[$key] = $value;
            }
            $sql .= " WHERE " . implode(" AND ", $where);
        }
        return $this->db->query($sql, $params)->fetchAll();
    }

    /**
     * Додати новий запис до таблиці.
     *
     * @param string $table Назва таблиці
     * @param array $data Масив значень (ключ => значення)
     * @return int ID вставленого запису
     */
    public function insert(string $table, array $data): int
    {
        $columns = implode(", ", array_map(fn($col) => "`$col`", array_keys($data)));
        $placeholders = implode(", ", array_map(fn($col) => ":$col", array_keys($data)));
        $sql = "INSERT INTO `$table` ($columns) VALUES ($placeholders)";

        $this->db->query($sql, $data);
        return $this->db->lastInsertId();
    }

    /**
     * Оновити наявний запис у таблиці.
     *
     * @param string $table Назва таблиці
     * @param array $data Нові значення
     * @param array $conditions Умови для вибору запису
     * @return bool
     */
    public function update(string $table, array $data, array $conditions): bool
    {
        $set = [];
        $params = [];

        foreach ($data as $key => $value) {
            $set[] = "`$key` = :set_$key";
            $params["set_$key"] = $value;
        }

        $where = [];
        foreach ($conditions as $key => $value) {
            $where[] = "`$key` = :where_$key";
            $params["where_$key"] = $value;
        }

        $sql = "UPDATE `$table` SET " . implode(", ", $set) . " WHERE " . implode(" AND ", $where);
        return $this->db->query($sql, $params)->rowCount() > 0;
    }

    /**
     * Видалити запис із таблиці.
     *
     * @param string $table Назва таблиці
     * @param array $conditions Умови для видалення
     * @return bool
     */
    public function delete(string $table, array $conditions): bool
    {
        $where = [];
        $params = [];

        foreach ($conditions as $key => $value) {
            $where[] = "`$key` = :$key";
            $params[$key] = $value;
        }

        $sql = "DELETE FROM `$table` WHERE " . implode(" AND ", $where);
        return $this->db->query($sql, $params)->rowCount() > 0;
    }
}
