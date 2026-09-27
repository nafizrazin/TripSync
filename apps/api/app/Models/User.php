<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
final class User extends Authenticatable {
    use HasFactory, HasUlids, Notifiable;
    protected $fillable = ['name','email','phone','password','status'];
    protected $hidden = ['password','remember_token'];
    protected function casts(): array { return ['email_verified_at'=>'datetime','password'=>'hashed']; }
    public function roles(): BelongsToMany { return $this->belongsToMany(Role::class); }
    public function hasRole(string ...$roles): bool { return $this->roles()->whereIn('name',$roles)->exists(); }
}
