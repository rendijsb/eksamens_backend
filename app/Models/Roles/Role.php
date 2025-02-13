<?php

declare(strict_types=1);

namespace App\Models\Roles;

use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    const ID = 'id';
    const NAME = 'name';

    protected $fillable = [
        self::NAME,
    ];

    public function relatedUsers(): hasMany
    {
        return $this->hasMany(User::class);
    }

    public function getName(): string
    {
        return $this->getAttribute(self::NAME);
    }

    public function getId(): int
    {
        return $this->getAttribute(self::ID);
    }
}
