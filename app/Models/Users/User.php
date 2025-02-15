<?php

declare(strict_types=1);

namespace App\Models\Users;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Roles\Role;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    const NAME = 'name';
    const EMAIL = 'email';
    const PASSWORD = 'password';
    const PHONE = 'phone';
    const ROLE_ID = 'role_id';
    const REMEMBER_TOKEN = 'remember_token';
    const EMAIL_VERIFIED_AT = 'email_verified_at';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        self::NAME,
        self::EMAIL,
        self::PASSWORD,
        self::ROLE_ID,
        self::PHONE,
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        self::PASSWORD,
        self::REMEMBER_TOKEN,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            self::EMAIL_VERIFIED_AT => 'datetime',
            self::PASSWORD => 'hashed',
        ];
    }

    public function relatedRole(): BelongsTo
    {
        return $this->belongsTo(Role::class, self::ROLE_ID);
    }

    public function getName(): string
    {
        return $this->getAttribute(self::NAME);
    }

    public function getPhone(): ?string
    {
        return $this->getAttribute(self::PHONE);
    }

    public function getEmail(): string
    {
        return $this->getAttribute(self::EMAIL);
    }

    public function getRoleId(): int
    {
        return $this->getAttribute(self::ROLE_ID);
    }

    public function getCreatedAt(): Carbon
    {
        return $this->getAttribute(self::CREATED_AT);
    }
}
