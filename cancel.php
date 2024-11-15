<?php
require_once "C:/xampp/htdocs/Sem3_dbms_final/database/database.php";
session_start();

if (!isset($_SESSION["current_user"])) {
    header("Location: /Sem3_dbms_final/login.php");
    die();
}

$dbo = new Database();
if (!$dbo->conn) {
    die("Database connection failed");
}

$dbo->conn->exec("USE airlines");

$current_user = $_SESSION["current_user"];

// For the passenger details :
$c = "SELECT * FROM passenger_details WHERE passenger_id = :passenger_id";
$st = $dbo->conn->prepare($c);
$st->bindParam(':passenger_id', $current_user, PDO::PARAM_STR);
$st->execute();
$passenger = $st->fetch(PDO::FETCH_ASSOC);

if ($passenger) {
} else {
    echo "No passenger found with the passenger_id " . htmlspecialchars($current_user);
    die();
}


if (isset($_POST['submit'])) {
    $bid = $_POST['booking_id'];

    $sql = "SELECT * FROM Bookings B JOIN Tickets T ON B.booking_id=T.booking_id WHERE B.booking_id=:bid AND B.passenger_id=$current_user";
    $st1 = $dbo->conn->prepare($sql);
    $st1->bindParam(':bid', $bid);
    $st1->execute();
    $res = $st1->fetch(PDO::FETCH_ASSOC);

    if($res){
        $ct = "UPDATE Tickets SET status='Canceled' WHERE booking_id=:bid";
        $stt1 = $dbo->conn->prepare($ct);
        $stt1->bindParam(':bid', $bid);
        if ($stt1->execute()) {
            echo "Cancellation successful!";
        } else{
            echo "Cancellation Failed!";
        }
    } else {
        echo "No such booking found!";
    }
}

unset($dbo);
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cancel My Booking</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/passenger.css">
</head>
<body>
    <nav class="miniline"></nav>
    <h1>Hey <?php echo htmlspecialchars($passenger['first_name']); ?>...</h1>
    <nav class="miniline"></nav>
    <nav class="ribbon" style="justify-content: center;">You may want to cancel your booking</nav>
    <div class="container">
            <form action="" method="POST">
                <label for="booking_id">Booking ID :</label>
                <input type="text" id="booking_id" name="booking_id" required>
                <br><br>
                <br><br>
                <input type="submit" name="submit" value="Cancel Now" class="loginbtn">
            </form>
    </div>
    <div></div>
    <nav class="ribbon"></nav>
    <div class="container">
        <div class="cont">Don't want to cancel right now....?
            <button id="back" style="color: rgb(70, 130, 180); background-color: transparent; border-radius: none; border: none; padding: 17px;">Go Back</button>
        </div>
    </div>
    <br>
    <br>
    <br>
    <nav class="miniline"></nav>
    <script src="js/bookNow.js"></script>
    <script src="js/back.js"></script>
</body>
</html>