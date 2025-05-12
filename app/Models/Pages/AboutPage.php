<?php

declare(strict_types=1);

namespace App\Models\Pages;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class AboutPage extends Model
{
    const ID = 'id';
    const TITLE = 'title';
    const CONTENT = 'content';
    const IS_ACTIVE = 'is_active';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        self::TITLE,
        self::CONTENT,
        self::IS_ACTIVE
    ];

    protected $casts = [
        self::IS_ACTIVE => 'boolean',
    ];

    public function getId(): int
    {
        return $this->getAttribute(self::ID);
    }

    public function getTitle(): string
    {
        return $this->getAttribute(self::TITLE);
    }

    public function getContent(): string
    {
        return $this->getAttribute(self::CONTENT);
    }

    public function getIsActive(): bool
    {
        return $this->getAttribute(self::IS_ACTIVE);
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
