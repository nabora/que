<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offices</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    
  </body>
</html>
</head>
<style>
    .btn-office{
        height: 80px;
        border-radius: 20px;
    }
    .custom-container{
        border-radius: 25px;
    }

</style>
<body>
    <nav class="navbar">
            <div>
                <li>
                    <img class="img" src="\images\depedLogo.png" alt="Logo" width="90">
                    <img class="img" src="\images\bg.png" alt="Logo" width="200" height="75">
                </li>
            </div>
        </nav>

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
    <!-- Modal -->
    <div class="modal fade" id="officeModal{{ $office->id }}" role="dialog" tabindex="-1" aria-labelledby="ModalLabel{{ $office->id }}" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <form action="{{ route('user') }}" method="POST" id="submitForm">
                    @csrf
                    <div class="modal-header">
                        <h3 class="modal-title" id="ModalLabel{{ $office->id }}">{{ $office->office_name }}</h3>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <h5>Select your Intention in the Office</h5>
                        <input type="hidden" name="office_id" value="{{ $office->office_name }}">
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
                        <button type="submit" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#NextModal{{ $office->id }}" data-bs-dismiss="modal">Next</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal 2 -->
    <div class="modal fade" id="NextModal{{ $office->id }}" tabindex="-1" aria-labelledby="NextModalLabel{{ $office->id }}" aria-hidden="true">
        <div class="modal modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h3>Do you want to have another Transaction?</h3>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Yes</button>
                    <button type="button" class="btn btn-primary">No</button>
                </div> 
            </div>                  
        </div>
    </div>
    <!-- Modal 2 -->
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
</script>



    <!-- <div class="container custom-container vh-85 w-100 shadow-lg p-5 mt-5 bg-dark text-white">
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

    @foreach ($offices as $office) -->
    <!-- Modal -->
    <!-- <div class="modal fade" id="officeModal{{ $office->id }}" role="dialog" tabindex="-1" aria-labelledby="ModalLabel{{ $office->id }}" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <form action="{{ route('user') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h3 class="modal-title" id="ModalLabel{{ $office->id }}">{{ $office->office_name }}</h3>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <h5>Select your Intention in the Office</h5>
                        <input type="hidden" name="office_name" value="{{ $office->office_name }}">
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
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#NextModal{{ $office->id }}">Next</button>
                    </div>
                </form>
            </div>
        </div>
    </div> -->

    <!-- Modal 2 -->
    <!-- <div class="modal fade" id="NextModal{{ $office->id }}" tabindex="-1" aria-labelledby="NextModalLabel{{ $office->id }}" aria-hidden="true">
            <div class="modal modal-dialog modal-sm">
                <div class="modal-content">
                <div class="modal-header">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <h3>Do you want to have another Transaction?</h3>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Yes</button>
                        <button type="button" class="btn btn-primary">No</button>
                    </div> 
                </div>                  
            </div>
        </div> -->
        <!-- @endforeach -->

</body>
</html>
