<?php

namespace App\Jobs;

use App\Models\DomainStatisticsChange;
use App\Models\Statistics;
use App\Services\DownloadAndSaveImage;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use App\Models\Domain;
use App\Models\DomainStatistic;
use Illuminate\Support\Facades\Log;

class FetchDomainStatisticsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Выполняем запрос к API
        $response = Http::withOptions(['verify' => false])->get('https://api.gaianet.ai/api/v1/network/domains/');

        if ($response->successful()) {
            $data = $response->json();
            $total_nodes = 0;
            $total_throughputs = 0;
            $total_earned = 0;

            foreach ($data['data']['objects'] as $object) {
                $total_nodes += $object['statistics']['total_running_nodes'];
                $total_throughputs += $object['statistics']['throughputs'];
                $total_earned += $object['statistics']['total_earned'];

                $check_domain = Domain::where('domain_id', $object['id'])->first();

                // Обновляем или создаем запись в таблице domains
                $domain = Domain::updateOrCreate(
                    ['domain_id' => $object['id']], // Условие поиска
                    [
                        'fqdn' => $object['fqdn'],
                        'display_name' => $object['display_name'],
                        'avatar_url' => $object['avatar_url'],
                        'gdn' => $object['gdn'],
                        'total_running_nodes' => $object['statistics']['total_running_nodes'],
                        'throughputs' => $object['statistics']['throughputs'],
                        'total_earned' => $object['statistics']['total_earned'],
                        'domain_name' => $object['domain_name'],
                        'system_prompt' => $object['system_prompt'],
                        'description' => $object['description'],
                        'hosting_type' => $object['hosting_type'],
                        'approval_method' => $object['approval_method'],
                        'llm_requirements' => $object['llm_requirements'],
                        'server_configuration' => $object['server_configuration'],
                        'domain_tier' => $object['domain_tier'],
                        'initial_stake_tokens' => $object['initial_stake_tokens'],
//                        'owner_id' => $object['owner_id'],
//                        'owner_wallet_address' => $object['owner_wallet_address'],
                        'status' => $object['status'],
                        'region' => $object['region'],
//                        'created' => Carbon::parse($object['created_at']),
//                        'updated' => Carbon::parse($object['updated_at']),
                        'throughputs_change' => $check_domain ? $object['statistics']['throughputs'] - $check_domain->throughputs : 0,
                        'total_earned_change' => $check_domain ? $object['statistics']['total_earned'] - $check_domain->total_earned : 0,
                        'total_running_nodes_change' => $check_domain ? $object['statistics']['total_running_nodes'] - $check_domain->total_running_nodes : 0,
                    ]
                );

                if(!empty($object['avatar_url']) and (!$check_domain or $check_domain->avatar_url != $object['avatar_url'])) {
                    $avatar = new DownloadAndSaveImage($object['avatar_url'], $domain->domain_id);
                    $avatar->run();
                }

                // Сохраняем статистику в таблицу domain_statistics
                DomainStatistic::create([
                    'domain_id' => $domain->domain_id,
                    'total_running_nodes' => $object['statistics']['total_running_nodes'],
                    'throughputs' => $object['statistics']['throughputs'],
                    'total_earned' => $object['statistics']['total_earned'],
                ]);
            }

            Statistics::create([
                'nodes' => $total_nodes,
                'throughputs' => $total_throughputs,
                'earned' => $total_earned,
            ]);

            Log::info('Domain statistics fetched and saved successfully.');
        } else {
            Log::error('Failed to fetch domain statistics.');
        }
    }
}
