<?php

namespace App\Http\Controllers;

use App\Models\Domain;
use App\Models\DomainStatistic;
use App\Models\DomainStatisticsChange;
use App\Models\Statistics;
use App\Services\DownloadAndSaveImage;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DomainStatisticsController extends Controller
{
    public function getDomainsList()
    {
        $domains = Domain::orderByDesc('total_running_nodes')->get(['fqdn', 'total_running_nodes', 'total_earned_change as 4h_earned', 'throughputs_change as 4h_throughputs']);
        return response()->json($domains);
    }

    public function avatars()
    {
        $domains = Domain::all();
        foreach ($domains as $domain) {
            if (!$domain->avatar_url or \Storage::disk('public')->exists('images/' . $domain->domain_id . '.webp')) {
                continue;
            }
            echo 'downl ' . $domain->avatar_url . PHP_EOL;
            $test = new DownloadAndSaveImage($domain->avatar_url, $domain->domain_id);
            $test->run();
        }
    }

    public function getDomain(int $domain_id)
    {
        $domain = Domain::findOrFail($domain_id);

        $statistics = DomainStatistic::where('domain_id', $domain_id)
            ->orderBy('created_at', 'desc')
            ->take(24)
            ->get()
            ->sortBy('created_at');

        $last_stat = $statistics->last();
        $start_date = Carbon::parse($last_stat->created_at)->subDays()->subHours();
        $first_stat = $statistics->where('created_at', '>=', $start_date)->first();

        $day_stat = [
            'throughputs_change' => number_format($last_stat->throughputs - $first_stat->throughputs),
            'total_earned_change' => number_format($last_stat->total_earned - $first_stat->total_earned, 2),
            'total_running_nodes_change' => number_format($last_stat->total_running_nodes - $first_stat->total_running_nodes),
        ];

// Подготавливаем данные для графика
        $labels = $statistics->pluck('created_at')->map(function ($date) {
            return \Carbon\Carbon::parse($date)->format('Y-m-d H:i'); // Форматируем дату
        })->toArray();

        return view('domain_stats', compact('domain', 'statistics', 'labels', 'day_stat', 'last_stat'));
    }

    public function getDomains(Request $request)
    {
        $default_order = 'throughputs';
        $allowed_orders = ['fqdn', 'throughputs', 'throughputs_change', 'total_earned', 'total_earned_change', 'total_running_nodes', 'total_running_nodes_change'];
        $query = Domain::query();

// Фильтрация по домену
        if ($request->has('filter.domain')) {
            $query->where('fqdn', 'like', '%' . $request->input('filter.domain') . '%');
        }
        if ($request->has('filter.llm')) {
            $query->where('llm_requirements', 'like', '%' . $request->input('filter.llm') . '%');
        }
        if ($request->has('filter.approve')) {
            $query->where('approval_method', 'like', '%' . $request->input('filter.approve') . '%');
        }

// Сортировка
        if ($request->has('sort')) {
//            $direction = $request->input('direction', 'asc');
            $order_by = in_array($request->input('sort'), $allowed_orders) ? $request->input('sort') : $default_order;
            $direction = $request->input('direction', 'asc') == 'desc' ? 'desc' : 'asc';
            $query->orderBy($order_by, $direction);
        } else {
            $query->orderByDesc($default_order);
        }

        $domains = $query->paginate(Domain::PER_PAGE);

        $domains_stats = Domain::all(['domain_id', 'domain_tier', 'throughputs', 'total_running_nodes', 'llm_requirements', 'approval_method', 'updated_at']);

        $llm = $domains_stats->unique('llm_requirements')->sortBy('llm_requirements')->pluck('llm_requirements')->toArray();
        $approvals = ['Automation', 'Manual'];

        $domain_tiers = $domains_stats->groupBy('domain_tier')->map(function ($group) {
            return $group->count();
        });
        $domain_tiers = $domain_tiers->sortByDesc(function ($count, $tier) {
            return $count;
        });

        $domain_llm = $domains_stats->groupBy('llm_requirements')->map(function ($group) {
            return $group->count();
        });
        $domain_llm = $domain_llm->sortByDesc(function ($count, $llm) {
            return $count;
        });

        $statistics = Statistics::orderBy('created_at', 'desc')
            ->take(12)
            ->get()
            ->sortBy('created_at');

// Подготавливаем данные для графика
        $labels = $statistics->pluck('created_at')->map(function ($date) {
            return \Carbon\Carbon::parse($date)->format('Y-m-d H:i');
        })->toArray();

        return view('stats', compact('domains', 'domain_tiers', 'domain_llm', 'domains_stats', 'statistics', 'labels', 'llm', 'approvals'));
    }
}
