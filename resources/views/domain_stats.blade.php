@extends('layouts.app')
@section('content')
    <nav class="bread">
        <ol>
            <li>
                <a href="{{ route('stats') }}">
                    Home
                </a>
            </li>
            <li>
                >
            </li>
            <li>
                {{ $domain->fqdn }}
            </li>
        </ol>
    </nav>
    <div class="col-12 mt-3 mb-3">
        <canvas id="throughputsChart"></canvas>
    </div>
    <div class="col-12 mt-3 mb-3">
        <canvas id="totalEarnedChart"></canvas>
    </div>
    <div class="col-12 mt-3 mb-3">
        <canvas id="totalRunningNodesChart"></canvas>
    </div>

    <div class="row mb-4">
        <div class="col-sm-12 col-md-6">
            <div class="card card-info h-100">
                <div class="card-body">
                    <div class="card-info-title">
                        Info
                    </div>
                    <div class="mb-2 card-row">
                        <div>Name</div>
                        <div>{{ $domain->display_name }}</div>
                    </div>
                    <div class="mb-2 card-row">
                        <div>Domain</div>
                        <div>{{ $domain->fqdn }}</div>
                    </div>
                    <div class="mb-2 card-row">
                        <div>LLM</div>
                        <div>{{ $domain->llm_requirements }}</div>
                    </div>
                    <div class="mb-2 card-row">
                        <div>Approval</div>
                        <div>{{ $domain->approval_method }}</div>
                    </div>
                    <div class="mb-2 card-row">
                        <div>Tier</div>
                        <div>{{ $domain->domain_tier }}</div>
                    </div>
                    <div class="mb-2 card-row">
                        <div>Created</div>
                        <div>{{ $domain->created }}</div>
                    </div>
                    <div class="mb-2 card-row">
                        <div>Last update</div>
                        <div>{{ $last_stat->created_at->format('Y-m-d H:i') }} UTC</div>
                    </div>
                    <div class="mb-2 card-row">
                        <div>Next update</div>
                        <div>{{ $last_stat->created_at->addHours(4)->format('Y-m-d H:i') }} UTC</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-12 col-md-6">
            <div class="card card-info h-100">
                <div class="card-body">
                    <div class="card-info-title">
                        Stats
                    </div>
                    <div class="mb-2 card-row">
                        <div>Throughputs</div>
                        <div>{{ number_format($domain->throughputs) }}</div>
                    </div>
                    <div class="mb-2 card-row">
                        <div>Throughputs change(4h)</div>
                        <div>{{ number_format($domain->throughputs_change) }}</div>
                    </div>
                    <div class="mb-2 card-row">
                        <div>Throughputs change(24h)</div>
                        <div>{{ $day_stat['throughputs_change'] }}</div>
                    </div>
                    <div class="mb-2 card-row">
                        <div>Earned</div>
                        <div>{{ number_format($domain->total_earned, 2) }}</div>
                    </div>
                    <div class="mb-2 card-row">
                        <div>Earned change(4h)</div>
                        <div>{{ number_format($domain->total_earned_change, 2) }}</div>
                    </div>
                    <div class="mb-2 card-row">
                        <div>Earned change(24h)</div>
                        <div>{{ $day_stat['total_earned_change'] }}</div>
                    </div>
                    <div class="mb-2 card-row">
                        <div>Nodes</div>
                        <div>{{ number_format($domain->total_running_nodes) }}</div>
                    </div>
                    <div class="mb-2 card-row">
                        <div>Nodes change(4h)</div>
                        <div>{{ number_format($domain->total_running_nodes_change) }}</div>
                    </div>
                    <div class="mb-2 card-row">
                        <div>Nodes change(24h)</div>
                        <div>{{ $day_stat['total_running_nodes_change'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-sm-12 col-md-6">
            <div class="card card-info h-100">
                <div class="card-body">
                    <div class="card-info-title">
                        Description
                    </div>
                    <div class="mb-2 card-row">
                        <div class="pt-2 pb-2">{{ $domain->description }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-md-6">
            <div class="card card-info h-100">
                <div class="card-body">
                    <div class="card-info-title">
                        System prompt
                    </div>
                    <div class="mb-2 card-row">
                        <div class="pt-2 pb-2">{{ $domain->system_prompt }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const labels = @json($labels);

            const throughputs = @json($statistics->pluck('throughputs')->toArray());
            const totalEarned = @json($statistics->pluck('total_earned')->toArray());
            const totalRunningNodes = @json($statistics->pluck('total_running_nodes')->toArray());

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

            // (Total Earned)
            const ctxTotalEarned = document.getElementById('totalEarnedChart').getContext('2d');
            const totalEarnedChart = new Chart(ctxTotalEarned, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Earned',
                        data: totalEarned,
                        backgroundColor: 'rgba(153, 102, 255, 0.2)',
                        borderColor: 'rgba(153, 102, 255, 1)',
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
                                text: 'Earned'
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

            // (Total Earned)
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
@endsection
