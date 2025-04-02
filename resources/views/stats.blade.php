@extends('layouts.app')
@section('content')
    <div class="row row-cols-1 row-cols-md-3 g-3 mb-3">
        <div class="col">
            <div class="card card-info h-100">
                <div class="card-body">
                    <div class="card-info-title">
                        Stats
                    </div>
                    <div class="mb-2 card-row">
                        <div>Domains</div>
                        <div>{{ $domains_stats->count() }}</div>
                    </div>
                    <div class="mb-2 card-row">
                        <div>Nodes</div>
                        <div>{{ number_format($domains_stats->sum('total_running_nodes'), 0, '.', ',') }}</div>
                    </div>
                    <div class="mb-2 card-row">
                        <div>Throughputs</div>
                        <div>{{ number_format($domains_stats->sum('throughputs'), 0, '.', ',') }}</div>
                    </div>
                    <div class="mb-2 card-row">
                        <div>Last update</div>
                        <div>{{ $domains_stats->sortByDesc('updated_at')->first()->updated_at->format('Y-m-d H:i') }}
                            UTC
                        </div>
                    </div>
                    <div class="mb-2 card-row">
                        <div>Next update</div>
                        <div>{{ $domains_stats->sortByDesc('updated_at')->first()->updated_at->addHours(4)->format('Y-m-d H:i') }}
                            UTC
                        </div>
                    </div>
                    <div>
                        <small class="text-muted">Updates every 4 hours.</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card card-info h-100">
                <div class="card-body">
                    <div class="card-info-title">
                        LLM
                    </div>
                    @foreach($domain_llm as $llm_name => $count)
                        <div class="mb-2 card-row llm-item" style="display: none;">
                            <div>{{ $llm_name }}</div>
                            <div>{{ $count }}</div>
                        </div>
                    @endforeach
                    <a id="showMoreBtn" class="btn-link node-link">Show all</a>
                    <a id="hideMoreBtn" class="btn-link node-link" style="display: none;">Hide</a>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card card-info h-100">
                <div class="card-body">
                    <div class="card-info-title">
                        Tiers
                    </div>
                    @foreach($domain_tiers as $tier => $count)
                        <div class="mb-2 card-row">
                            <div>{{ $tier }}</div>
                            <div>{{ $count }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-6 mt-3 mb-3">
            <canvas id="throughputsChart"></canvas>
        </div>
        <div class="col-6 mt-3 mb-3">
            <canvas id="totalRunningNodesChart"></canvas>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const labels = @json($labels);

            const throughputs = @json($statistics->pluck('throughputs')->toArray());
            const totalRunningNodes = @json($statistics->pluck('nodes')->toArray());

            // (Throughputs)
            const ctxThroughputs = document.getElementById('throughputsChart').getContext('2d');
            const throughputsChart = new Chart(ctxThroughputs, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Throughputs',
                        data: throughputs,
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    interaction: {
                        intersect: false,
                    },
                    scales: {
                        x: {
                            type: 'category',
                            title: {
                                display: false,
                                text: 'Date'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Throughputs'
                            }
                        }
                    },
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });

            // (Total Nodes)
            const ctxTotalRunningNodes = document.getElementById('totalRunningNodesChart').getContext('2d');
            const totalRunningNodesChart = new Chart(ctxTotalRunningNodes, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Nodes',
                        data: totalRunningNodes,
                        backgroundColor: 'rgba(102,133,255,0.2)',
                        borderColor: 'rgb(102,127,255)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    interaction: {
                        intersect: false,
                    },
                    scales: {
                        x: {
                            type: 'category',
                            title: {
                                display: false,
                                text: 'Date'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Nodes'
                            }
                        }
                    },
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        });
    </script>

    <form method="GET" action="{{ route('stats') }}" class="row g-3 mt-2 mb-4">
        <div class="col-sm-12 col-md-3 col-xxl-2">
            <div class="input-group">
                <div class="input-group-text">Domain</div>
                <input
                    id="domain"
                    type="text"
                    class="form-control"
                    name="filter[domain]"
                    placeholder="All"
                    value="{{ request('filter.domain') }}">
            </div>
        </div>
        <div class="col-sm-12 col-md-3 col-xxl-2">
            <div class="input-group">
                <div class="input-group-text">LLM</div>
                <select class="form-select" name="filter[llm]">
                    <option @selected(request('filter.llm', 'all') == 'all') value="">All</option>
                    @foreach($llm as $l)
                        <option @selected(request('filter.llm') == $l)>{{$l}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-sm-12 col-md-3 col-xxl-2">
            <div class="input-group">
                <div class="input-group-text">Approve</div>
                <select class="form-select" name="filter[approve]">
                    <option @selected(request('filter.approve', 'all') == 'all') value="">All</option>
                    @foreach($approvals as $approval)
                        <option @selected(request('filter.approve') == $approval)>{{ $approval }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-auto">
            <button class="btn btn-secondary" type="submit">Filter</button>
            <a class="btn btn-outline-secondary" href="{{ route('stats') }}">Reset</a>
        </div>

        {{--        <div class="col-sm-12 col-md-6">--}}
        {{--            <div class="input-group mb-3">--}}
        {{--                <button class="btn btn-secondary" type="submit">Filter</button>--}}
        {{--                <input--}}
        {{--                    type="text"--}}
        {{--                    class="form-control"--}}
        {{--                    name="filter[domain]"--}}
        {{--                    placeholder="by domain"--}}
        {{--                    value="{{ request('filter.domain') }}">--}}
        {{--                <select class="form-select">--}}
        {{--                    <option selected>All models</option>--}}
        {{--                    <option>...</option>--}}
        {{--                </select>--}}
        {{--                <select class="form-select">--}}
        {{--                    <option selected>All approve</option>--}}
        {{--                    <option>...</option>--}}
        {{--                </select>--}}
        {{--            </div>--}}
        {{--        </div>--}}
    </form>

    <table class="table table-striped">
        <thead>
        <tr>
            <td class="text-muted">#</td>
            <td></td>
            <td>
                <a class="text-muted" href="{{ request()->fullUrlWithQuery([
                'sort' => 'fqdn',
                'direction' => request('sort') === 'fqdn' ? (request('direction') === 'asc' ? 'desc' : 'asc') : 'asc'
            ]) }}">domain</a>
                @if(request('sort') === 'fqdn')
                    @if(request('direction') === 'asc')
                        <x-icons.arrow-up/>
                    @else
                        <x-icons.arrow-down/>
                    @endif
                @endif
            </td>
            <td class="text-center">
                <a class="text-muted" href="{{ request()->fullUrlWithQuery([
                'sort' => 'throughputs',
                'direction' => request('sort') === 'throughputs' ? (request('direction') === 'asc' ? 'desc' : 'asc') : 'desc'
            ]) }}">throughputs</a>
                @if(request('sort','throughputs') === 'throughputs')
                    @if(request('direction') === 'asc')
                        <x-icons.arrow-up/>
                    @else
                        <x-icons.arrow-down/>
                    @endif
                @endif
            </td>
            <td class="text-center">
                <a class="text-muted" href="{{ request()->fullUrlWithQuery([
                'sort' => 'throughputs_change',
                'direction' => request('sort') === 'throughputs_change' ? (request('direction') === 'asc' ? 'desc' : 'asc') : 'desc'
            ]) }}">throughputs change(4h)</a>
                @if(request('sort') === 'throughputs_change')
                    @if(request('direction') === 'asc')
                        <x-icons.arrow-up/>
                    @else
                        <x-icons.arrow-down/>
                    @endif
                @endif
            </td>
            <td class="text-center">
                <a class="text-muted" href="{{ request()->fullUrlWithQuery([
                'sort' => 'total_earned',
                'direction' => request('sort') === 'total_earned' ? (request('direction') === 'asc' ? 'desc' : 'asc') : 'desc'
            ]) }}">earned</a>
                @if(request('sort') === 'total_earned')
                    @if(request('direction') === 'asc')
                        <x-icons.arrow-up/>
                    @else
                        <x-icons.arrow-down/>
                    @endif
                @endif
            </td>
            <td class="text-center">
                <a class="text-muted" href="{{ request()->fullUrlWithQuery([
                'sort' => 'total_earned_change',
                'direction' => request('sort') === 'total_earned_change' ? (request('direction') === 'asc' ? 'desc' : 'asc') : 'desc'
            ]) }}">earned change(4h)</a>
                @if(request('sort') === 'total_earned_change')
                    @if(request('direction') === 'asc')
                        <x-icons.arrow-up/>
                    @else
                        <x-icons.arrow-down/>
                    @endif
                @endif
            </td>
            <td class="text-center">
                <a class="text-muted" href="{{ request()->fullUrlWithQuery([
                'sort' => 'total_running_nodes',
                'direction' => request('sort') === 'total_running_nodes' ? (request('direction') === 'asc' ? 'desc' : 'asc') : 'desc'
            ]) }}">nodes</a>
                @if(request('sort') === 'total_running_nodes')
                    @if(request('direction') === 'asc')
                        <x-icons.arrow-up/>
                    @else
                        <x-icons.arrow-down/>
                    @endif
                @endif
            </td>
            <td class="text-center">
                <a class="text-muted" href="{{ request()->fullUrlWithQuery([
                'sort' => 'total_running_nodes_change',
                'direction' => request('sort') === 'total_running_nodes_change' ? (request('direction') === 'asc' ? 'desc' : 'asc') : 'desc'
            ]) }}">nodes change(4h)</a>
                @if(request('sort') === 'total_running_nodes_change')
                    @if(request('direction') === 'asc')
                        <x-icons.arrow-up/>
                    @else
                        <x-icons.arrow-down/>
                    @endif
                @endif
            </td>
        </tr>
        </thead>
        <tbody>
        @php
            $count = \App\Models\Domain::PER_PAGE * (request('page', 1) - 1);
        @endphp
        @foreach($domains as $domain)
            @php
                $count++;
            @endphp
            <tr>
                <td class="align-middle">{{ $count }}</td>
                <td class="align-middle">
                    @if(Storage::disk('public')->exists('images/' . $domain->domain_id . '.webp'))
                        <img src="{{ Storage::url('images/' . $domain->domain_id . '.webp') }}"
                             alt="{{ $domain->fqdn }}" class="domain-avatar">
                    @endif
                </td>
                <td>
                    <div>
                        <a class="node-link" href="{{ route('domain.stats', ['domain_id' => $domain->domain_id]) }}">
                            {{ $domain->fqdn }}
                        </a>
                    </div>
                    <span class="badge-info">{{ $domain->llm_requirements }}</span>
                    <span class="badge-info">{{ $domain->approval_method }}</span>
                </td>
                <td class="text-center align-middle">{{ number_format($domain->throughputs, 0) }}</td>
                <td class="text-center align-middle">{{ number_format($domain->throughputs_change, 0) }}</td>
                <td class="text-center align-middle">{{ number_format($domain->total_earned, 2) }}</td>
                <td class="text-center align-middle">{{ number_format($domain->total_earned_change, 2) }}</td>
                <td class="text-center align-middle">{{ $domain->total_running_nodes }}</td>
                <td class="text-center align-middle">{{ $domain->total_running_nodes_change }}</td>
            </tr>

        @endforeach
        </tbody>
    </table>

    {{ $domains->withQueryString()->links() }}
@endsection
