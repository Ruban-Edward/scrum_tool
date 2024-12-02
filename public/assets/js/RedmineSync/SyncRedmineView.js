
if (typeof syncRedmineView !== 'undefined') {

    document.addEventListener('DOMContentLoaded', function () {
        $(document).ready(function () {
            $('#syncForm').on('submit', function (e) {
                e.preventDefault(); // Prevent the default form submission

                // Serialize the form data
                var formData = $(this).serialize();
                console.log(formData);

                // Show the loading screen
                showLoadingScreen();

                // Send the form data using AJAX
                $.ajax({
                    url: BASE_URL + 'syncing/' + 'syncall',
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function (response) {
                        console.log(response);
                        let successMessages = [];

                        if (response.members === true) {
                            successMessages.push('user sync');

                        }

                        if (response.tasks === true) {
                            successMessages.push('task sync');

                        }

                        if (response.product === true) {
                            successMessages.push('product sync');

                        }

                        if (response.users === true) {
                            successMessages.push('member sync');
                        }
                        if (response.customers == true) {
                            successMessages.push('customersync');
                        }

                        if (successMessages.length > 0) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: `Your ${successMessages.join(', ')} have been saved successfully.`,
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = window.location.href;
                                }
                            })

                        } else {

                            if (Object.keys(response).length === 0) {
                                Swal.fire({
                                    title: 'Action Required',
                                    icon: 'warning',
                                    text: 'Please click on the card to proceed.',
                                });

                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Failed!',
                                    text: 'An error occurred while updating your settings. Please try again.',
                                });
                            }
                        }
                    },
                    error: function (xhr, status, error) {
                        var errorMessage = xhr.responseJSON && xhr.responseJSON.error ? xhr.responseJSON.error : 'Sync Failed';
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: errorMessage,
                        });
                    },
                    complete: function () {
                        hideLoadingScreen();
                    }
                });

            });

            function showLoadingScreen() {
                $('#loading-screen').show();
                simulateProgress();
            }

            function hideLoadingScreen() {
                $('#loading-screen').hide();
            }

            function updateProgress(progress) {
                $('#progress-bar').css('width', progress + '%').attr('aria-valuenow', progress);
                $('#progress-text').text(progress + '%');
            }

            function simulateProgress() {
                let progress = 0;
                const interval = setInterval(function () {
                    progress += 10;
                    updateProgress(progress);

                    if (progress >= 90) {
                        clearInterval(interval);
                    }
                }, 300);
            }
        });
        document.getElementById('resetBtn').addEventListener('click', function () {
            document.getElementById('syncForm').reset();
            updateStatusText();
        });

        document.getElementById('selectAllBtn').addEventListener('click', function () {
            var checkboxes = document.querySelectorAll('#syncForm input[type="checkbox"]');
            checkboxes.forEach(function (checkbox) {
                checkbox.checked = true;
            });
            updateStatusText();
        });

        function updateStatusText() {
            var checkboxes = document.querySelectorAll('#syncForm input[type="checkbox"]');
            checkboxes.forEach(function (checkbox) {
                var statusText = checkbox.nextElementSibling.querySelector('.status-text');
                if (checkbox.checked) {
                    statusText.textContent = 'Ready to sync';
                } else {
                    statusText.textContent = 'Not synced';
                }
            });
        }

        document.querySelectorAll('.sync-checkbox').forEach(function (checkbox) {
            checkbox.addEventListener('change', updateStatusText);
        });

        updateStatusText();
    });

}
