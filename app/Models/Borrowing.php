<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Borrowing extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'equipment_id',
        'borrowed_at',
        'expected_return_at',
        'returned_at',
        'status',
        'condition_on_return',
        'accessories_brought_json',
    ];

    protected $casts = [
        'borrowed_at' => 'datetime',
        'expected_return_at' => 'datetime',
        'returned_at' => 'datetime',
        'accessories_brought_json' => 'array',
    ];

    public function isOverdue()
    {
        return $this->returned_at === null && $this->expected_return_at < now();
    }

    public function scopeOverdue($query)
    {
        return $query->whereNull('returned_at')->where('expected_return_at', '<', now());
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }
}
