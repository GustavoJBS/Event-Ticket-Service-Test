<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\{Builder, Model};

class Event extends Model
{
    /** @use HasFactory<\Database\Factories\EventFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'date'       => 'datetime:Y-m-d H:i:s',
            'created_at' => 'datetime:Y-m-d H:i:s',
            'updated_at' => 'datetime:Y-m-d H:i:s',
        ];
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function scopeFilters(Builder $query, array $filters): Builder
    {
        return $query->when($filters, function (Builder $query, array $filters) {
            $query->where(function (Builder $query) use ($filters) {
                foreach ($filters as $prop => $value) {
                    if (!$value) {
                        continue;
                    }

                    match ($prop) {
                        'only_available' => $query->where('remaining_availability', '>', 0),
                        'start_date'     => $query->whereDate('date', '>=', $value),
                        'end_date'       => $query->whereDate('date', '<=', $value),
                        default          => $query->where($prop, 'LIKE', "%{$value}%"),
                    };
                }
            });
        });
    }

    public static function boot(): void
    {
        parent::boot();

        static::creating(function (self $event) {
            $event->remaining_availability ??= $event->total_availability;
        });
    }
}
