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
    //var_dump($_POST);

    $flight_date = $_POST['flight_date'];
    $flight_time = $_POST['flight_time'];
    $class = $_POST['class'];
    $pay_mode = $_POST['pay_mode'];
    $passenger_id = $current_user;
    $booking_date = date('Y-m-d H:i:s');
    $price=0;

    $from = $_POST['from'];
    $to = $_POST['to'];
    switch ($class) {
        case 'Economy':
            $price = rand(100, 600);
            break;
        case 'Business':
            $price = rand(700, 1500);
            break;
        case 'First Class':
            $price = rand(1500, 3000);
            break;
    }
    $ticket_start = 'TCKT';
    $ticket_rest = random_int(1050,9999);
    $ticket_num = $ticket_start.$ticket_rest;
    $status_all = ['Active','Canceled'];
    $i = rand(0,1);
    $status = 'Active';
    if($i<0.5) $status = 'Canceled';


    $flight_query = "SELECT flight_id FROM flight_details WHERE departure_airport = :departure_airport AND arrival_airport = :arrival_airport ORDER BY RAND() LIMIT 1";

    //$flight_query = "SELECT flight_id FROM flight_details ORDER BY RAND() LIMIT 1";
    $flight_result = $dbo->conn->prepare($flight_query);
    $flight_result->bindParam(':departure_airport', $from);
    $flight_result->bindParam(':arrival_airport', $to);
    $flight_result->execute();
    $row = $flight_result->fetch(PDO::FETCH_ASSOC);
    
    if ($row) {
        $flight_id = $row['flight_id'];
    } else {
        echo "No flights available!";
        exit;
    }
    $bid_query = "SELECT * FROM Bookings WHERE booking_id = (SELECT MAX(booking_id) FROM Bookings)";
    $bid_result = $dbo->conn->prepare($bid_query);
    $bid_result->execute();
    $row1 = $bid_result->fetch(PDO::FETCH_ASSOC);
    if($row1){
        $bid = $row1['booking_id']+1;
    }else{
        exit;
    }

    do {
        $seat_number = rand(1, 150);
    
        $seat_check_query = "SELECT * FROM Bookings WHERE flight_id = :flight_id AND seat_number = :seat_number";
        $seat_check_stmt = $dbo->conn->prepare($seat_check_query);
        $seat_check_stmt->bindParam(':flight_id', $flight_id);
        $seat_check_stmt->bindParam(':seat_number', $seat_number);
        $seat_check_stmt->execute();
    
    } while ($seat_check_stmt->rowCount() > 0);

    $sql = "INSERT INTO Bookings (booking_id, flight_id, passenger_id, booking_date, seat_number, class, price) 
            VALUES (:bid, :flight_id, :passenger_id, :booking_date, :seat_number, :class, :price)";
    
    $insert_stmt = $dbo->conn->prepare($sql);
    $insert_stmt->bindParam(':bid', $bid);
    $insert_stmt->bindParam(':flight_id', $flight_id);
    $insert_stmt->bindParam(':passenger_id', $passenger_id);
    $insert_stmt->bindParam(':booking_date', $booking_date);
    $insert_stmt->bindParam(':seat_number', $seat_number);
    $insert_stmt->bindParam(':class', $class);
    $insert_stmt->bindParam(':price', $price);
    

    $cc = "INSERT INTO Payments (booking_id, payment_date, amount, payment_method)
               VALUES (:bid, :booking_date, :amount, :pay_mode)";
    $stt = $dbo->conn->prepare($cc);
    $stt->bindParam(':bid', $bid);
    $stt->bindParam(':booking_date', $booking_date);
    $stt->bindParam(':amount', $price);
    $stt->bindParam(':pay_mode', $pay_mode);

    $ct = "INSERT INTO Tickets (booking_id, issue_date, ticket_number, status)
               VALUES (:bid, :booking_date, :ticket_num, :status_final)";
    $stt1 = $dbo->conn->prepare($ct);
    $stt1->bindParam(':bid', $bid);
    $stt1->bindParam(':booking_date', $booking_date);
    $stt1->bindParam(':ticket_num', $ticket_num);
    $stt1->bindParam(':status_final', $status);
    
    if ($insert_stmt->execute() && $stt->execute() && $stt1->execute()) {
        echo "Booking successful!";
        
    } else {
        $error = $insert_stmt->errorInfo();
        echo "Error: " . $sql . "<br>" . $error[2];
    }
}

unset($dbo);
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Now</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/passenger.css">
</head>
<body>
    <nav class="miniline"></nav>
    <h1>Glad to see you here <?php echo htmlspecialchars($passenger['first_name']); ?> !</h1>
    <nav class="miniline"></nav>
    <nav class="ribbon"></nav>
    <div class="container">
            <form action="" method="POST">
                <label for="flight_date">Date of Flight :</label>
                <input type="date" id="flight_date" name="flight_date" required>
                
                <label for="from">From :</label>
                <select name="from" id="from">
                    <option value="3">New Delhi, India</option>
                    <option value="1">Kuala Lumpur, Malaysia</option>
                    <option value="2">Hanoi, Vietnam</option>
                </select>
                <label for="to">To :</label>
                <select name="to" id="to">
                    <option value="2">Hanoi, Vietnam</option>
                    <option value="3">New Delhi, India</option>
                    <option value="1">Kuala Lumpur, Malaysia</option>
                </select>

                <label for="flight_time">Time of Flight :</label>
                <input type="time" id="flight_time" name="flight_time" required>
                
                <label for="class">Class :</label>
                <select id="class" name="class" required>
                    <option value="Economy">Economy</option>
                    <option value="Business">Business</option>
                    <option value="First Class">First Class</option>
                </select>
                <label for="pay_mode">Payment Mode :</label>
                <select name="pay_mode" id="pay_mode">
                    <option value="Credit Card">Credit Card</option>
                    <option value="PayPal">PayPal</option>
                    <option value="Debit Card">Debit Card</option>
                </select>
                <br><br>
                <br><br>
                <input type="submit" name="submit" value="Book Now" class="loginbtn">
            </form>
    </div>
    <div></div>
    <nav class="ribbon"></nav>
    <div class="container">
        <div class="cont">Don't want to book right now....?
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