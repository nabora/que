<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offices</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>

<body>
    <nav class="navbar">
        <div>
            <li>
                <img class="img" src="/images/depedLogo.png" alt="Logo" width="90">
                <img class="img" src="/images/bg.png" alt="Logo" width="200" height="75">
            </li>
        </div>
    </nav>

            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show w-50" role="alert" id="success-alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert"  id="error-alert">
                {{ $errors->first() }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

    <div class="container custom-container vh-85 w-100 shadow-lg p-5 mt-5 bg-dark text-white">
        <h1 class="mb-4 select-title">SELECT OFFICE</h1>
        @foreach ($offices->chunk(3) as $chunk)
            <div class="row mb-3">
                @foreach ($chunk as $office)
                    <div class="col-md-4">
                        <button type="button" class="btn-office btn-primary w-100" data-bs-toggle="modal" data-bs-target="#officeModal{{ $office->id }}">
                            {{ $office->office_name }}
                        </button>
                    </div>
                @endforeach 
            </div>
        @endforeach    
    </div>

    @foreach ($offices as $office)
        <!-- Office Modal -->
        <div class="modal fade" id="officeModal{{ $office->id }}" role="dialog" tabindex="-1" aria-labelledby="ModalLabel{{ $office->id }}" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <form action="{{ route('store') }}" method="POST" id="submitForm{{ $office->id }}">
                        @csrf
                        <div class="modal-header">
                            <h3 class="modal-title" id="ModalLabel{{ $office->id }}">{{ $office->office_name }}</h3>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <h5>Select your Intention in the Office</h5>

                            <!-- Include office_id and office_name for processing -->
                            <input type="hidden" name="office_id" value="{{ $office->id }}">
                            <input type="hidden" name="office_name" value="{{ $office->office_name }}">
                            <input type="hidden" name="queue_number" id="queue_number{{ $office->id }}" value="">

                            @foreach ($office->services as $service)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="services[]" id="service{{ $service->id }}" value="{{ $service->service }}">
                                    <label class="form-check-label mb-2" for="service{{ $service->id }}">
                                        {{ $service->service }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-dismiss="modal" data-bs-target="#NextModal{{ $office->id }}">Next</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal 2 -->
        <div class="modal fade" id="NextModal{{ $office->id }}" tabindex="-1" aria-labelledby="NextModalLabel{{ $office->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <h3>Do you want to have another transaction?</h3>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" form="submitForm{{ $office->id }}" class="btn btn-secondary" data-bs-dismiss="modal">Yes</button>
                        <form action="{{ route('print') }}" method="POST">
                            @csrf
                                <!-- Include hidden inputs to carry over the data from the first form -->
                                <form id="printForm{{ $office->id }}" action="{{ route('print') }}" method="POST">
                                    <div id="selectedServicesContainer{{ $office->id }}"></div>
                            <button type="submit" class="btn btn-primary" onclick="prepareSelectedServices('{{ $office->id }}');">No</button>

                        </form>
                    </div> 
                </div>                  
            </div>
        </div>
    @endforeach

    <script>

        // Initialize counters for each office
        const counters = {};

        document.addEventListener('DOMContentLoaded', () => {
            // Get all office buttons
            const officeButtons = document.querySelectorAll('.btn-office');

            officeButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const officeId = button.getAttribute('data-bs-target').replace('#officeModal', '');
                    
                    // Initialize the counter for this office if it doesn't exist
                    if (!counters[officeId]) {
                        counters[officeId] = 1;
                    } else {
                        counters[officeId]++;
                    }

                    // Format the counter value to be 3 digits
                    const formattedCounter = counters[officeId].toString().padStart(3, '0');

                    // Set the transaction number value in the hidden input
                    const queueNumberInput = document.getElementById(`queue_number${officeId}`);
                    queueNumberInput.value = formattedCounter;
                });
            });
        });

        // Function to hide alerts after 5 seconds (5000 ms)
            function autoHideAlert(alertId) {
                const alertElement = document.getElementById(alertId);
                if (alertElement) {
                    setTimeout(function() {
                        const bsAlert = new bootstrap.Alert(alertElement);
                        bsAlert.close();
                    }, 5000); // 5 seconds
                }
            }

            // Auto-hide success and error alerts after 5 seconds
            autoHideAlert('success-alert');
            autoHideAlert('error-alert');


            function prepareSelectedServices(officeId) {
            // Get the container where we will add selected services
            const container = document.getElementById(`selectedServicesContainer${officeId}`);
            container.innerHTML = ''; // Clear any existing inputs

            // Get all the checkboxes for this office
            const checkboxes = document.querySelectorAll(`#officeModal${officeId} .form-check-input`);

            // Loop through each checkbox and add only the checked ones to the container
            checkboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'services[]';
                    input.value = checkbox.value;
                    container.appendChild(input);
                }
            });

            // Ensure the office_id is appended to the form
            const officeIdInput = document.createElement('input');
            officeIdInput.type = 'hidden';
            officeIdInput.name = 'office_id';
            officeIdInput.value = officeId;
            container.appendChild(officeIdInput);
        }

    </script>

</body>
</html>
