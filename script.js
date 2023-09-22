const englishText = document.getElementById('englishText');
        const startButton = document.getElementById('click_to_record');
        let recognition = null;

        startButton.addEventListener('click', () => {
            if (recognition && recognition.running) {
                recognition.stop();
                startButton.textContent = 'Start Listening';
            } else {
                recognition = new (window.SpeechRecognition || window.webkitSpeechRecognition)();
                recognition.interimResults = true;

                recognition.addEventListener('result', e => {
                    const transcript = Array.from(e.results)
                        .map(result => result[0])
                        .map(result => result.transcript)
                        .join(' ');

                    englishText.value = transcript; // Use the 'value' property to set the textarea content
                    console.log(transcript);
                });

                recognition.addEventListener('end', () => {
                    startButton.textContent = 'Start Listening';
                });

                recognition.start();
                startButton.textContent = 'Stop Listening';
            }
        });