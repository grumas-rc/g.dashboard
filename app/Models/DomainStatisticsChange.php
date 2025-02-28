<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DomainStatisticsChange extends Model
{
    use HasFactory;
    protected $primaryKey = 'change_id'; // Указываем первичный ключ
    public $timestamps = true; // Используем timestamps

    protected $fillable = [
        'domain_id',
        'throughputs_change',
        'total_earned_change',
        'total_running_nodes_change',
    ];

    // Связь с таблицей domains
    public function domain(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Domain::class, 'domain_id', 'domain_id');
    }
}
