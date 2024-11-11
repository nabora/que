<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">


   
    <style>
        body {
            display: flex;
            min-height: 100vh;
            overflow: hidden;
            font-family:system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
        }
        .main-content {
            width: 80%;
            flex-grow: 1;
            padding: 30px;
            background-color: #f8f9fa;
            overflow-y: auto;
            max-height: 100vh;
        }
        .service-list {
            display: none;
            
        }
        .card-title{
            font-size: 18px;
        }
        .card-text{
            font-size: 30px;
        }
        
        .service-navigation {
        display: flex;
        justify-content: space-between;
        width: 100%;
        margin-bottom: 20px;
        }

        .nav-btn {
        background-color: none;
        border: none;
        font-size: 20px;
        cursor: pointer;
        outline: none;
        }

        .nav-btn:focus {
        outline: none;
        }

        .service-cards {
        display: flex;
        flex-wrap: nowrap;
        overflow-x: auto;  /* Enable horizontal scrolling */
        scroll-behavior: smooth;  /* Smooth scrolling */
        }

        .service-card {
        min-width: 200px;
        margin-right: 10px;
        flex-shrink: 0;
        background-color: #fff;
        border-radius: 5px;
    }

    .card {
        transition: transform 0.2s;
    }
    .card:hover {
        transform: scale(1.05);
    }
    .text-primary {
        color: #007bff !important;
    }

    </style>
</head>
<body>

<!-- Main Content -->
<div class="main-content">
    <h2 class="text-center mb-4">Super Admin Dashboard</h2>
    
    <div class="row">
        @foreach($offices as $office)
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm border-primary">
                    <div class="card-body">
                        <h5 class="card-title text-primary">{{ $office->office_name }}</h5>
                        <p class="card-text">Total Transactions: <strong>{{ $office->transactions_count }}</strong></p>
                        <a href="{{ route('office.details', $office->id) }}" class="btn btn-primary">View Details</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

</div>


<script>
    const ctx = document.getElementById('transactionChart').getContext('2d');
    const colors = [
        'rgba(75, 192, 192, 0.2)',
        'rgba(255, 99, 132, 0.2)',
        'rgba(54, 162, 235, 0.2)',
        'rgba(255, 206, 86, 0.2)',
        'rgba(153, 102, 255, 0.2)',
        'rgba(255, 159, 64, 0.2)',
    ];

    // Prepare data for total transactions per office
    const officeLabels = @json(array_keys($totalTransactionsPerOffice));
    const officeData = @json(array_values($totalTransactionsPerOffice));

    let transactionChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: officeLabels,
            datasets: [{
                label: 'Total Transactions per Office',
                data: officeData,
                backgroundColor: colors.slice(0, officeData.length),
                borderColor: colors.slice(0, officeData.length).map(color => color.replace('0.2', '1')),
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
</script>
</body>
</html>