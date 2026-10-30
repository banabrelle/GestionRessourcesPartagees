<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles; 


class User extends Authenticatable
{
     use HasRoles; // ✅ ici
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'matricule',
        'password',
        'must_change_password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'must_change_password' => 'boolean',
        ];
    }

        // ── Relation 1:1 avec Personnel ─────────────────────────────
    // Un User est associé à un seul Personnel
    public function personnel()
    {
        return $this->hasOne(Personnel::class, 'id_user');
    }

    // ── Notifications ────────────────────────────────────────────
    public function notificationsApp()
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    public function notificationsNonLues()
    {
        return $this->notificationsApp()->where('lu', false);
    }

    // ── Scope actifs ─────────────────────────────────────────────
    // public function scopeActifs($query)
    // {
    //     return $query->where('is_active', true);
    // }
}


