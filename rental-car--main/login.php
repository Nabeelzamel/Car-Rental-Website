<?php
include("connect.php");
$username = $_POST['username'];
$password = $_POST['password'];
$selectedRole = $_POST["role"];
// $encrypt = md5($password);
$checkEmailQuery = mysqli_query($con, "SELECT * FROM customer WHERE username = '$username' and password = '$password'");
// $row= mysqli_fetch_assoc($checkEmailQuery);
$res = mysqli_fetch_assoc($checkEmailQuery);

if ($selectedRole == 'user') {
    if ($res && $res['adminflag'] == 0) {
        header("Location: rental.html?customerId=" . $res['customerId']);
        mysqli_close($con);
        exit();
    } else {
        echo "<script type='text/javascript'> alert('Incorrect email or password');window.location='login.html';</script>";
    }
} else if ($selectedRole == 'admin') {
    if ($res && $res['adminflag'] == 1) {
        header("Location: admin.html");
        mysqli_close($con);
        exit();
    } else {
        echo "<script type='text/javascript'> alert('Incorrect email or password');window.location='login.html';</script>";
    }
}
// Redirect to car_data.php
mysqli_close($con);

// exit();  // Ensure that no further code is executed after the redirection
?>
