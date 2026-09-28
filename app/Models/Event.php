<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'external_only' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function isOwnedBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        // Events created before ownership existed have no owner and stay
        // manageable by any admin for backward compatibility.
        return $this->user_id === null || $this->user_id === $user->id;
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
