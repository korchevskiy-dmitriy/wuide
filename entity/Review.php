<?php

class Review {
    public int $id;
    public int $userId;
    public string $userName;
    public ?string $userPhoto;
    public int $countryId;
    public string $countryName;
    public string $text;
    public string $date;
    public string $status;

    public function __construct(
        int $id, 
        int $userId, 
        string $userName, 
        ?string $userPhoto, 
        int $countryId, 
        string $countryName,
        string $text, 
        string $date,
        string $status = 'pending'
    ) {
        $this->id = $id;
        $this->userId = $userId;
        $this->userName = $userName;
        $this->userPhoto = $userPhoto;
        $this->countryId = $countryId;
        $this->countryName = $countryName;
        $this->text = $text;
        $this->date = $date;
        $this->status = $status;
    }

    public static function fromArray(array $data): self {
        return new self(
            $data['id'],
            $data['user_id'],
            $data['user_name'],
            $data['user_photo'] ?? null,
            $data['country_id'],
            $data['country_name'],
            $data['text'],
            $data['date'],
            $data['status'] ?? 'pending'
        );
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'user_id' => $this->userId,
            'user_name' => $this->userName,
            'user_photo' => $this->userPhoto,
            'country_id' => $this->countryId,
            'country_name' => $this->countryName,
            'text' => $this->text,
            'date' => $this->date,
            'status' => $this->status
        ];
    }
}