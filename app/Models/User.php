<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Clinics\Clinic\Clinic;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'avatar_path',
        'password',
        'clinic_id', // Adicionado na Etapa 1
        'phone', // Adicionado na Etapa 1
        'specialties', // Adicionado nesta Etapa 3
        'calendar_color',
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
            'specialties' => 'array', // Importante para tratar o campo JSON
        ];
    }

    // Relação com a Clínica (já definida anteriormente)
    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    /**
     * Get the URL for the user's avatar.
     */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar_path) {
            return asset('storage/'.$this->avatar_path);
        }

        return 'https://ui-avatars.com/api/?name='.urlencode($this->name).'&background=3b82f6&color=fff&bold=true';
    }

    /**
     * Get the user's initials.
     */
    public function getInitialsAttribute(): string
    {
        $words = explode(' ', $this->name);
        $initials = '';

        if (count($words) >= 2) {
            $initials = strtoupper(substr($words[0], 0, 1).substr(end($words), 0, 1));
        } else {
            $initials = strtoupper(substr($this->name, 0, 1));
        }

        return $initials;
    }
}
