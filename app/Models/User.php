<?php

namespace App\Models;

use App\Core\Model;

class User extends Model
{
    protected string $table = 'users';

    public function findByLogin(string $login): ?array
    {
        return $this->queryOne(
            'SELECT * FROM users WHERE (username = ? OR email = ?) LIMIT 1',
            [$login, $login]
        );
    }

    public function findByUsername(string $username): ?array
    {
        return $this->where('username', $username);
    }

    public function findByEmail(string $email): ?array
    {
        return $this->where('email', $email);
    }

    public function listByRole(?string $role = null): array
    {
        if ($role) {
            return $this->query('SELECT * FROM users WHERE role = ? ORDER BY full_name ASC', [$role]);
        }
        return $this->query('SELECT id, username, email, full_name, role, status, created_at FROM users ORDER BY role, full_name');
    }

    public function updateLastLogin(int $id): void
    {
        $this->update($id, ['last_login_at' => now()]);
    }

    public function updatePassword(int $id, string $password): bool
    {
        return $this->update($id, ['password' => password_hash($password, PASSWORD_DEFAULT)]);
    }
}
