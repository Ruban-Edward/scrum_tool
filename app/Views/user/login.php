<!-- 
Author: Stervin Richard
Email: stervinrichard74@gmail.com
Created Date: 7 July 2024
Updated Date: 16 July 2024
-->
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta tags for character set and viewport -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Title of the page -->
    <title>Infiniti Scrum Master Portal</title>

    <!-- favicon for this page -->
    <link rel="shortcut icon" href="<?= ASSERT_PATH ?>support/compiled/svg/favicon.svg" type="image/x-icon">
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="<?= ASSERT_PATH ?>assets/css/bootstrap/css/bootstrap.min.css">
    
    <!-- Custom compiled CSS -->
    <link rel="stylesheet" href="<?= ASSERT_PATH ?>support/compiled/css/app.css">
    
    <!-- Login specific CSS -->
    <link href="<?= ASSERT_PATH ?>assets/css/login/login.css" rel="stylesheet">
    
    <!-- jQuery library -->
    <script src="<?= ASSERT_PATH ?>support/extensions/jquery/jquery.min.js"></script>
</head>
<body>
    <!-- Header section with Infiniti logo -->
    <header>
        <div class="cls-login-logo">
        <img src="<?= ASSERT_PATH ?>assets/images/infiniti_logo.png" alt="Infiniti Logo">
        </div>
    </header>
    
    <!-- Main content area with login form -->
    <div class="row cls-login-form">
        <div class="cls-left col-md-7">
            <!-- Scrum process image with background color overlay -->
            <div class="cls-scrum-img">
                <img src="<?= ASSERT_PATH ?>assets/images/login/scrum_img.jpeg" alt="Scrum Process">
                <div class="cls-background-col"></div>
            </div>
        </div>
        <div class="cls-right col-md-5">
            <div class="cls-form">
                <!-- Login header message -->
                <h2 id="login-or-signup-msg">Get started</h2>
                <p>Welcome back to scrum master portal</p>
                
                <!-- Error message display for wrong username/password -->
                <div class="cls-wrong-username-password">
                    <?php if(session()->has('error')):?>
                        <?= session()->getFlashdata('error') ?>
                    <?php endif ?>
                </div>

                <!-- Login form with Parsley validation -->
                <form id="loginForm" class="needs-validation" method="post" action="login" novalidate>
                    <!-- Username input field -->
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" class="form-control" 
                            placeholder="Enter your username" name="username" autocomplete="off" required/>
                        <div class="valid-feedback">Looks good!</div>
                        <div class="invalid-feedback">Username is required</div>
                        <div class="cls-error-msg">
                            <?php if(session()->has('validation')):?>
                            <?= session()->getFlashdata('validation')['username'] ?? "" ?>
                            <?php endif ?>
                        </div>
                    </div>
                    
                    <!-- Password input field -->
                    <div class="form-group mandatory">
                        <label for="password">Password</label>
                        <div class="cls-password">
                            <input type="password" id="password" class="form-control"
                                placeholder="Enter your password" name="password" autocomplete="off" required />
                            <i class="bi bi-eye-slash" id="togglePassword"></i>
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Password is required</div>
                        </div>
                        <div class="cls-error-msg">
                            <?php if(session()->has('validation')):?>
                            <?= session()->getFlashdata('validation')['password'] ?? "" ?>
                            <?php endif ?>
                        </div>
                    </div>
                    
                    <!-- Submit button -->
                    <div class="row">
                        <button type="submit" class="btn btn-primary me-1 mb-1">Login</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Footer section -->
    <footer>
        <p>Powered by Infiniti Software Solutions | All rights reserved <?= date('Y',strtotime('now'))?></p>
    </footer>
    
    <!-- Custom JS for login page -->
    <script src="<?= ASSERT_PATH ?>assets/js/login/login.js"></script>
</body>
</html>


