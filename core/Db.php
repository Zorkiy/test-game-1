<?php

namespace core;

use \PDO;
use \PDOException;
use \PDOStatement;

/**
 * Клас Db для підключення та взаємодії з базою даних MySQL.
 */
class Db
{
    private static ?Db $instance = null;
    private PDO $pdo;
    private array $config;

    /**
     * Приватний конструктор для запобігання створенню екземплярів класу ззовні.
     *
     * @param array $config Масив з параметрами підключення до бази даних.
     */
    private function __construct(array $config)
    {
        $this->config = $config;

        try {
            $this->pdo = new PDO(
                "mysql:host={$this->config['host']};dbname={$this->config['dbname']};charset={$this->config['charset']}",
                $this->config['username'],
                $this->config['password'],
                $this->config['options'] ?? []
            );
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Обробка помилки підключення до бази даних.
            die("Database connection error: " . $e->getMessage());
        }
    }

    /**
     * Забороняє клонування об'єкта.
     */
    private function __clone()
    {
    }

    /**
     * Повертає єдиний екземпляр об'єкта Db (Singleton).
     *
     * @param array $config Масив з параметрами підключення до бази даних.
     * @return Db Єдиний екземпляр класу Db.
     */
    public static function getInstance(array $config = [
		'host' => 'localhost',
		'dbname' => 'game',
		'username' => 'admin',
		'password' => '121010',
		'charset' => 'utf8mb4',
		'options' => [],
	]): Db
    {
        if (self::$instance === null) {
            self::$instance = new self($config);
        } elseif (!empty($config) && self::$instance->config !== $config) {
            throw new \InvalidArgumentException("Database configuration cannot be changed after the instance is created.");
        }
        return self::$instance;
    }

    /**
     * Повертає об'єкт PDO для виконання запитів.
     *
     * @return PDO Об'єкт PDO.
     */
    public function getConnection(): PDO
    {
        return $this->pdo;
    }

    /**
     * Виконує SQL-запит.
     *
     * @param string $sql SQL-запит.
     * @param array $params Параметри для підстановки в запит (необов'язково).
     * @return PDOStatement|false Об'єкт PDOStatement у разі успіху або false у разі помилки.
     */
    public function query(string $sql, array $params = []): PDOStatement|false
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Повертає одну колонку з першого рядка результату запиту.
     *
     * @param string $sql SQL-запит.
     * @param array $params Параметри для підстановки в запит (необов'язково).
     * @return string|false Значення колонки або false у разі помилки.
     */
    public function fetchColumn(string $sql, array $params = []): string|false
    {
        $stmt = $this->query($sql, $params);
        return $stmt ? $stmt->fetchColumn() : false;
    }

    /**
     * Повертає один рядок результату запиту у вигляді асоціативного масиву.
     *
     * @param string $sql SQL-запит.
     * @param array $params Параметри для підстановки в запит (необов'язково).
     * @return array|false Асоціативний масив з даними або false у разі помилки.
     */
    public function fetchOne(string $sql, array $params = []): array|false
    {
        $stmt = $this->query($sql, $params);
        return $stmt ? $stmt->fetch() : false;
    }

    /**
     * Повертає всі рядки результату запиту у вигляді масиву асоціативних масивів.
     *
     * @param string $sql SQL-запит.
     * @param array $params Параметри для підстановки в запит (необов'язково).
     * @return array Масив асоціативних масивів з даними.
     */
    public function fetchAll(string $sql, array $params = []): array
    {
        $stmt = $this->query($sql, $params);
        return $stmt ? $stmt->fetchAll() : [];
    }

    /**
     * Повертає останній вставлений ID.
     *
     * @return string|false Останній вставлений ID або false у разі помилки.
     */
    public function lastInsertId(): string|false
    {
        return $this->pdo->lastInsertId();
    }
}
