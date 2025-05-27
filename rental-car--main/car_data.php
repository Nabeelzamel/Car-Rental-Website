<?php
include("connect.php");
$selectedLocation = isset($_POST['location']) ? $_POST['location'] : '';
$color = isset($_POST['color']) ? $_POST['color'] : '';
$shape = isset($_POST['shape']) ? $_POST['shape'] : '';
$price = isset($_POST['price']) ? $_POST['price'] : '';
$carid = isset($_POST['carid']) ? $_POST['carid'] : '';
$pickupDate = isset($_POST['pickupDate']) ? $_POST['pickupDate'] : '';
$returnDate = isset($_POST['returnDate']) ? $_POST['returnDate'] : '';
$paymentMethod = isset($_POST['paymentMethod']) ? $_POST['paymentMethod'] : '';
$customerId =isset($_POST['customerId']) ? $_POST['customerId'] : '';
// $price1=$price-20;
// $price2=$price+20;
// Fetch car data
if(empty($selectedLocation)){
$checkCarQuery = mysqli_query($con, "SELECT * FROM `car` WHERE status='active'");
}
if(!empty($color) || !empty($shape)|| !empty($price)){
    // $checkCarQuery = mysqli_query($con, "SELECT * FROM (SELECT * FROM car NATURAL JOIN office WHERE location='$selectedLocation'  AND status='active' ) WHERE (color='$color' OR bodyshape='$shape' OR price='$price')  AND status='active'  ");
     $checkCarQuery = mysqli_query($con, "SELECT * FROM car NATURAL JOIN office WHERE (color='$color' OR bodyshape='$shape' OR price='$price')  AND status='active' UNION SELECT * FROM car NATURAL JOIN office WHERE `location`='$selectedLocation'  AND status='active'");
// $checkCarQuery = mysqli_query($con, "SELECT * FROM `car` WHERE (color='$color' OR bodyshape='$shape' OR price='$price') AND status='active'");
}
else{
    $checkCarQuery = mysqli_query($con, "SELECT * FROM `car` WHERE status='active'");
}
$cars = array();

if (!$checkCarQuery) {
    $error = ['error' => 'Query failed: ' . mysqli_error($con)];
    echo json_encode($error);
    exit;  // Terminate script execution
}

while ($row = mysqli_fetch_assoc($checkCarQuery)) {
    $cars[] = array(
        'model' => $row['model'],
        'year' => $row['year'],
        'price'=> $row['price'],
        'imagepath'=>$row['imagepath'],
        'carId' => $row['carId']  // Add carId to the response
   );
}

// If pickupDate is provided, update reservation status && !empty($customerId)
if (!empty($pickupDate) ) {
    $sql =  "INSERT INTO reservation  (customerId, carId, pickupdate, returndate, payment) VALUES ('$customerId', '$carid', '$pickupDate', '$returnDate', '$paymentMethod')";
    mysqli_query($con, $sql);

    $updateQuery = "UPDATE car SET status = 'Reserved' WHERE carId = '$carid'";
    mysqli_query($con, $updateQuery);
}
// Return JSON response
header('Content-Type: application/json');
echo json_encode($cars);
mysqli_close($con);
?>
