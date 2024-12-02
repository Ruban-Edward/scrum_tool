document.addEventListener('DOMContentLoaded', function() {
    const datePicker = document.getElementById('date-picker');
    const today = new Date().toISOString().split('T')[0];
    datePicker.value = today;

    const feedbackLabel = document.getElementById('feedback-label');
    const textarea = document.getElementById('general');
    const typeError = document.getElementById('typeError');
    const generalError = document.getElementById('generalError');
    const voiceRecorderBtn = document.getElementById('voiceRecorderBtn');
    let recognition;

    document.querySelectorAll('input[name="feedback-type"]').forEach(radio => {
        radio.addEventListener('change', function() {
            typeError.style.display = 'none';
            if (this.value === 'pros') {
                feedbackLabel.innerHTML = '<h5 class="headerName"><i class="fas fa-thumbs-up"></i> Pros</h5>';
                textarea.placeholder = 'Enter your pros';
            } else if (this.value === 'cons') {
                feedbackLabel.innerHTML = '<h5 class="headerName"><i class="fas fa-exclamation-triangle"></i> Cons</h5>';
                textarea.placeholder = 'Enter your cons';
            } else if (this.value === 'lns') {
                feedbackLabel.innerHTML = '<h5 class="headerName"><i class="fas fa-envelope"></i> Suggestions</h5>';
                textarea.placeholder = 'Enter your suggestions';
            }
        });
    });

    document.getElementById('scrumRetrospectiveForm').addEventListener('submit', function(event) {
        let valid = true;

        const feedbackType = document.querySelector('input[name="feedback-type"]:checked');
        const generalValue = textarea.value.trim();

        if (!feedbackType) {
            typeError.style.display = 'block';
            valid = false;
        } else {
            typeError.style.display = 'none';
        }

        if (generalValue === '') {
            generalError.style.display = 'block';
            valid = false;
        } else {
            generalError.style.display = 'none';
        }

        if (!valid) {
            event.preventDefault();
        }
    });

    // Voice recorder functionality
    if ('webkitSpeechRecognition' in window) {
        recognition = new webkitSpeechRecognition();
        recognition.continuous = false;
        recognition.lang = 'en-US';

        recognition.onresult = function(event) {
            const transcript = event.results[0][0].transcript;
            textarea.value += transcript + ' ';
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