<?php

class User {
    public int $id;
    public string $name;
    public string $surname;
    public string $email;
    public string $password;
    public ?string $photoUrl;
    public ?string $country;
    public ?int $reviewId;
    public string $role;
    public string $creationDate;
    public $api_token;

    public function __construct(
        int $id,
        string $name,
        string $surname,
        string $email,
        string $password,
        ?string $photoUrl = null,
        ?string $country = null,
        ?int $reviewId = null,
        string $role = 'user',
        string $creationDate = null,
        string $api_token = null
        
    ) {
        $this->id           = $id;
        $this->name         = $name;
        $this->surname      = $surname;
        $this->email        = $email;
        $this->password     = $password;
        $this->photoUrl     = $photoUrl;
        $this->country      = $country;
        $this->reviewId     = null;
        $this->role         = $role;
        $this->api_token = $api_token;
        $this->creationDate = $creationDate ?? date('Y-m-d H:i:s');
        
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'],
            $data['name'],
            $data['surname'],
            $data['email'],
            $data['password'],
            $data['photo_url'] ?? null,
            $data['country'] ?? null,
            $data['review_id'] ?? null,
            $data['role'],
            $data['creation_date']
        );
    }

    public function toArray(): array
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'surname'       => $this->surname,
            'email'         => $this->email,
            'password'      => $this->password,
            'photo_url'     => $this->photoUrl,
            'country'       => $this->country,
            'review_id'     => $this->reviewId,
            'role'          => $this->role,
            'creation_date' => $this->creationDate,
        ];
    }
}
