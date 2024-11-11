<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Deped Division Office Queueing System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f2f5;
            font-family: 'Roboto', sans-serif;
        }

        .navbar {
            background: linear-gradient(135deg, #1a237e, #3f51b5);
            padding: 1rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .navbar-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .text-content {
            text-align: center;
            color: white;
        }

        .logout-button {
            background-color: transparent;
            color: white;
            border: 2px solid white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            transition: all 0.3s ease;
        }

        .logout-button:hover {
            background-color: white;
            color: #1a237e;
            transform: scale(1.05);
        }

        .sidebar {
            background-color: white;
            height: calc(100vh - 100px);
            padding: 1rem;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            margin-top: 20px;
        }

        .main-content {
            padding: 2rem;
        }

        .card {
            border: none;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            margin-bottom: 1rem;
            border-radius: 15px;
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .queue-number {
            font-size: 5rem;
            font-weight: bold;
            color: #1a237e;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
        }

        .btn-action {
            width: 130px;
            margin: 0.5rem;
            border-radius: 25px;
            transition: all 0.3s ease;
        }

        .btn-action:hover {
            transform: scale(1.05);
        }

        #clock {
            font-size: 1.4rem;
            color: white;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
        }

        .scrollable-section {
            max-height: 300px;
            overflow-y: auto;
            border-radius: 10px;
            background-color: #f8f9fa;
            padding: 10px;
        }

        .list-group-item {
            border-radius: 8px;
            margin-bottom: 5px;
            transition: all 0.2s ease;
        }

        .list-group-item:hover {
            background-color: #e9ecef;
        }

        h2,
        h4,
        h5 {
            color: #1a237e;
        }

        .button-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <div class="navbar-content container-fluid">
            <img class="img logo" src="{{ asset('images/depedLogo.png') }}" alt="Logo" width="90">
            <div class="text-content">
                <h1>Queueing Management System</h1>
                <p>DepEd District of Bukidnon</p>
                <p class="clock" id="clock"></p>
            </div>
            <form id="logout-form" action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="logout-button">Logout</button>
            </form>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3 sidebar">
                <h4 class="mt-4 text-center">Office: {{ $user->name }}</h4>
                <hr>
                <div class="pending-section scrollable-section mt-4">
                    <h5 class="text-center">Pending Queue Numbers</h5>
                    <ul class="list-group">
                        @foreach ($pendingNumbers as $number)
                            <li class="list-group-item">{{ $number }}</li>
                        @endforeach
                    </ul>
                </div>
                <hr>
                <div class="absent-section scrollable-section mt-4">
                    <h5 class="text-center">Absent Queue Numbers</h5>
                    <ul class="list-group" id="absentList">
                        @foreach ($absentNumbers as $number)
                            <li class="list-group-item d-flex justify-content-between align-items-center"
                                data-queue="{{ $number }}">
                                {{ $number }}
                                <button class="btn btn-danger btn-sm remove-absent" data-queue="{{ $number }}"
                                    title="Remove">
                                    <i class="fas fa-times"></i>
                                </button>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="col-md-9 main-content">
                <h2>Queueing Dashboard</h2>
                <div class="card mt-4">
                    <div class="card-body text-center">
                        <h1 class="display-4">Queue Number: <span class="queue-number" id="queueNumber"></span></h1>
                    </div>
                </div>
                <div class="button-container mt-4 text-center">
                    <button class="btn btn-primary btn-action" id="nextQueueButton">Next Queue</button>
                    <button class="btn btn-success btn-action" id="callButton">Call</button>
                    <button class="btn btn-secondary btn-action" id="repeatButton">Repeat</button>
                    <button class="btn btn-danger btn-action" id="removeButton">Remove</button>
                    <button class="btn btn-warning btn-action" id="absentButton">Mark Absent</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let currentQueueNumber = null;
            let userOffice = "{{ $user->name }}";

            function playDingDong() {
                return new Promise((resolve) => {
                    const audio = new Audio('/sounds/ding.mp3'); // This is the correct path
                    audio.play();
                    // Wait for 5 seconds (5000 milliseconds) before resolving the promise
                    setTimeout(resolve, 6000);
                });
            }

            function updateQueueDisplay(queueNumber) {
                document.getElementById('queueNumber').textContent = queueNumber;
                currentQueueNumber = queueNumber;
            }

            async function speakQueueNumber(number, office) {
                await playDingDong(); // This will now wait for 5 seconds
                const utterance = new SpeechSynthesisUtterance(
                    `Calling ${number}. Please proceed to the ${office} window.`
                );
                utterance.lang = 'en-US';
                speechSynthesis.speak(utterance);
            }

            function handleNextQueue() {
                fetch('/next-queue', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            updateQueueDisplay(data.queueNumber);
                        } else {
                            alert(data.message);
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }

            function handleCallQueue() {
                if (!currentQueueNumber) {
                    alert('No active queue to call.');
                    return;
                }

                fetch('/call-queue', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        },
                        body: JSON.stringify({
                            queueNumber: currentQueueNumber
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            speakQueueNumber(currentQueueNumber, userOffice);
                        } else {
                            alert(data.message);
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }

            function handleRemoveQueue() {
                if (!currentQueueNumber) {
                    alert('No active queue to remove.');
                    return;
                }

                fetch('/remove-queue', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        },
                        body: JSON.stringify({
                            queueNumber: currentQueueNumber
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            updateQueueDisplay('');
                        } else {
                            alert(data.message);
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }

            function handleAbsentQueue() {
                if (!currentQueueNumber) {
                    alert('No queue to mark as absent.');
                    return;
                }

                fetch('/absent-queue', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        },
                        body: JSON.stringify({
                            queueNumber: currentQueueNumber
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Queue marked as absent successfully.');
                            location.reload(); // This will refresh the page
                        } else {
                            alert(data.message);
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }

            // Add event listeners for remove buttons
            document.querySelectorAll('.remove-absent').forEach(button => {
                button.addEventListener('click', function() {
                    const queueNumber = this.getAttribute('data-queue');
                    removeAbsentQueue(queueNumber, this);
                });
            });

            function removeAbsentQueue(queueNumber, buttonElement) {
                fetch('/remove-absent-queue', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        },
                        body: JSON.stringify({
                            queueNumber: queueNumber
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Remove the entire list item (parent of the button)
                            const listItem = buttonElement.closest('li');
                            if (listItem) {
                                listItem.remove();
                            }
                            console.log('Queue removed successfully');
                        } else {
                            alert(data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while removing the queue.');
                    });
            }

            document.getElementById('nextQueueButton').addEventListener('click', handleNextQueue);
            document.getElementById('callButton').addEventListener('click', handleCallQueue);
            document.getElementById('removeButton').addEventListener('click', handleRemoveQueue);
            document.getElementById('absentButton').addEventListener('click', handleAbsentQueue);
            document.getElementById('repeatButton').addEventListener('click', function() {
                if (currentQueueNumber) {
                    speakQueueNumber(currentQueueNumber, userOffice);
                } else {
                    alert('No active queue to repeat.');
                }
            });
        });
    </script>
</body>

</html>
