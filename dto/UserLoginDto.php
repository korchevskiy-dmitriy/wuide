<?php

class UserLoginDto {

    public string $email;
    public string $password;

    public function __construct(array $data)
    {
        $this->email    = trim($data['email'] ?? '');
        $this->password = $data['password'] ?? '';
    }

}