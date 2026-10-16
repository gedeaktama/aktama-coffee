<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public const ROLE_OWNER = 'owner';
    public const ROLE_ADMIN = 'admin';
    public const ROLE_SALES = 'sales';
    public const ROLE_WAREHOUSE = 'warehouse';
    public const ROLE_FINANCE = 'finance';
    public const ROLE_DRIVER = 'driver';
    public const ROLE_CUSTOMER = 'customer';

    public const ROLES = [
        self::ROLE_OWNER => 'Owner',
        self::ROLE_ADMIN => 'Admin',
        self::ROLE_SALES => 'Sales',
        self::ROLE_WAREHOUSE => 'Warehouse',
        self::ROLE_FINANCE => 'Finance',
        self::ROLE_DRIVER => 'Driver / Kurir Lapangan',
        self::ROLE_CUSTOMER => 'Customer',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'role',
        'is_active',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    public function isOwner(): bool
    {
        return $this->role === self::ROLE_OWNER;
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isSales(): bool
    {
        return $this->role === self::ROLE_SALES;
    }

    public function isWarehouse(): bool
    {
        return $this->role === self::ROLE_WAREHOUSE;
    }

    public function isFinance(): bool
    {
        return $this->role === self::ROLE_FINANCE;
    }

    public function isCustomer(): bool
    {
        return $this->role === self::ROLE_CUSTOMER;
    }

    public function isDriver(): bool
    {
        return $this->role === self::ROLE_DRIVER;
    }

    public function storeVisits(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(StoreVisit::class);
    }

    /**
     * Check if user has any of the given roles.
     *
     * @param string|array $roles
     */
    public function hasRole(string|array $roles): bool
    {
        if (is_string($roles)) {
            $roles = array_map('trim', explode(',', $roles));
        }

        return in_array($this->role, $roles, true);
    }

    public function customer(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Customer::class, 'user_id');
    }

    /**
     * Ambil tipe customer untuk penentuan harga khusus (retail, toko, kafe)
     */
    public function getCustomerType(): string
    {
        return $this->customer?->customer_type ?? Customer::TYPE_RETAIL;
    }

    public function isToko(): bool
    {
        return $this->getCustomerType() === Customer::TYPE_TOKO || $this->getCustomerType() === Customer::TYPE_RESELLER;
    }

    public function isKafe(): bool
    {
        return $this->getCustomerType() === Customer::TYPE_KAFE;
    }

    /**
     * Determine if user has internal dashboard access.
     */
    public function canAccessDashboard(): bool
    {
        return in_array($this->role, [
            self::ROLE_OWNER,
            self::ROLE_ADMIN,
            self::ROLE_SALES,
            self::ROLE_WAREHOUSE,
            self::ROLE_FINANCE,
            self::ROLE_DRIVER,
        ], true);
    }
}
