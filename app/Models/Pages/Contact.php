<?php

declare(strict_types=1);

namespace App\Models\Pages;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    const ID = 'id';
    const ADDRESS = 'address';
    const EMAIL = 'email';
    const PHONE = 'phone';
    const WORKING_HOURS = 'working_hours';
    const MAP_EMBED_CODE = 'map_embed_code';
    const ADDITIONAL_INFO = 'additional_info';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        self::ADDRESS,
        self::EMAIL,
        self::PHONE,
        self::WORKING_HOURS,
        self::MAP_EMBED_CODE,
        self::ADDITIONAL_INFO
    ];

    public function getId(): int
    {
        return $this->getAttribute(self::ID);
    }

    public function getAddress(): string
    {
        return $this->getAttribute(self::ADDRESS);
    }

    public function getEmail(): string
    {
        return $this->getAttribute(self::EMAIL);
    }

    public function getPhone(): string
    {
        return $this->getAttribute(self::PHONE);
    }

    public function getWorkingHours(): ?string
    {
        return $this->getAttribute(self::WORKING_HOURS);
    }

    public function getMapEmbedCode(): ?string
    {
        return $this->getAttribute(self::MAP_EMBED_CODE);
    }

    public function getAdditionalInfo(): ?string
    {
        return $this->getAttribute(self::ADDITIONAL_INFO);
    }

    public function getCreatedAt(): Carbon
    {
        return $this->getAttribute(self::CREATED_AT);
    }

    public function getUpdatedAt(): Carbon
    {
        return $this->getAttribute(self::UPDATED_AT);
    }
}
