<?php

declare(strict_types=1);

namespace App\Models\Users;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\Images\ImageTypeEnum;
use App\Models\Newsletter\NewsletterSubscription;
use App\Models\Orders\Order;
use App\Models\Roles\Role;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    const ID = 'id';
    const NAME = 'name';
    const EMAIL = 'email';
    const PASSWORD = 'password';
    const PHONE = 'phone';
    const PROFILE_IMAGE = 'profile_image';
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
        self::PROFILE_IMAGE,
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

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'user_id');
    }

    public function relatedNewsletter(): ?NewsletterSubscription
    {
        return NewsletterSubscription::firstWhere(
            NewsletterSubscription::EMAIL, $this->getEmail()
        );
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

    public function getId(): int
    {
        return $this->getAttribute(self::ID);
    }

    public function getProfileImage(): ?string
    {
        return $this->getAttribute(self::PROFILE_IMAGE);
    }

    public function getProfileImageUrl(): ?string
    {
        if (!$this->getProfileImage()) {
            return null;
        }

        $imagePath = ImageTypeEnum::PROFILE->value . '/' . $this->getProfileImage();
        $url = Storage::disk('s3')->url($imagePath);

        return $url . '?v=' . time();
    }

    public function notificationPreferences(): HasOne
    {
        return $this->hasOne(NotificationPreference::class);
    }

    public function getOrCreateNotificationPreferences(): NotificationPreference
    {
        if (!$this->notificationPreferences) {
            return NotificationPreference::createDefaultForUser($this->getId());
        }

        return $this->notificationPreferences;
    }
}
