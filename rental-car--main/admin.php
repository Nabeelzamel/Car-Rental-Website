<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Output Arrays</title>
    <style>
        body {
            background-image: url('background.jpg'); /* Replace with your image path */
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
            color: white;
        }

        table {
            border-collapse: collapse;
            width: 80%;
            margin: 20px auto;
            background-color: rgba(255, 0, 0, 0.7); /* Red background with some transparency */
        }

        th, td {
            border: 1px solid white;
            text-align: left;
            padding: 10px;
        }

        th {
            background-color: rgba(255, 0, 0, 0.9); /* Darker red for header */
        }

        h2 {
            text-align: center;
        }
    </style>
</head>
<body>

<?php
include("connect.php");
$flag = isset($_POST['flagg']) ? $_POST['flagg'] : '';
$plateId = isset($_POST['plateId']) ? $_POST['plateId'] : '';
$year = isset($_POST['year']) ? $_POST['year'] : '';
$color = isset($_POST['color']) ? $_POST['color'] : '';
$km = isset($_POST['km']) ? $_POST['km'] : '';
$fueltype = isset($_POST['fueltype']) ? $_POST['fueltype'] : '';

// Adjusted handling for the updated bodyshape input (radio buttons)
$bodyshape = isset($_POST['bodyshape']) ? $_POST['bodyshape'] : '';

$transition = isset($_POST['transition']) ? $_POST['transition'] : '';
$enginecapacity = isset($_POST['enginecapacity']) ? $_POST['enginecapacity'] : '';
$model = isset($_POST['model']) ? $_POST['model'] : '';
$price = isset($_POST['price']) ? $_POST['price'] : '';
$officeId = isset($_POST['officeId']) ? $_POST['officeId'] : '';
$imagepath = isset($_POST['imagepath']) ? $_POST['imagepath'] : '';
$startDate = isset($_POST['startDate']) ? $_POST['startDate'] : '';
$endDate = isset($_POST['endDate']) ? $_POST['endDate'] : '';
$startDate1 = isset($_POST['startDate1']) ? $_POST['startDate1'] : '';
$endDate1 = isset($_POST['endDate1']) ? $_POST['endDate1'] : '';
$startDate5 = isset($_POST['startDate5']) ? $_POST['startDate5'] : '';
$endDate5 = isset($_POST['endDate5']) ? $_POST['endDate5'] : '';
$specificDate = isset($_POST['specificDate']) ? $_POST['specificDate'] : '';

if (!empty($plateId)) {
    $checkPlateIdQuery = mysqli_query($con, "SELECT * FROM car WHERE plateId = '$plateId'");
    $res = mysqli_fetch_row($checkPlateIdQuery);
    
    if ($res) {
        echo "<script type='text/javascript'> alert('Plate ID Already Exists');window.location='registration.html';</script>";
    } else {
        // Use a prepared statement to insert data into the 'car' table
        $sql = "INSERT INTO car (plateId, year, color, KM, fueltype, bodyshape, transition, enginecapacity, model, price, status, officeId, imagepath) VALUES ('$plateId', '$year', '$color', '$km', '$fueltype', '$bodyshape', '$transition', '$enginecapacity', '$model', '$price', 'active', '$officeId', '$imagepath')";

        if (mysqli_query($con, $sql)) {
            header("Location: admin.html");
        } else {
            echo "Error: " . mysqli_error($con);
        }
    }
}

//delete reservation  
$customerId = isset($_POST['customerId']) ? $_POST['customerId'] : '';
$carId = isset($_POST['carId']) ? $_POST['carId'] : '';
$pickupdate = isset($_POST['pickupDate']) ? $_POST['pickupDate'] : '';
$formattedPickupDate = formatDate($pickupdate);
if (!empty($customerId)) {
    $checkreservation= mysqli_query($con, "SELECT * FROM reservation WHERE customerId = '$customerId' AND carId = '$carId' AND pickupdate = '$formattedPickupDate' ");
    $reser = mysqli_fetch_assoc($checkreservation);

    if ($reser) {
        $updateQuery = "UPDATE car SET status = 'active' WHERE carId = '$carId'";
        if (mysqli_query($con, $updateQuery)) {
            header("Location: admin.html");
        } else {
            echo "Error: " . mysqli_error($con);
        }
        // $updateQuery = " DELETE reservation WHERE  customerId = '$customerId' and carId = '$carId' and pickupdate = '$pickupdate'";
        // mysqli_query($con, $updateQuery);
    }
}

function formatDate($date) {
    $formattedDate = date_create($date);
    $year = date_format($formattedDate, 'Y');
    $month = date_format($formattedDate, 'm');
    $day = date_format($formattedDate, 'd');
    return $year . '-' . $month . '-' . $day;
}

function displayTable($data) {
    if (empty($data)) {
        echo "<p>No data to display.</p>";
        return;
    }

    echo "<table>";
    echo "<tr>";
    foreach ($data[0] as $key => $value) {
        echo "<th>$key</th>";
    }
    echo "</tr>";

    foreach ($data as $row) {
        echo "<tr>";
        foreach ($row as $value) {
            echo "<td>$value</td>";
        }
        echo "</tr>";
    }

    echo "</table>";
}

// Function 1 to fetch reservations based on the date range

    global $con;
    $query = "SELECT * FROM reservation NATURAL JOIN car NATURAL JOIN customer WHERE pickupdate BETWEEN '$startDate' AND '$endDate' ORDER BY pickupdate";
  
    $stmt = mysqli_prepare($con, $query);
    //mysqli_stmt_bind_param($stmt, 'ss', $startDate, $endDate);

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!$result) {
        die('Error executing query: ' . mysqli_error($con));
    }

    $reservations = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $reservations[] = $row;
    }

    

    // Print the retrieved data for debugging
    displayTable($reservations);
    mysqli_stmt_close($stmt);


    // Function 2

    global $con;
    $query = "SELECT *
              FROM reservation 
              NATURAL JOIN car
              WHERE carID = '$carId' AND (pickupdate BETWEEN '$startDate1' AND '$endDate1') 
              ORDER BY carId";

    $stmt = mysqli_prepare($con, $query);
    

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!$result) {
        die('Error executing query: ' . mysqli_error($con));
    }

    $reservations = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $reservations[] = $row;
    }
    displayTable($reservations);
    mysqli_stmt_close($stmt);

    // Function 3.1
if($specificDate != ''){
global $con;
$query1 = "SELECT carId
           FROM reservation 
           WHERE '$specificDate' BETWEEN pickupdate AND returndate";
$stmt1 = mysqli_prepare($con, $query1);

mysqli_stmt_execute($stmt1);
$result1 = mysqli_stmt_get_result($stmt1);

if (!$result1) {
    die('Error executing query 1: ' . mysqli_error($con));
}

$reservations1 = array();
while ($row1 = mysqli_fetch_assoc($result1)) {
    $reservations1[] = $row1;
}

mysqli_stmt_close($stmt1);

// Function 3.2
$query2 = "SELECT carId 
            FROM car
            WHERE carId NOT IN ( SELECT carId
                                FROM reservation 
                                WHERE '$specificDate' BETWEEN pickupdate AND returndate)";
$stmt2 = mysqli_prepare($con, $query2);

mysqli_stmt_execute($stmt2);
$result2 = mysqli_stmt_get_result($stmt2);

if (!$result2) {
    die('Error executing query 2: ' . mysqli_error($con));
}

$reservations2 = array();
while ($row2 = mysqli_fetch_assoc($result2)) {
    $reservations2[] = $row2;
}

mysqli_stmt_close($stmt2);

// Display the tables
echo "<h2>Function 3.1 Output</h2>";
displayTable($reservations1);

echo "<h2>Function 3.2 Output</h2>";
displayTable($reservations2);
}
    //Function 4

    global $con;
    $query = "SELECT * FROM reservation
              NATURAL JOIN car
              NATURAL JOIN customer
              WHERE customerId = '$customerId'
              ORDER BY pickupdate";

    $stmt = mysqli_prepare($con, $query);
    //mysqli_stmt_bind_param($stmt, 's', $customerId);

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!$result) {
        die('Error executing query: ' . mysqli_error($con));
    }

    $reservations = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $reservations[] = $row;
    }
    displayTable($reservations);
    mysqli_stmt_close($stmt);

    //Function 5

    global $con;
    $query = "SELECT customerId,carId,DATEDIFF(returndate, pickupdate) AS Rental_Duration,
              DATEDIFF(returndate, pickupdate) * payment AS COST
              FROM	reservation
              where pickupdate  BETWEEN  '$startDate5' AND '$endDate5'";

    $stmt = mysqli_prepare($con, $query);
    //mysqli_stmt_bind_param($stmt, 's', $customerId);

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!$result) {
        die('Error executing query: ' . mysqli_error($con));
    }

    $reservations = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $reservations[] = $row;
    }
    displayTable($reservations);
    mysqli_stmt_close($stmt);

mysqli_close($con);
?>
</body>
</html>