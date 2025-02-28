<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
    use HasFactory;

    const PER_PAGE = 50;
    protected $primaryKey = 'domain_id'; // Указываем первичный ключ
    public $timestamps = true; // Используем timestamps

    protected $fillable = [
        'fqdn',
        'display_name',
        'avatar_url',
        'gdn',
        'domain_name',
        'system_prompt',
        'description',
        'hosting_type',
        'approval_method',
        'llm_requirements',
        'server_configuration',
        'domain_tier',
        'initial_stake_tokens',
        'owner_id',
        'owner_wallet_address',
        'status',
        'region',
        'total_running_nodes',
        'throughputs',
        'total_earned',
        'throughputs_change',
        'total_earned_change',
        'total_running_nodes_change',
        'created',
        'updated',
    ];

    // Связь с таблицей статистики
    public function statistics(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DomainStatistic::class, 'domain_id', 'domain_id');
    }

    public function latestStatistics(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(DomainStatistic::class, 'domain_id', 'domain_id')->latest('created_at');
    }

    public function latestChange(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(DomainStatisticsChange::class, 'domain_id', 'domain_id')->latest('created_at');
    }
}
