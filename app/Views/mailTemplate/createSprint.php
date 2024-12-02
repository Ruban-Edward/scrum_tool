<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f0f4f8;
        }
        .container {
            background-color: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        h1 {
            color: #3949ab;
            text-align: center;
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
        }
        h5{
            margin-left: 30px;
        }
        p {
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }
        .animated-image {
            /* width: 200px;
            height: 100px;
            background-image: url('https://cdn.pixabay.com/photo/2017/02/03/09/46/team-2034858_1280.png'); */
            background-size: cover;
            animation: pulse 2s infinite;
            margin: 0 auto 0.5rem;
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 1.5rem;
            margin-bottom: 1.5rem;
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border: 1px solid #e0e0e0;
        }
        th {
            background-color: #3949ab;
            color: white;
            font-size: 0.9rem;
            font-weight: 700;
        }
        td {
            font-size: 0.9rem;
            background-color: #f8f9fa;
        }
        tr:nth-child(even) td {
            background-color: #e8eaf6;
        }
        .signature {
            text-align: right;
            margin-top: 1.5rem;
            color: #3949ab;
            font-weight: 700;
        }
        .meeting-image {
            width: 100%;
            max-height: 200px;
            object-fit: cover;
            border-radius: 10px;
            margin-top: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .btn {
            display: inline-block;
            background-color: #3949ab;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 0.9rem;
            transition: background-color 0.3s ease;
        }
        .btn:hover {
            background-color: #303f9f;
        }
    </style>
</head>
<body>
    <div class="container">

        <h1 >Sprint Creation</h1>
        
        <!-- <div class="animated-image">
        </div> -->
        
        <h4>Dear Team,</h4>
        <h5>We are pleased to inform you that a new sprint has been created for the product {product_name}. The sprint is scheduled to start on {start_date}. Please find the relevant details below for your reference.</h5>        
        <table>
            <tr>
                <th>Product Name</th>
                <td> {product_name}</td>
            </tr>
            <tr>
                <th>Customer</th>
                <td>{customer_name}</td>
            </tr>
            <tr>
                <th>Sprint Duration</th>
                <td>{sprint_duration}</td>
            </tr>
            <tr>
                <th>Sprint Version</th>
                <td>{sprint_version}</td>
            </tr>
            <tr>
                <th>Start Date</th>
                <td>{start_date}</td>
            </tr>
            <tr>
                <th>End Date</th>
                <td>{end_date}</td>
            </tr>
            <tr>
                <th>Sprint Goal</th>
                <td>{sprint_goal}</td>
            </tr>
        </table>
        <!-- <p>We look forward to your attendance and valuable input.</p> -->
        
        <!-- <div class="signature">
            Best regards,<br>my Team
        </div> -->
    </div>
</body>
</html>
