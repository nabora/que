<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DepEd Division Office Queueing System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-image: url('/images/bgImg.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            min-height: 100vh;
            touch-action: manipulation;
        }

        .navbar {
            background: linear-gradient(135deg, #ffffff, #4159e2);
            padding: 1rem;
        }

        .custom-container {
            background-color: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            margin-top: 2rem;
        }

        .select-title {
            color: #1a237e;
            font-weight: bold;
            font-size: 3rem;
            margin-bottom: 1.5rem;
        }

        .btn-office {
            background-color: #1a237e;
            color: white;
            border: none;
            padding: 20px 10px;
            margin: 15px 0;
            transition: all 0.3s ease;
            font-size: 1.3rem;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            height: 100px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            width: 100%;
            white-space: normal;
        }

        .btn-office:hover, .btn-office:focus {
            background-color: #3949ab;
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        }

        .modal-content {
            border-radius: 20px;
        }

        .modal-header {
            background-color: #1a237e;
            color: white;
            border-top-left-radius: 20px;
            border-top-right-radius: 20px;
            padding: 1.5rem;
        }

        .modal-title {
            font-size: 2rem;
        }

        .form-check {
            margin-bottom: 1rem;
        }

        .form-check-label {
            font-size: 1.2rem;
        }

        .form-check-input {
            width: 1.5em;
            height: 1.5em;
            margin-top: 0.25em;
        }

        .btn {
            font-size: 1.2rem;
            padding: 10px 20px;
            border-radius: 10px;
        }

        .alert {
            font-size: 1.2rem;
            border-radius: 10px;
        }

        .row {
            display: flex;
            flex-wrap: wrap;
        }

        .col-md-4 {
            flex: 0 0 33.333333%;
            max-width: 33.333333%;
            padding: 10px;
        }

        @media (max-width: 768px) {
            .btn-office {
                font-size: 1.1rem;
                height: 80px;
            }

            .col-md-4 {
                flex: 0 0 50%;
                max-width: 50%;
            }
        }

        @media (max-width: 576px) {
            .col-md-4 {
                flex: 0 0 100%;
                max-width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="overlay">
        <nav class="navbar">
            <div class="container">
                <a class="navbar-brand" href="#">
                    <img class="img" src="{{ asset('images/depedLogo.png') }}" alt="Logo" width="90">
                    <img class="img" src="{{ asset('images/bg.png') }}" alt="Logo" width="200" height="75">
                </a>
            </div>
        </nav>

        <div class="container mt-4">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert" id="success-alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert" id="error-alert">
                    {{ $errors->first() }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="custom-container p-5 mt-5">
                <h1 class="mb-4 select-title text-center">SELECT OFFICE</h1>
                <div class="row">
                    @foreach ($offices as $office)
                        <div class="col-md-4 mb-3">
                            <button type="button" class="btn btn-office" data-bs-toggle="modal"
                                data-bs-target="#officeModal{{ $office->id }}">
                                <i class="fas fa-building me-2"></i>{{ $office->office_name }}
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        @foreach ($offices as $office)
            <!-- Office Modal -->
            <div class="modal fade" id="officeModal{{ $office->id }}" tabindex="-1"
                aria-labelledby="ModalLabel{{ $office->id }}" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <form action="{{ route('store') }}" method="POST" id="submitForm{{ $office->id }}">
                            @csrf
                            <div class="modal-header">
                                <h3 class="modal-title" id="ModalLabel{{ $office->id }}">{{ $office->office_name }}
                                </h3>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <h5 class="mb-4">Select your Intention in the Office</h5>

                                <input type="hidden" name="office_id" value="{{ $office->id }}">
                                <input type="hidden" name="office_name" value="{{ $office->office_name }}">
                                <input type="hidden" name="queue_number" id="queue_number{{ $office->id }}"
                                    value="">

                                @foreach ($office->services as $service)
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" name="services[]"
                                            id="service{{ $service->id }}" value="{{ $service->service }}">
                                        <label class="form-check-label" for="service{{ $service->id }}">
                                            {{ $service->service }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-dismiss="modal"
                                    data-bs-target="#NextModal{{ $office->id }}">Next</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modal 2 -->
            <div class="modal fade" id="NextModal{{ $office->id }}" tabindex="-1"
                aria-labelledby="NextModalLabel{{ $office->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="NextModalLabel{{ $office->id }}">Confirm Transaction</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <h3>Do you want to have another transaction?</h3>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" form="submitForm{{ $office->id }}"
                                class="btn btn-secondary">Yes</button>
                            <form action="{{ route('print') }}" method="POST" id="printForm{{ $office->id }}">
                                @csrf
                                <div id="selectedServicesContainer{{ $office->id }}"></div>
                                <button type="submit" class="btn btn-primary"
                                    onclick="prepareSelectedServices('{{ $office->id }}');">No</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Your existing JavaScript code here (unchanged)
        const counters = {};

        document.addEventListener('DOMContentLoaded', () => {
            const officeButtons = document.querySelectorAll('.btn-office');

            officeButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const officeId = button.getAttribute('data-bs-target').replace('#officeModal',
                        '');

                    if (!counters[officeId]) {
                        counters[officeId] = 1;
                    } else {
                        counters[officeId]++;
                    }

                    const formattedCounter = counters[officeId].toString().padStart(3, '0');

                    const queueNumberInput = document.getElementById(`queue_number${officeId}`);
                    queueNumberInput.value = formattedCounter;
                });
            });
        });

        function autoHideAlert(alertId) {
            const alertElement = document.getElementById(alertId);
            if (alertElement) {
                setTimeout(function() {
                    const bsAlert = new bootstrap.Alert(alertElement);
                    bsAlert.close();
                }, 5000);
            }
        }

        autoHideAlert('success-alert');
        autoHideAlert('error-alert');

        function prepareSelectedServices(officeId) {
            const container = document.getElementById(`selectedServicesContainer${officeId}`);
            container.innerHTML = '';

            const checkboxes = document.querySelectorAll(`#officeModal${officeId} .form-check-input`);

            checkboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'services[]';
                    input.value = checkbox.value;
                    container.appendChild(input);
                }
            });

            const officeIdInput = document.createElement('input');
            officeIdInput.type = 'hidden';
            officeIdInput.name = 'office_id';
            officeIdInput.value = officeId;
            container.appendChild(officeIdInput);
        }
    </script>
</body>

</html>
