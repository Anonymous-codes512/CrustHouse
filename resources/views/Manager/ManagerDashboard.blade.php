@extends('Components.Manager')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let branchName = document.getElementById('branch_name').value;
        let titleElement = document.getElementById('dynamic-title');
        titleElement.textContent = branchName + ' | Manager - Dashboard';
    });
</script>
@push('styles')
    <link rel="stylesheet" href="{{ asset('CSS/Manager/managerdashboard.css') }}">
@endpush

@section('main')
    <main id="dashboard">
        @php
            $id = $id;
            $branch_id = $branch_id;
            $branch = $branch;
            $branch_Name = $branch->branch_name . ' - ' . $branch->branch_city;
        @endphp

        <div class="heading">
            <h3>{{ $branch_Name }} </h3>
            <div id="branchServices">
                <h4>Services</h4>
                <div id="options" class="options">
                    <div class="opt" style="border-right: 1px solid #000;">
                        <p class="opt-txt">Rider</p>
                        <label class="switch">
                            <input type="checkbox" name="riderOption" @if ($branch->riderOption == 1) checked @endif
                                onclick="return false;">
                        </label>
                    </div>

                    <div class="opt" style="border-right: 1px solid #000;">
                        <p class="opt-txt">Online</p>
                        <label class="switch">
                            <input type="checkbox" name="onlineDeliveryOption"
                                @if ($branch->onlineDeliveryOption == 1) checked @endif onclick="return false;">
                        </label>
                    </div>

                    <div class="opt">
                        <p class="opt-txt">Dine-In</p>
                        <label class="switch">
                            <input type="checkbox" name="diningTableOption" @if ($branch->DiningOption == 1) checked @endif
                                onclick="return false;">
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="cards">
            <a class="category" id="category">
                <div class="icon">
                    <i class='bx bxs-category-alt'></i>
                </div>
                <div class="disc">
                    <p>Total Categories</p>
                    @if (session('totalCategories'))
                        <h3>{{ session('totalCategories') }}</h3>
                    @else
                        <h3>Nil</h3>
                    @endif
                </div>
            </a>

            <a class="products" id="products">
                <div class="icon">
                    <i class='bx bx-package'></i>
                </div>
                <div class="disc">
                    <p>Total Products</p>
                    @if (session('totalProducts'))
                        <h3>{{ session('totalProducts') }}</h3>
                    @else
                        <h3>Nil</h3>
                    @endif
                </div>
            </a>

            <a class="stock" id="stock">
                <div class="icon">
                    <i class='bx bxs-store'></i>
                </div>
                <div class="disc">
                    <p>Total Stock</p>
                    @if (session('totalStocks'))
                        <h3>{{ session('totalStocks') }}</h3>
                    @else
                        <h3>Nil</h3>
                    @endif
                </div>
            </a>

            <a class="branchRevenue" id="branchRevenue">
                <div class="icon">
                    <i class='bx bx-dollar-circle'></i>
                </div>
                <div class="disc">
                    <p>Branch Revenue</p>
                    @if (session('totalRevenue'))
                        <h3>Rs. {{ session('totalRevenue') }}</h3>
                    @else
                        <h3>Nil</h3>
                    @endif
                </div>
            </a>

        </div>

        @if (collect($annualRevenueArray)->isEmpty() &&
                collect($monthlyRevenueArray)->isEmpty() &&
                collect($dailyRevenueArray)->isEmpty())
            <div style="display: flex;flex-direction:column; justify-content:center; align-items:center;">
                <h3>No Data for graphs</h3>
            </div>
        @else
            <div class="graph">
                <div class="totalRevenueGraph">
                    <div class="info">
                        <p class="ttle">Annual Branch Revenue</p>
                    </div>
                    <canvas id="myChart"></canvas>
                    <script>
                        var ctx = document.getElementById('myChart').getContext('2d');
                        var minYear = {{ $minYear }};
                        var currentYear = new Date().getFullYear();
                        var years = [];

                        @foreach ($annualRevenueArray as $branch_id => $revenues)
                            @foreach ($revenues as $year => $revenue)
                                years.push('{{ $year }}');
                            @endforeach
                        @endforeach

                        var datasets = [
                            @foreach ($annualRevenueArray as $branch_id => $revenues)
                                {
                                    label: 'Yearly Revenue',
                                    data: [{{ implode(',', $revenues) }}],
                                    backgroundColor: '#ffbb00',
                                    borderColor: '#ffbb00',
                                    borderWidth: 2,
                                    fill: false
                                },
                            @endforeach
                        ];

                        var myChart = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: years,
                                datasets: datasets
                            },
                            options: {
                                scales: {
                                    yAxes: [{
                                        ticks: {
                                            beginAtZero: true
                                        }
                                    }]
                                }
                            }
                        });
                    </script>
                </div>

                <div class="monthlyRevenuegraph">
                    <div class="info">
                        <p class="ttle">Monthly Branch Revenue</p>
                    </div>
                    <canvas id="barChart">
                        <script>
                            var ctx = document.getElementById('barChart').getContext('2d');
                            var myChart = new Chart(ctx, {
                                type: 'line',
                                data: {
                                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                                    datasets: [
                                        @foreach ($monthlyRevenueArray as $branch_id => $revenues)
                                            {
                                                label: 'Monthly Revenue',
                                                data: [{{ implode(',', $revenues) }}],
                                                backgroundColor: '#ffbb00',
                                                borderColor: '#ffbb00',
                                                borderWidth: 2
                                            },
                                        @endforeach
                                    ]
                                },
                                options: {
                                    scales: {
                                        yAxes: [{
                                            ticks: {
                                                beginAtZero: true
                                            }
                                        }]
                                    }
                                }
                            });
                        </script>
                    </canvas>
                </div>

                <div class="dailyRevenueGraph">
                    <div class="info">
                        <p class="ttle">Daily Revenue</p>
                    </div>
                    <canvas id="dailyChart"></canvas>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            var ctx = document.getElementById('dailyChart').getContext('2d');
                            var dailyLabels = [];
                            var dailyDatasets = [];

                            @php
                                $daysInMonth = \Carbon\Carbon::create($year, $month)->daysInMonth;
                                $labels = range(1, $daysInMonth);
                                $datasets = [];
                                foreach ($dailyRevenueArray as $branch_id => $revenues) {
                                    $datasets[] = [
                                        'label' => 'Daily Revenue',
                                        'data' => $revenues,
                                        'backgroundColor' => '#ffbb00',
                                        'borderColor' => '#ffbb00',
                                        'borderWidth' => 2,
                                    ];
                                }
                            @endphp

                            dailyLabels = @json($labels);
                            dailyDatasets = @json($datasets);

                            var dailyChart = new Chart(ctx, {
                                type: 'line',
                                data: {
                                    labels: dailyLabels,
                                    datasets: dailyDatasets
                                },
                                options: {
                                    scales: {
                                        y: {
                                            beginAtZero: true
                                        }
                                    }
                                }
                            });
                        });
                    </script>
                </div>
            </div>
        @endif

        <div class="map">
            <h4>Branch Location</h4>
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d1400.1374189471333!2d74.3779472!3d31.4728824!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x391906746a44dfef%3A0x49d8b59f64029da6!2sDHA%20Phase%203%2C%20Lahore%2C%20Punjab%2C%20Pakistan!5e1!3m2!1sen!2s!4v1712561394745!5m2!1sen!2s"
                width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    </main>

    <script>
        var resizeCanvas = function() {
            var graphContainers = document.querySelectorAll(
                '.monthlyRevenuegraph, .totalRevenueGraph, .dailyRevenueGraph');
            graphContainers.forEach(function(container) {
                var canvas = container.querySelector('canvas');
                var width = container.offsetWidth;
                canvas.style.height = (width * 0.35) + 'px';
            });
        };
        window.addEventListener('resize', resizeCanvas);
        resizeCanvas();
    </script>
    
@endsection
