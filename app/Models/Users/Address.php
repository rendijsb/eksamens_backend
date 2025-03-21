<?php

declare(strict_types=1);

namespace App\Models\Users;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    const ID = 'id';
    const USER_ID = 'user_id';
    const NAME = 'name';
    const PHONE = 'phone';
    const STREET_ADDRESS = 'street_address';
    const APARTMENT = 'apartment';
    const CITY = 'city';
    const STATE = 'state';
    const POSTAL_CODE = 'postal_code';
    const COUNTRY = 'country';
    const IS_DEFAULT = 'is_default';
    const TYPE = 'type';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        self::USER_ID,
        self::NAME,
        self::PHONE,
        self::STREET_ADDRESS,
        self::APARTMENT,
        self::CITY,
        self::STATE,
        self::POSTAL_CODE,
        self::COUNTRY,
        self::IS_DEFAULT,
        self::TYPE
    ];

    protected $casts = [
        self::IS_DEFAULT => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getId(): int
    {
        return $this->getAttribute(self::ID);
    }

    public function getUserId(): int
    {
        return $this->getAttribute(self::USER_ID);
    }

    public function getName(): string
    {
        return $this->getAttribute(self::NAME);
    }

    public function getPhone(): string
    {
        return $this->getAttribute(self::PHONE);
    }

    public function getStreetAddress(): string
    {
        return $this->getAttribute(self::STREET_ADDRESS);
    }

    public function getApartment(): ?string
    {
        return $this->getAttribute(self::APARTMENT);
    }

    public function getCity(): string
    {
        return $this->getAttribute(self::CITY);
    }

    public function getState(): ?string
    {
        return $this->getAttribute(self::STATE);
    }

    public function getPostalCode(): string
    {
        return $this->getAttribute(self::POSTAL_CODE);
    }

    public function getCountry(): string
    {
        return $this->getAttribute(self::COUNTRY);
    }

    public function getIsDefault(): bool
    {
        return $this->getAttribute(self::IS_DEFAULT);
    }

    public function getType(): string
    {
        return $this->getAttribute(self::TYPE);
    }

    public function getCreatedAt(): Carbon
    {
        return $this->getAttribute(self::CREATED_AT);
    }

    public function getUpdatedAt(): Carbon
    {
        return $this->getAttribute(self::UPDATED_AT);
    }

    public function getFullAddress(): string
    {
        $address = $this->getStreetAddress();

        if ($this->getApartment()) {
            $address .= ', ' . $this->getApartment();
        }

        $address .= ', ' . $this->getCity();

        if ($this->getState()) {
            $address .= ', ' . $this->getState();
        }

        $address .= ', ' . $this->getPostalCode();
        $address .= ', ' . $this->getCountry();

        return $address;
    }
}
