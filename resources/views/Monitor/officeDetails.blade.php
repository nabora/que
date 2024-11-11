<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $office->office_name }} - Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            margin-bottom: 20px;
        }
        .service-item {
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .service-item:hover {
            background-color: #e2e6ea;
        }
        .chart-container {
            position: relative;
            margin: auto;
            max-width: 600px;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">{{ $office->office_name }} - Transaction Details</h2>

    <!-- Back Button -->
    <div class="mb-4">
        <a href="{{ route('superDashboard') }}" class="btn btn-secondary">Back to Dashboard</a>
    </div>

    <form method="GET" class="mb-4">
        <div class="input-group">
            <label for="date" class="form-label me-2">Select Month:</label>
            <input type="month" id="date" name="date" value="{{ $selectedDate }}" class="form-control" required>
            <button type="submit" class="btn btn-primary">Search</button>
        </div>
    </form>

    <h4 class="mt-4">Services Offered</h4>
    <ul class="list-group mb-4">
        @if($office->services->isEmpty())
            <li class="list-group-item">No services available.</li>
        @else
            @foreach($office->services as $service)
                <li class="list-group-item service-item" onclick="updateChart('{{ $service->service }}')">{{ $service->service }}</li>
            @endforeach
        @endif
    </ul>

    <div class="chart-container">
        <canvas id="transactionChart" width="400" height="200"></canvas>
    </div>

    <h4 class="mt-4">Transactions for {{ $selectedDate }}</h4>
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered mt-2">
                <thead>
                    <tr>
                        <th>Service</th>
                        <th>Transaction Count</th>
                    </tr>
                </thead>
                <tbody>
                    @if($transactions->isEmpty())
                        <tr>
                            <td colspan="2" class="text-center">No transactions available.</td>
                        </tr>
                    @else
                        @foreach($transactions as $transaction)
                            <tr>
                                <td>{{ $transaction->service }}</td> <!-- This will now display as a string -->
                                <td>{{ $transaction->count }}</td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    const ctx = document.getElementById('transactionChart').getContext('2d');
    
    // Define an array of colors
    const colors = [
        'rgba(75, 192, 192, 0.2)',
        'rgba(255, 99, 132, 0.2)',
        'rgba(54, 162, 235, 0.2)',
        'rgba(255, 206, 86, 0.2)',
        'rgba(153, 102, 255, 0.2)',
        'rgba(255, 159, 64, 0.2)',
        'rgba(201, 203, 207, 0.2)',
        'rgba(255, 99, 132, 0.2)',
        'rgba(75, 192, 192, 0.2)',
        'rgba(54, 162, 235, 0.2)',
    ];

    let transactionChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($transactions->pluck('service')) !!},
            datasets: [{
                label: 'Transaction Count',
                data: {!! json_encode($transactions->pluck('count')) !!},
                backgroundColor: colors.slice(0, {!! json_encode($transactions->count()) !!}), // Use colors based on the number of transactions
                borderColor: colors.slice(0, {!! json_encode($transactions->count()) !!}).map(color => color.replace('0.2', '1')), // Make borders solid
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    function updateChart(service) {
        const filteredTransactions = {!! json_encode($transactions) !!}.filter(transaction => transaction.service === service);
        
        transactionChart.data.labels = filteredTransactions.map(t => t.service);
        transactionChart.data.datasets[0].data = filteredTransactions.map(t => t.count);
        transactionChart.update();
    }
</script>
</body>
</html>
