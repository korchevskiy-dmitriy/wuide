<?php

class Place {
    public string $name;
    public string $description;
    public string $photoUrl;

    public function __construct(string $name, string $description, string $photoUrl) {
        $this->name = $name;
        $this->description = $description;
        $this->photoUrl = $photoUrl;
    }

    public static function fromArray(array $data): self {
        return new self(
            $data['name'] ?? '',
            $data['description'] ?? '',
            $data['photo_url'] ?? ''
        );
    }

    public function toArray(): array {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'photo_url' => $this->photoUrl,
        ];
    }
}