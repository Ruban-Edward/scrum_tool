<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>401 - No Authorization</title>
    <style>
        body {
            font-family: Nunito, sans-serif;
            background-color: #f8f4f8;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0 auto;
        }
        .container {
            text-align: center;
            max-width: 550px;
        }
        .error-code {
            width: 500px;
        }

        .error-code img{
            width: 100%;
        }
        h1 {
            color: #3949AB;
            font-size: 30px;
            margin-bottom: 10px;
        }
        p {
            color: #a495a4;
            margin-bottom: 20px;
        }
        .button {
            background-color: #3949AB;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-weight: 800;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="error-code">
            <img src="<?= base_url().'support/static/images/bg/401.png' ?>" alt="401 Error">
        </div>
        <h1>No authorization found.</h1>
        <p>This page is not publically available.</p>
        <a href="javascript:history.back()" class="button">RETURN TO PREVIOUS PAGE</a>
    </div>
</body>
</html> 