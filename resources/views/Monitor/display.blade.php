<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Queue Display - DepEd District of Bukidnon</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/display.css') }}">
    <style>
        .fullscreen-button {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            font-size: 16px;
            border-radius: 5px;
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <div class="navbar-content">
            <img class="img logo" src="{{ asset('images/depedLogo.png') }}" alt="Logo" width="90">
            <div class="text-content">
                <h1>Queueing Management System</h1>
                <p style="text-align: center">DepEd District of Bukidnon</p>
                <p class="clock" id="clock"></p>
            </div>
            <img class="img background" onclick="toggleFullscreen()" src="{{ asset('images/bg.png') }}" alt="Background" width="200" height="75">
        </div>
    </nav>

    <div class="container">
        <div class="grid-container" id="queue-container">
            <!-- Queue cards will be populated here -->
        </div>
    </div>

    <!-- Script Side -->
    <script>
        function updateClock() {
            const now = new Date();
            let hours = now.getHours();
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            let period = 'AM';

            // Convert to 12-hour format
            if (hours >= 12) {
                period = 'PM';
                if (hours > 12) {
                    hours -= 12;
                }
            } else if (hours === 0) {
                hours = 12; // Midnight case
            }

            hours = String(hours).padStart(2, '0');

            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            const formattedDate = now.toLocaleDateString(undefined, options);

            document.getElementById('clock').textContent = `${formattedDate} ${hours}:${minutes}:${seconds} ${period}`;
        }

        setInterval(updateClock, 1000);
        updateClock(); // Initialize immediately

        function toggleFullscreen() {
            if (!document.fullscreenElement) {
                // Enter fullscreen mode
                document.documentElement.requestFullscreen();
                document.querySelector('.fullscreen-button').textContent = 'Exit Fullscreen';
            } else {
                // Exit fullscreen mode
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                }
                document.querySelector('.fullscreen-button').textContent = 'Enter Fullscreen';
            }
        }

        function fetchQueues() {
            fetch('/fetch-queues')
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('queue-container');
                    container.innerHTML = ''; // Clear the existing content

                    data.offices.forEach(office => {
                        const currentQueue = data.queues.find(queue => queue.office_id === office.id);
                        const nextQueues = data.queues.filter(queue => queue.office_id === office.id).slice(1, 6);

                        const officeElement = document.createElement('div');
                        officeElement.className = 'queue-card';
                        officeElement.innerHTML = `
                            <h2 style="text-align: center">${office.office_name}</h2>
                            <br><br>
                            <h2><strong>Current Queue: ${currentQueue ? currentQueue.queue_number : 'None'}</strong></h2>
                            <p>
                                <strong>Next Queue: ${nextQueues.length ? nextQueues.map(queue => queue.queue_number).join(', ') : 'None'}</strong>
                            </p>
                        `;
                        container.appendChild(officeElement);
                    });
                })
                .catch(error => console.error('Error fetching queues:', error));
        }

        setInterval(fetchQueues, 10000); // Fetch data every 10 seconds
        fetchQueues(); // Initial fetch
    </script>
</body>

</html>