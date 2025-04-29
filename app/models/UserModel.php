<?php

namespace app\models;

/**
 * Модель для роботи з користувачами.
 * Наслідує базову функціональність з MainModel.
 */
class UserModel extends MainModel
{
    protected string $table = 'users';

    /**
     * Отримати користувача за ID.
     *
     * @param int $id Ідентифікатор користувача
     * @return array|null
     */
    public function getUserById(int $id): ?array
    {
        $results = $this->select($this->table, ['id' => $id]);
        return $results[0] ?? null;
    }

	/**
     * Отримати користувача за login.
     *
     * @param string $login Логін користувача
     * @return array|null
     */
    public function getUserByLogin(string $login): ?array
    {
        $results = $this->select($this->table, ['login' => $login]);
        return $results[0] ?? null;
    }

    /**
     * Отримати користувача за email.
     *
     * @param string $email Email користувача
     * @return array|null
     */
    public function getUserByEmail(string $email): ?array
    {
        $results = $this->select($this->table, ['email' => $email]);
        return $results[0] ?? null;
    }

    /**
     * Створити нового користувача.
     *
     * @param array $data Дані користувача (наприклад: name, email, password)
     * @return int ID нового користувача
     */
    public function createUser(array $data): int
    {
        return $this->insert($this->table, $data);
    }

    /**
     * Оновити дані користувача.
     *
     * @param int $id Ідентифікатор користувача
     * @param array $data Масив нових даних
     * @return bool
     */
    public function updateUser(int $id, array $data): bool
    {
        return $this->update($this->table, $data, ['id' => $id]);
    }

    /**
     * Видалити користувача за ID.
     *
     * @param int $id Ідентифікатор користувача
     * @return bool
     */
    public function deleteUser(int $id): bool
    {
        return $this->delete($this->table, ['id' => $id]);
    }
}
