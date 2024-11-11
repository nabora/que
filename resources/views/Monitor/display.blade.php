<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DepEd Division Office Queueing System</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/display.css') }}">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f0f4f8;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .navbar {
            background-color: #1a237e;
            padding: 15px 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .navbar-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 12000px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .text-content h1 {
            color: #ffffff;
            font-size: 28px;
            margin: 0;
        }

        .marquee-container {
            background-color: rgba(26, 35, 126, 0.9);
            color: #ffffff;
            padding: 15px;
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            display: flex;
            align-items: center;
            font-size: 24px;
            box-shadow: 0 -2px 4px rgba(0,0,0,0.1);
        }

        .marquee-label {
            white-space: nowrap;
            padding-right: 30px;
            font-weight: bold;
            font-size: 28px;
        }

        .marquee {
            flex-grow: 1;
            overflow: hidden;
        }

        .marquee-content {
            display: inline-block;
            white-space: nowrap;
            animation: marquee 20s linear infinite;
            font-size: 30px;
        }

        @keyframes marquee {
            0% { transform: translateX(100%); }
            100% { transform: translateX(-100%); }
        }

        .fullscreen-button {
            position: absolute;
            top: 20px;
            right: 20px;
            background-color: #4caf50;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            font-size: 16px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .fullscreen-button:hover {
            background-color: #45a049;
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <div class="navbar-content">
            <img class="img logo" src="{{ asset('images/depedLogo.png') }}" alt="Logo" width="90">
            <div class="text-content">
                <h1>Queueing Management System</h1>
                <br>
                <p class="clock" id="clock"></p>
            </div>
            <img class="img background" onclick="toggleFullscreen()" src="{{ asset('images/bg.png') }}" alt="Background"
                width="200" height="75">
        </div>
    </nav>

    <div class="container">
        <div class="grid-container" id="queue-container">
            <!-- Queue cards will be populated here -->
        </div>
    </div>

    <!-- Updated marquee structure -->
    <div class="marquee-container">
        <div class="marquee-label">Latest Called:</div>
        <div class="marquee">
            <div class="marquee-content" id="absentMarquee">
                <!-- Not present transactions will be populated here -->
            </div>
        </div>
    </div>

    <!-- Add this new element for not called transactions -->

    <!-- Script Side -->
    <script>
        function updateClock() {
            const now = new Date();
            let hours = now.getHours();
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            let period = 'AM';

            if (hours >= 12) {
                period = 'PM';
                if (hours > 12) {
                    hours -= 12;
                }
            } else if (hours === 0) {
                hours = 12;
            }

            hours = String(hours).padStart(2, '0');

            const options = {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            const formattedDate = now.toLocaleDateString(undefined, options);

            document.getElementById('clock').textContent = `${formattedDate} ${hours}:${minutes}:${seconds} ${period}`;
        }

        setInterval(updateClock, 1000);
        updateClock(); // Initialize immediately

        function toggleFullscreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen();
                document.querySelector('.fullscreen-button').textContent = 'Exit Fullscreen';
            } else {
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
                    console.log('Fetched data:', data);

                    const container = document.getElementById('queue-container');
                    container.innerHTML = '';

                    data.offices.forEach(office => {
                        console.log('Processing office:', office);

                        // Get the current queue (status 'called')
                        const currentQueue = data.queues
                            .filter(queue => queue.office_id === office.id && queue.status === 'called')
                            .sort((a, b) => new Date(b.updated_at) - new Date(a.updated_at))[0]; // Most recent 'called' queue

                        // Get the next queues (status 'pending')
                        const nextQueues = data.queues
                            .filter(queue => queue.office_id === office.id && queue.status === 'pending')
                            .sort((a, b) => new Date(a.created_at) - new Date(b.created_at)) // Oldest 'pending' queues
                            .slice(0, 5);

                        const officeElement = document.createElement('div');
                        officeElement.className = 'queue-card';
                        officeElement.innerHTML = ` 
                        
                    <h2 style="text-align: center;">${office.office_name}</h2>

                    <h2 <span style="color: #00000;">Current Queue:</span> ${currentQueue ? currentQueue.queue_number : 'None'}</h2>
                    <p style="font-size: 25px">
                        <strong>Next Queue: ${nextQueues.length ? nextQueues.map(queue => queue.queue_number).join(', ') : 'None'}</strong>
                    </p>
                `;
                        container.appendChild(officeElement);
                    });

                    // Update the marquee with not present transactions
                    updateNotPresentMarquee(data.notPresentTransactions);
                })
                .catch(error => console.error('Error fetching queues:', error));
        }

        function updateNotPresentMarquee(notPresentTransactions) {
            console.log('Updating marquee with:', notPresentTransactions);
            const marquee = document.getElementById('absentMarquee');
            if (!marquee) {
                console.error('Marquee element not found!');
                return;
            }
            if (notPresentTransactions && notPresentTransactions.length > 0) {
                const content = notPresentTransactions.map(transaction => 
                    `${transaction.queue_number}`
                ).join(' | ');
                marquee.textContent = content;
            } else {
                marquee.textContent = "No transactions at the moment.";
            }
            console.log('Marquee updated:', marquee.textContent);
        }

        function updateAbsentMarquee(absentTransactions) {
            console.log('Updating marquee with:', absentTransactions);
            const marquee = document.getElementById('absentMarquee');
            if (!marquee) {
                console.error('Marquee element not found!');
                return;
            }
            if (absentTransactions && absentTransactions.length > 0) {
                const content = absentTransactions.map(transaction => 
                    `${transaction.queue_number} (${transaction.office_name}) - Absent`
                ).join(' | ');
                marquee.textContent = content;
            } else {
                marquee.textContent = "No absent transactions at the moment.";
            }
            console.log('Marquee updated:', marquee.textContent);
        }

        function updateNotCalledMarquee(notCalledTransactions) {
            const marquee = document.getElementById('notCalledMarquee');
            marquee.innerHTML = notCalledTransactions.map(transaction => 
                `${transaction.queue_number} (${transaction.office_name}) - ${transaction.status}`
            ).join(' &bull; ');
        }

        setInterval(fetchQueues, 1000); // Fetch data every 1 second
        fetchQueues(); // Initial fetch
    </script>
</body>

</html>
