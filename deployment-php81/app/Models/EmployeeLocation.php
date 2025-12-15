<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class EmployeeLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'latitude',
        'longitude',
        'accuracy',
        'altitude',
        'speed',
        'heading',
        'battery_level',
        'is_charging',
        'recorded_at',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'accuracy' => 'decimal:2',
        'altitude' => 'decimal:2',
        'speed' => 'decimal:2',
        'heading' => 'decimal:2',
        'battery_level' => 'decimal:2',
        'is_charging' => 'boolean',
        'recorded_at' => 'datetime',
    ];

    // Accessors for backwards compatibility
    protected function lat(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->latitude,
            set: fn ($value) => ['latitude' => $value],
        );
    }

    protected function lng(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->longitude,
            set: fn ($value) => ['longitude' => $value],
        );
    }

    protected function battery(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->battery_level,
        );
    }

    protected function receivedAt(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->created_at,
        );
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
