<?php

namespace App\Models;

use App\Notifications\RestablecerPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'avatar', 'telefono', 'bio'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /** URL pública del avatar (o una imagen generada con las iniciales). */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return Storage::url($this->avatar);
        }

        // Sin foto: genera un círculo con las iniciales (SVG en línea, no depende de internet)
        $iniciales = collect(explode(' ', trim($this->name)))->filter()->take(2)
            ->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))->join('');
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><rect width="64" height="64" fill="#7c3aed"/>'
            .'<text x="50%" y="50%" dy=".35em" text-anchor="middle" font-family="Arial" font-size="26" fill="#fff">'
            .e($iniciales).'</text></svg>';

        return 'data:image/svg+xml;base64,'.base64_encode($svg);
    }

    public function peliculas(): HasMany
    {
        return $this->hasMany(Pelicula::class);
    }

    public function animes(): HasMany
    {
        return $this->hasMany(Anime::class);
    }

    /** Usa nuestro correo personalizado (en español) para "olvidé mi contraseña". */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new RestablecerPassword($token));
    }
}
