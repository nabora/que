<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Transactions</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Set the print size to fit thermal paper (e.g., 80mm width) */
        @media print {
            body {
                width: 80mm;
                font-family: Arial, sans-serif;
                font-size: 18px;
                margin-top: 0;
                padding: 0;
            }
            .container{
                margin-top: 0px;
                padding: 0px;
            }
            .text{
                font-size: 30px;
            }
            p {
                margin: 0 0 5px 0;
                line-height: 1.2;
            }
            hr {
                border: none;
                border-top: 1px dashed #000;
                margin: 5px 0;
            }

        }
    </style>
</head>
<body>
    <div class="container">
        @if($transactions->isEmpty())
            <p>No transactions found.</p>
        @else
            @foreach($transactions as $transaction)
                <h1 class="text-center fw-bold text">{{ $transaction->queue_number }}</h1>
                <p>{{ $transaction->office->office_name }}</p>
                <p><b>Services Selected:</b></p>
                <ul>
                    @foreach (json_decode($transaction->service) as $service)
                        <li>{{ $service }}</li>
                    @endforeach
                </ul>
                <hr>
            @endforeach
        @endif
    </div>
    <script>
        window.print();

        //  // After printing, redirect to the user page
         window.onafterprint = function() {
            window.location.href = "{{ route('user') }}";
         }
    </script>
</body>
</html>