document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('scrumDiaryForm');
    const generalTextarea = document.getElementById('general');
    const generalError = document.getElementById('generalError');
    const radioError = document.getElementById('radioError');
    const challengesRadios = document.querySelectorAll('input[name="challenges"]');
    const formGroupHeader = document.getElementById('formGroupHeader');
    const datePicker = document.getElementById('date-picker');
    const voiceRecorderBtn = document.getElementById('voiceRecorderBtn');
    let recognition;

    // Set today's date in the date picker
    const today = new Date().toISOString().split('T')[0];
    datePicker.value = today;

    // Event listener for radio buttons
    challengesRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            radioError.style.display = 'none';
            if (radio.checked && radio.value === 'Y') {
                formGroupHeader.innerHTML = ' <h5 class="headerName"><i class="fas fa-exclamation-triangle"></i> Challenge</h5>';
                generalTextarea.placeholder = 'Your challenges';
            } else if (radio.checked && radio.value === 'N') {
                formGroupHeader.innerHTML = ' <h5 class="headerName"><i class="fas fa-clipboard"></i> General</h5>';
                generalTextarea.placeholder = 'General comments';
            }
        });
    });

    form.addEventListener('submit', function(e) {
        let isValid = true;

        // Validate the radio buttons
        const selectedRadio = document.querySelector('input[name="challenges"]:checked');
        if (!selectedRadio) {
            radioError.style.display = 'block';
            isValid = false;
        } else {
            radioError.style.display = 'none';
        }

        // Validate the general textarea
        if (generalTextarea.value.trim() === '') {
            generalError.style.display = 'block';
            generalTextarea.focus();
            isValid = false;
        } else {
            generalError.style.display = 'none';
        }

        // Prevent form submission if any field is invalid
        if (!isValid) {
            e.preventDefault();
            return;
        }
    });

    // Voice recorder functionality
    if ('webkitSpeechRecognition' in window) {
        recognition = new webkitSpeechRecognition();
        recognition.continuous = false;
        recognition.lang = 'en-US';

        recognition.onresult = function(event) {
            const transcript = event.results[0][0].transcript;
            generalTextarea.value += transcript + ' ';
        };

        recognition.onerror = function(event) {
            console.error('Speech recognition error:', event.error);
        };

        voiceRecorderBtn.addEventListener('click', function() {
            if (recognition.isStarted) {
                recognition.stop();
                voiceRecorderBtn.innerHTML = '<i class="fas fa-microphone"></i>';
                voiceRecorderBtn.classList.remove('recording');
            } else {
                recognition.start();
                voiceRecorderBtn.innerHTML = '<i class="fas fa-stop"></i>';
                voiceRecorderBtn.classList.add('recording');
            }
        });

        recognition.onend = function() {
            voiceRecorderBtn.innerHTML = '<i class="fas fa-microphone"></i>';
            voiceRecorderBtn.classList.remove('recording');
            recognition.isStarted = false;
        };
    } else {
        voiceRecorderBtn.style.display = 'none';
        console.log('Web Speech API is not supported in this browser.');
    }
});