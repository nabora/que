document.addEventListener('DOMContentLoaded', () => {
    // Function to speak the queue number
    function speakQueueNumber(number) {
        const utterance = new SpeechSynthesisUtterance(`Calling ${number}. Proceed to the ASDS Window.`);
        utterance.lang = 'en-US'; // Set language to English
        speechSynthesis.speak(utterance);
    }

    // Function to update the queue number on the page
    function updateQueueNumber(queueNumber) {
        document.getElementById('queueNumber').textContent = queueNumber;
    }

    // Helper function to handle fetch requests
    function handleFetch(route, callback) {
        fetch(route, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
            .then(response => response.json())
            .then(data => {
                console.log(`Response from ${route}:`, data);
                callback(data);
            })
            .catch(error => console.error('Error:', error));
    }

    // Event listener for the Next Queue button
    
});