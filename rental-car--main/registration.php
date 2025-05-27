<?php
include ("connect.php");
    $name = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $phone = $_POST['phone'];
    $encrypt = md5($password);
    // Check if the email already exists in the 'user' table
    $checkEmailQuery = mysqli_query($con, "SELECT * FROM customer WHERE email = '$email'");
    $res=mysqli_fetch_row($checkEmailQuery);
    if ($res) {
       echo "<script type='text/javascript'> alert('Email Already Exists');window.location='registration.html';</script>";
    } else {
        // Use a prepared statement to insert data into the 'user' table
        $sql =  "INSERT INTO Customer  (username , password , fname , lname , email , phone , adminflag ) VALUES ('$name', '$password', '$fname', '$lname', '$email', '$phone', 0)";

        if (mysqli_query($con,$sql)) {
            header("Location: login.html");
        } else {
            echo "Error: " . mysqli_error($con);
        }
    }

    // Close the database connection
    mysqli_close($con);

?>