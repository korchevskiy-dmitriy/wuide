<?php

require_once __DIR__ . '/../entity/User.php';

class UserDao {
    private $filePath;

    public function __construct() {
        $this->filePath = __DIR__ . '/../user.json';
    }

    public function getAll() {
        if (!file_exists($this->filePath)) {
            return [];
        }
        $json = file_get_contents($this->filePath);
        return json_decode($json, true) ?? [];
    }

    public function findByEmail($email) {
        $users = $this->getAll();
        foreach ($users as $user) {
            if ($user['email'] === $email) {
                return $user;
            }
        }
        return null;
    }

    public function save(User $user) {
        $users = $this->getAll();
        
        // ВАЖНО: Используем toArray(), чтобы ключи стали photo_url, api_token и т.д.
        $userData = $user->toArray();
        
        $users[] = $userData;
        
        return file_put_contents(
            $this->filePath, 
            json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
            LOCK_EX
        );
    }

    public function getNextId() {
        $users = $this->getAll();
        if (empty($users)) return 1;
        
        $maxId = 0;
        foreach ($users as $user) {
            if (isset($user['id']) && $user['id'] > $maxId) {
                $maxId = $user['id'];
            }
        }
        return $maxId + 1;
    }

    public function update(User $updatedUser) {
        $users = $this->getAll();
        $found = false;

        foreach ($users as &$userArr) {
            if ($userArr['id'] == $updatedUser->id) {
                // ВАЖНО: Здесь тоже используем toArray()
                $userArr = $updatedUser->toArray();
                $found = true;
                break;
            }
        }

        if ($found) {
            $this->saveAll($users);
            return true;
        }
        return false;
    }

    public function findByToken($token) {
        $users = $this->getAll();
        foreach ($users as $user) {
            // Теперь ключ точно будет 'api_token'
            if (isset($user['api_token']) && $user['api_token'] === $token) {
                return $user;
            }
        }
        return null;
    }

    private function saveAll($users) {
        file_put_contents(
            $this->filePath, 
            json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
            LOCK_EX
        );
    }
}