<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'name',
        'serial_number',
        'qr_code_identifier',
        'description',
        'status',
    ];

    public function accessories()
    {
        return $this->hasMany(Accessory::class);
    }

    public function currentBorrowing()
    {
        return $this->hasOne(Borrowing::class)->whereNull('returned_at')->latestOfMany();
    }

    public function borrowings()
    {
        return $this->hasMany(Borrowing::class);
    }

    public function repairs()
    {
        return $this->hasMany(Repair::class);
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class, 'model_id')
                    ->where('model_type', get_class($this))
                    ->orderBy('created_at', 'desc');
    }

    public function getRouteKeyName()
    {
        return 'qr_code_identifier';
    }
}
