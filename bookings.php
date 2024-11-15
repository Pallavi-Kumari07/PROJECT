<?php
require_once "C:/xampp/htdocs/Sem3_dbms_final/database/database.php";
session_start();
if (!isset($_SESSION["current_user"])) {
    header("Location: /Sem3_dbms_final/login.php");
    die();
}

$dbo = new Database();
$dbo->conn->exec("USE airlines");

$current_user = $_SESSION["current_user"];

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

$c2 = "SELECT * FROM Bookings WHERE passenger_id = :passenger_id";
$st2 = $dbo->conn->prepare($c2);
$st2->bindParam(':passenger_id', $current_user, PDO::PARAM_STR);
$st2->execute();
$booking = $st2->fetchAll(PDO::FETCH_ASSOC);


unset($dbo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MY BOOKINGS</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/passenger.css">
    <style>
        table{
            border-collapse: collapse;
            width: 100%;
        }
        th,td{
            border: 1px solid rgb(199, 168, 68);
            padding: 10px;
            text-align: center;
        }
        th{
            background-color: #dfd797;
        }
    </style>
</head>
<body>
    <nav class="miniline"></nav>
    <h1>Hey <?php echo htmlspecialchars($passenger['first_name']); ?>!</h1>
    <nav class="miniline"></nav>
    <p class="para">Ready to take off ??</p>
    <nav class="ribbon">
        <nav class="container">
            <img class="logo" src="https://img.lovepik.com/element/40151/7582.png_1200.png" alt="airplane">
            <ul class="cont">
            <li class="item"><img id ="pax" width="24" height="24" src="https://img.icons8.com/material-outlined/24/user--v1.png" alt="user--v1"/></li>
                <li class="item" id="bookings">My Bookings</li>
                <li class="item" id="flights">My Flights</li>
                <li class="item" id="payments">Payments</li>
                <li class="item" id="more">More</li>
            </ul>
        </nav>
    </nav>
    <div class="ribbon">
        <div></div>
        <div></div>
        <p>Don't want to continue.....?</p>
        <button id="btnlogout">LOGOUT</button>
        <div></div>
    </div>
    <div class="box">
        <p><strong>Name :</strong> <?php echo htmlspecialchars($passenger['first_name']); ?>
        <?php echo htmlspecialchars($passenger['last_name']); ?>
        </p>
        <p><strong>Passenger ID :</strong> <?php echo htmlspecialchars($passenger['passenger_id']); ?></p>
        <p><strong>DOB :</strong> <?php echo htmlspecialchars($passenger['date_of_birth']); ?></p>
        <p><strong>Email :</strong> <?php echo htmlspecialchars($passenger['email']); ?></p>
        <p><strong>Phone Number :</strong> <?php echo htmlspecialchars($passenger['phone_number']); ?></p>
    </div>
    <br>
    <br>
    <br>
    <div class="ribbon"><div></div>Planning on a new journey?
        <button class="loginbtn" id="bookNow">Book Now</button>
        <div></div>
    </div>
    <br>
    <br>
    <br>
    <div class="container">
        <div class="cont">
            <table border="1">
                <caption>MY BOOKINGS</caption>
                <div></div>
            <thead>
                <tr>
                    <th>
                        <p><strong>booking_id</strong></p>
                    </th>
                    <th>
                        <p><strong>flight_id</strong></p>
                    </th>
                    <th>
                        <p><strong>booking_date</strong></p>
                    </th>
                <th>
                    <p><strong>seat_number</strong></p>
                </th>
                <th>
                    <p><strong>class</strong></p>
                </th>
                <th>
                    <p><strong>price</strong></p>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($booking)): ?>
                <?php foreach ($booking as $book): ?>
                    <tr>
                        <td>
                            <p><?php echo htmlspecialchars($book['booking_id']); ?></p>
                        </td>
                        <td>
                            <p><?php echo htmlspecialchars($book['flight_id']); ?></p>
                        </td>
                        <td>
                            <p><?php echo htmlspecialchars($book['booking_date']); ?></p>
                        </td>
                        <td>
                            <p><?php echo htmlspecialchars($book['seat_number']); ?></p>
                        </td>
                        <td>
                            <p><?php echo htmlspecialchars($book['class']); ?></p>
                        </td>
                        <td>
                            <p><?php echo htmlspecialchars($book['price']); ?></p>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8">No bookings found for the passenger_id <?php echo htmlspecialchars($current_user) ?></td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <br>
        <br>
        <br>
        <div class="ribbon"><div></div>Want to cancel your bookings?
            <button class="loginbtn" id="cancelNow">Cancel Booking</button>
            <div></div>
        </div>
    <nav class="line"></nav>
    <div class="footer">
        <div class="footcont">
            <ul class="list">
                <li class="item home">Home</li>
                <li class="item bookings">My Bookings</li>
                <li class="item flights">My Flights</li>
            </ul>
            <ul class="list">
                <li class="item more">More</li>
                <li class="item">Insta Handle</li>
                <li class="item">Facebook</li>
            </ul>
            <ul class="list">
                <li class="item">Linked In</li>
                <li class="item">Email</li>
                <li class="item">Youtube</li>
            </ul>
        </div>
    </div>

    <script src="js/jquery.js"></script>
    <script src="js/logout.js"></script>
    <script src="js/pax.js"></script>
    <script src="js/bookNow.js"></script>
    <script src="js/more.js"></script>
    <script src="js/profile.js"></script>
    <script src="js/payments.js"></script>
    <script src="js/cancel.js"></script>
    <script src="js/footer.js"></script>
</body>
</html>