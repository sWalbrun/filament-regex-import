<?php

namespace SWalbrun\FilamentModelImport\Tests\__Data__\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property int $id
 * @property string name
 * @property string email
 * @property string password
 */
class User extends Authenticatable
{
    use HasRoles;

    public const TABLE = 'user';

    protected $table = self::TABLE;

    public const COL_ID = 'id';
    public const COL_NAME = 'name';
    public const COL_EMAIL = 'email';
    public const COL_PASSWORD = 'password';
    public const COL_EMAIL_VERIFIED_AT = 'email_verified_at';
    public const COL_REMEMBER_TOKEN = 'remember_token';
    public const COL_TWO_FACTOR_SECRET = 'two_factor_secret';
    public const COL_TWO_FACTOR_RECOVERY_CODES = 'two_factor_recovery_codes';
    public const COL_CURRENT_TEAM_ID = 'current_team_id';
    public const COL_PROFILE_PHOTO_PATH = 'profile_photo_path';
    public const COL_CREATED_AT = 'createdAt';
    public const CREATED_AT = self::COL_CREATED_AT;
    public const COL_UPDATED_AT = 'updatedAt';
    public const UPDATED_AT = self::COL_UPDATED_AT;
    public const COL_CONTRIBUTION_GROUP = 'contributionGroup';
    public const COL_JOIN_DATE = 'joinDate';
    public const COL_EXIT_DATE = 'exitDate';
    public const COL_COUNT_SHARES = 'countShares';
    public const COL_PAYMENT_INTERVAL = 'paymentInterval';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        self::COL_NAME,
        self::COL_EMAIL,
        self::COL_PASSWORD,
        self::COL_CONTRIBUTION_GROUP,
        self::COL_JOIN_DATE,
        self::COL_EXIT_DATE,
        self::COL_COUNT_SHARES,
        self::COL_PAYMENT_INTERVAL,
        self::COL_EMAIL_VERIFIED_AT,
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        self::COL_PASSWORD,
        self::COL_REMEMBER_TOKEN,
        self::COL_TWO_FACTOR_RECOVERY_CODES,
        self::COL_TWO_FACTOR_SECRET,
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        self::COL_EMAIL_VERIFIED_AT => 'datetime',
        self::COL_JOIN_DATE => 'datetime',
        self::COL_EXIT_DATE => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        self::creating(fn (User $user) => $user->password ??= Hash::make(Str::random(10)));
    }

    public function name(): string
    {
        return $this->name ?? '';
    }

    public function email(): string
    {
        return $this->email ?? '';
    }

    public function identifier(): string
    {
        return self::TABLE;
    }
}
