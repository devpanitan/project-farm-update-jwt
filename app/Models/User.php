<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
       'name',
       'user_role_id',
       'password',
       'google',
       'email',
       'tel',
       'address',
       'birth_date',
       'firstname',
       'lastname',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed',
        'birth_date' => 'date',
        'last_login' => 'datetime',
    ];

    /**
     * Get the role of the user.
     */
    public function userRole()
    {
        return $this->belongsTo(UserRole::class, 'user_role_id');
    }

    /**
     * The farms that the user belongs to.
     */
    public function farms()
    {
        return $this->belongsToMany(Farm::class, 'user_farms');
    }

    /**
     * Check if the user is a Super Admin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->user_role_id === 1;
    }

    /**
     * Check if the user is a Farm Owner.
     */
    public function isFarmOwner(): bool
    {
        return $this->user_role_id === 2;
    }

    /**
     * Check if the user is a Farm Worker.
     */
    public function isFarmWorker(): bool
    {
        return $this->user_role_id === 3;
    }

     /**
     * Check if the user is the owner of a specific farm.
     */
    public function isOwnerOfFarm($farmId): bool
    {
        // A user is the owner of the farm if they are a Farm Owner AND are linked to that farm.
        if (!$this->isFarmOwner()) {
            return false;
        }
        return $this->farms()->where('farm_id', $farmId)->exists();
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
