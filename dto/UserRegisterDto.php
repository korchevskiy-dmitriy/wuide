<?php

class UserRegisterDto {
    
    public string $name;
    public string $surname;
    public string $email;
    public string $password;
    public ?string $photoUrl;
    public ?string $country;

    public function __construct(array $data)
    {
        $this->name     = trim($data['name'] ?? '');
        $this->surname  = trim($data['surname'] ?? '');
        $this->email    = trim($data['email'] ?? '');
        $this->password = $data['password'] ?? '';
        $this->photoUrl = $data['photo_url'] ?? null;
        $this->country  = $data['country'] ?? null;
    }

}