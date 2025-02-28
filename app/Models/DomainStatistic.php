<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DomainStatistic extends Model
{
    use HasFactory;

    protected $primaryKey = 'domain_statistic_id'; // Указываем первичный ключ
    public $timestamps = true; // Используем timestamps

    protected $fillable = [
        'domain_id',
        'total_running_nodes',
        'throughputs',
        'total_earned',
    ];

    // Связь с таблицей domains
    public function domain(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Domain::class, 'domain_id', 'domain_id');
    }
}
