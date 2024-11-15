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
if (isset($_POST['submit'])) {
    //$flight_date = $_POST['flight_date'];
    $from = $_POST['from'];
    $to = $_POST['to'];
    if($from==$to){
        echo "Please select valid departure airport and arrival airport.";
        die();
    }
    $query = "SELECT F.*, FR.distance 
          FROM flight_details F 
          LEFT JOIN FlightRoutes FR 
          ON F.departure_airport = FR.departure_airport_id 
          AND F.arrival_airport = FR.arrival_airport_id 
          WHERE F.departure_airport = :departure_airport 
          AND F.arrival_airport = :arrival_airport";
    $res = $dbo->conn->prepare($query);
    $res->bindParam(':departure_airport', $from);
    $res->bindParam(':arrival_airport', $to);
    $res->execute();

    $result = $res->fetchAll(PDO::FETCH_ASSOC);

    if (!$result) {
        echo "No flights available!";
        die();
    }
}


unset($dbo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Passenger Page</title>
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
        <p><strong>USERNAME:</strong> <?php echo htmlspecialchars($passenger['user_name']); ?></p>
        <p><strong>Passenger ID:</strong> <?php echo htmlspecialchars($passenger['passenger_id']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($passenger['email']); ?></p>
        <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($passenger['phone_number']); ?></p>
        <button id="btnlogout">LOGOUT</button>
    </div>
    <div class="miniline"></div>
    <br>
    <br>
    <br>
    <section class="slide">
        <div class="box">
            <p><strong>Name :</strong> <?php echo htmlspecialchars($passenger['first_name']); ?>
            <?php echo htmlspecialchars($passenger['last_name']); ?>
            </p>
            <p><strong>Passenger ID :</strong> <?php echo htmlspecialchars($passenger['passenger_id']); ?></p>
            <p><strong>DOB :</strong> <?php echo htmlspecialchars($passenger['date_of_birth']); ?></p>
            <p><strong>Email :</strong> <?php echo htmlspecialchars($passenger['email']); ?></p>
            <p><strong>Phone Number :</strong> <?php echo htmlspecialchars($passenger['phone_number']); ?></p>
        </div>
        <div class="box">
            <img class="pic airport" src="https://www.thewalletstore.in/cdn/shop/products/Untitled-3.0.jpg?v=1668432118&width=2048" alt="">
        </div>
    </section>

    <nav class="miniline"></nav>
    <nav class="ribbon"></nav>

    <h1>Looking for Flights ?</h1>
    <nav class="miniline"></nav>
    <br>
    <br>
    <br>
    <br>
    <div class="container">
        <div class="cont">
            <form action="" method="POST">
                <label for="from">From :</label>
                <select name="from" id="from">
                    <option value="1">Kuala Lumpur, Malaysia</option>
                    <option value="2">Hanoi, Vietnam</option>
                    <option value="3">New Delhi, India</option>
                </select>
                <label for="to">To :</label>
                <select name="to" id="to">
                    <option value="3">New Delhi, India</option>
                    <option value="2">Hanoi, Vietnam</option>
                    <option value="1">Kuala Lumpur, Malaysia</option>
                </select>
                <!-- <label for="flight_date">Date of Flight :</label>
                <input type="date" id="flight_date" name="flight_date" required> -->

                <br><br>
                <br><br>
                <input type="submit" name="submit" value="Go" class="loginbtn">
            </form>
        </div>
    </div>

    <div class="container">
    <div class="cont">
        <table border="1">
            <caption>FLIGHTS AVAILABLE</caption>
            <thead>
            <tr>
                <th><strong>Flight ID</strong></th>
                <th><strong>Flight Number</strong></th>
                <th><strong>Departure Airport</strong></th>
                <th><strong>Departure Time</strong></th>
                <th><strong>Arrival Airport</strong></th>
                <th><strong>Arrival Time</strong></th>
                <th><strong>Duration</strong></th>
                <th><strong>Status</strong></th>
                <th><strong>Distance</strong></th>
            </tr>
            </thead>
            <tbody>
            <?php if (isset($result) && !empty($result)): ?>
                <?php foreach ($result as $fl): ?>
                <tr>
                    <td><?php echo htmlspecialchars($fl['flight_id']); ?></td>
                    <td><?php echo htmlspecialchars($fl['flight_number']); ?></td>
                    <td><?php echo htmlspecialchars($fl['departure_airport']); ?></td>
                    <td><?php echo htmlspecialchars($fl['departure_time']); ?></td>
                    <td><?php echo htmlspecialchars($fl['arrival_airport']); ?></td>
                    <td><?php echo htmlspecialchars($fl['arrival_time']); ?></td>
                    <td><?php echo htmlspecialchars($fl['duration']); ?></td>
                    <td><?php echo htmlspecialchars($fl['status']); ?></td>
                    <td><?php echo htmlspecialchars($fl['distance']); ?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8">No flights available for the selected criteria.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
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
    <script src="js/more.js"></script>
    <script src="js/profile.js"></script>
    <script src="js/payments.js"></script>
    <script src="js/footer.js"></script>
</body>
</html>
