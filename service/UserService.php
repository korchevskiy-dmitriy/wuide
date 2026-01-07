<?php

require_once __DIR__ . '/../dao/UserDao.php';
require_once __DIR__ . '/../entity/User.php';
require_once __DIR__ . '/../dto/UserRegisterDto.php';
require_once __DIR__ . '/../dto/UserLoginDto.php';

class UserService {
    private $userDao;

    public function __construct() {
        $this->userDao = new UserDao();
    }

    public function register(UserRegisterDto $dto) {
        if (empty($dto->email) || empty($dto->password) || empty($dto->name)) {
            throw new Exception("All fields are required", 400);
        }

        if ($this->userDao->findByEmail($dto->email)) {
            throw new Exception("User with this email already exists", 409);
        }

        $hashedPassword = password_hash($dto->password, PASSWORD_DEFAULT);

        $newId = $this->userDao->getNextId();
        
        $user = new User(
            $newId,
            $dto->name,
            $dto->surname,
            $dto->email,
            $hashedPassword,
            $dto->photoUrl,
            $dto->country
        );

        $this->userDao->save($user);
        
        return ["id" => $user->id, "message" => "Registration successful"];
    }

    public function login(UserLoginDto $dto) {
        $userArr = $this->userDao->findByEmail($dto->email);

        if (!$userArr || !password_verify($dto->password, $userArr['password'])) {
            throw new Exception("Invalid email or password", 401);
        }

        $token = bin2hex(random_bytes(32));

        $userEntity = new User(
            $userArr['id'],
            $userArr['name'],
            $userArr['surname'],
            $userArr['email'],
            $userArr['password'],
            $userArr['photo_url'] ?? null,
            $userArr['country'] ?? null,
            $userArr['review_id'] ?? null,
            $userArr['role'] ?? 'user',
            $userArr['creation_date'] ?? null,
            $token
        );

        $this->userDao->update($userEntity);
        
        return [
            "message" => "Login successful",
            "token" => $token,
            "userId" => $userEntity->id
        ];
    }
    
    public function authenticate($token) {
        if (!$token) {
            throw new Exception("Token not provided", 401);
        }
        $user = $this->userDao->findByToken($token);
        if (!$user) {
            throw new Exception("Invalid or expired token", 401);
        }
        return $user;
    }

    public function authorize($currentUser, $resourceOwnerId) {
        if ($currentUser['id'] != $resourceOwnerId) {
            throw new Exception("Access denied", 403);
        }
    }

    public function updateProfile(string $token, array $newData) {
        $userArr = $this->userDao->findByToken($token);
        if (!$userArr) {
            throw new Exception("Unauthorized", 401);
        }

        $newName = $newData['name'] ?? $userArr['name'];
        $newSurname = $newData['surname'] ?? $userArr['surname'];
        $newPhoto = $newData['photoUrl'] ?? ($userArr['photo_url'] ?? null);
        $newCountry = $newData['country'] ?? ($userArr['country'] ?? null);
        
        $newPassword = $userArr['password'];
        if (!empty($newData['password'])) {
            $newPassword = password_hash($newData['password'], PASSWORD_DEFAULT);
        }

        $updatedUser = new User(
            $userArr['id'],
            $newName,
            $newSurname,
            $userArr['email'],
            $newPassword,
            $newPhoto,
            $newCountry,
            $userArr['review_id'] ?? null,
            $userArr['role'] ?? 'user',
            $userArr['creation_date'] ?? null,
            $token
        );

        $this->userDao->update($updatedUser);

        $response = $updatedUser->toArray();
        unset($response['password']);
        unset($response['api_token']);
        
        return $response;
    }

    public function deleteProfile(string $token) {
        $userArr = $this->userDao->findByToken($token);
        
        if (!$userArr) {
            throw new Exception("Unauthorized", 401);
        }

        $this->userDao->delete($userArr['id']);

        return ["message" => "User profile deleted successfully"];
    }
}