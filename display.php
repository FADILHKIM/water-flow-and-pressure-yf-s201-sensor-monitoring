<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Combined Web</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 30px;
            background-color: #b3d9ff;
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        #title-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #title {
            font-size: 24px;
            color: #333;
        }

        #left-container,
        #right-container {
            flex: 1;
            margin: 10px;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 90%;
            max-width: 400px;
            height: 550px;
        }

        #left-container {
            background-color: #ffffff; /* White */
        }

        #right-container {
            background-color: #f5f5f5; /* Light Gray */
        }

        h1 {
            color: #333333; /* Dark Gray */
            padding: 15px;
            background-color: #87cefa; /* Light Sky Blue */
            border-radius: 10px;
            display: inline-block;
            font-size: 24px;
        }

        h1 img {
            margin-left: 10px;
            width: 40px;
            height: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            background-color: #ffffff; /* White */
        }

        th, td {
            border: 1px solid #dddddd; /* Light Gray */
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #4CAF50; /* Green */
            color: #ffffff; /* White */
        }

        tr:nth-child(even) {
            background-color: #f9f9f9; /* Light Gray */
        }

        tr:hover {
            background-color: #f5f5f5; /* Light Gray */
            cursor: pointer;
        }

        #water-level {
            width: 200px;
            height: 300px;
            position: relative;
            margin: 20px auto;
            border: 2px solid #3498db;
            overflow: hidden;
        }

        #water-level .water {
            background-color: #3498db;
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            transition: height 1s ease;
        }

        #percentage,
        #height,
        #datetime {
            font-size: 16px;
            margin-top: 10px;
            color: #333;
        }

        #container {
            background-color: rgba(255, 255, 255, 0.7); /* 70% transparent white background */
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 90%; /* Set the initial width to 100% */
            max-width: 400px; /* Set the maximum width as needed */
            margin: 0 auto; /* Center the container */
            height: 550px; /* Set the initial height as needed */
        }

        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .page-link {
            padding: 10px;
            margin: 0 5px;
            background-color: #4CAF50;
            color: #fff;
            text-decoration: none;
            border: 1px solid #4CAF50;
            border-radius: 5px;
        }

        .page-link:hover {
            background-color: #45a049;
        }

        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 600px;
            height: 600px;
            background: transparent; /* Fix the missing semicolon and set it to transparent */
            background-size: contain;
            opacity: 0.4;
            z-index: -1;
        }
    </style>
</head>
<body>

<!-- Title Container -->
<div id="title-container">
    <div id="title">MONITORING AIR</div>
</div>

<!-- Left Container -->
<div id="left-container">
    <h1>Monitoring Data PDAM Surakarta <img src="pdam.png" alt="PDAM Logo"></h1>
    <div class="watermark"></div>

    <?php
    // Include the PHP code from the first block here
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "pdam";
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Pagination
    $page = isset($_GET['page']) ? $_GET['page'] : 1;
    $rowsPerPage = 17;
    $offset = ($page - 1) * $rowsPerPage;

    $sql = "SELECT * FROM monitoring LIMIT $offset, $rowsPerPage";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<table>";
        echo "<tr><th>id</th><th>Tinggi Air</th><th>Lokasi</th><th>(Tanggal/Jam)</th></tr>";

        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row["id"] . "</td>";
            echo "<td>" . $row["presentase"], " %". "</td>";
            echo "<td>" . $row["tinggi"]," cm". "</td>";
            echo "<td>" . $row["timestamp"] . "</td>";
            echo "</tr>";
        }

        echo "</table>";

        // Pagination links
        $sqlCount = "SELECT COUNT(id) AS total FROM monitoring";
        $resultCount = $conn->query($sqlCount);
        $rowCount = $resultCount->fetch_assoc()['total'];
        $totalPages = ceil($rowCount / $rowsPerPage);

        echo "<div class='pagination'>";
        for ($i = 1; $i <= $totalPages; $i++) {
            echo "<a class='page-link' href='?page=$i'>$i</a>";
        }
        echo "</div>";
    } else {
        echo "No data found";
    }

    $conn->close();
    ?>
</div>

<!-- Right Container -->
<div id="right-container">
    <div id="container">
        <h1>Level Air</h1>
        <div id="water-level">
            <div class="water" id="water"></div>
        </div>
        <div id="percentage">0%</div>
        <div id="height">0 cm</div>
        <div id="datetime"></div>
    </div> 

    <script>
        // Include the JavaScript code from the second block here
        function updateWaterLevel(height, percentage) {
            const waterHeight = (height / 300) * 100;
            document.getElementById('water').style.height = waterHeight + '%';
            document.getElementById('percentage').innerText = percentage + '%';
            document.getElementById('height').innerText = height + ' cm';
        }

        function updateDateTime() {
            const options = { timeZone: 'Asia/Jakarta' };
            const dateTimeString = new Date().toLocaleString('en-US', options);
            document.getElementById('datetime').innerText = dateTimeString;
        }
        {
            updateWaterLevel(tinggi, presentase);
            updateDateTime();
        }
    </script>
</div>

</body>
</html>
