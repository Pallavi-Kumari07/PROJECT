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


$c3 = "SELECT * FROM flight_details";
$st3 = $dbo->conn->prepare($c3);
$st3->execute();
$schedules = $st3->fetchAll(PDO::FETCH_ASSOC);

// For airline details :
$c2 = "SELECT * FROM airline_details A JOIN Aircrafts R ON A.airline_id=R.airline_id";
$st2 = $dbo->conn->prepare($c2);
$st2->execute();
$alns = $st2->fetchAll(PDO::FETCH_ASSOC);

// For airport details :
$c5 = "SELECT * FROM Airports";
$st5 = $dbo->conn->prepare($c5);
$st5->execute();
$airs = $st5->fetchAll(PDO::FETCH_ASSOC);

// For the crew details :
$c1 = "SELECT DISTINCT C.crew_id, C.airline_id, C.name, C.role, C.years_of_experience FROM Bookings B, flight_details F, Crew C WHERE B.passenger_id = :passenger_id AND B.flight_id=F.flight_id AND F.airline_id=C.airline_id";
$st1 = $dbo->conn->prepare($c1);
$st1->bindParam(':passenger_id', $current_user, PDO::PARAM_STR);
$st1->execute();
$crew = $st1->fetchAll(PDO::FETCH_ASSOC);


unset($dbo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MORE</title>
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
    <br><br><br><br><br>
    <div class="container">
        <div class="cont">
        <table border="1">
            <caption>SCHEDULES</caption>
            <thead>
            <tr>
                <th>
                    <p><strong>flight_id</strong></p>
                </th>
                <th>
                    <p><strong>departure_date</strong></p>
                </th>
                <th>
                    <p><strong>arrival_date</strong></p>
                </th>
                <th>
                    <p><strong>status</strong></p>
                </th>
            </tr>
            </thead>
            <tbody>
            <?php if (!empty($schedules)): ?>
                <?php foreach ($schedules as $schedule): ?>
                <tr>
                    <td>
                        <p><?php echo htmlspecialchars($schedule['flight_id']); ?></p>
                    </td>
                    <td>
                        <p><?php echo htmlspecialchars($schedule['departure_time']); ?></p>
                    </td>
                    <td>
                        <p><?php echo htmlspecialchars($schedule['arrival_time']); ?></p>
                    </td>
                    <td>
                        <p><?php echo htmlspecialchars($schedule['status']); ?></p>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php else: ?>
                <tr>
                    <td colspan="8">No flights found for the passenger_id " <?php htmlspecialchars($current_user) ?></td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
        </div>
    </div>
    <br><br><br><br><br><br><br><br>
    <div class="miniline" style="color: rgb(159, 157, 157); font-size: x-large; opacity: 0.85">Crew flying with you :</div>
    <br><br><br><br>
    <div class="container">
        <div class="cont">
        <table border="1">
            <caption>CREW</caption>
            <thead>
            <tr>
                <th>
                    <p><strong>crew_id</strong></p>
                </th>
                <th>
                    <p><strong>airline_id</strong></p>
                </th>
                <th>
                    <p><strong>name</strong></p>
                </th>
                <th>
                    <p><strong>role</strong></p>
                </th>
                <th>
                    <p><strong>years_of_experience</strong></p>
                </th>
            </tr>
            </thead>
            <tbody>
            <?php if (!empty($crew)): ?>
                <?php foreach ($crew as $cr): ?>
                <tr>
                    <td>
                        <p><?php echo htmlspecialchars($cr['crew_id']); ?></p>
                    </td>
                    <td>
                        <p><?php echo htmlspecialchars($cr['airline_id']); ?></p>
                    </td>
                    <td>
                        <p><?php echo htmlspecialchars($cr['name']); ?></p>
                    </td>
                    <td>
                        <p><?php echo htmlspecialchars($cr['role']); ?></p>
                    </td>
                    <td>
                        <p><?php echo htmlspecialchars($cr['years_of_experience']); ?></p>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php else: ?>
                <tr>
                    <td colspan="8">No crew info available!</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
        </div>
    </div>
    <br><br><br><br><br><br><br><br>
    <div class="miniline" style="color: rgb(159, 157, 157); font-size: x-large; opacity: 0.85">Airline Partners :</div>
    <br><br><br><br>
    <div class="container">
        <div class="cont">
        <table border="1">
            <caption>AIRLINES</caption>
            <thead>
            <tr>
                <th>
                    <p><strong>airline_id</strong></p>
                </th>
                <th>
                    <p><strong>airline_name</strong></p>
                </th>
                <th>
                    <p><strong>country</strong></p>
                </th>
                <th>
                    <p><strong>headquarters</strong></p>
                </th>
                <th>
                    <p><strong>aircraft_id</strong></p>
                </th>
                <th>
                    <p><strong>aircraft_model</strong></p>
                </th>
                <th>
                    <p><strong>capacity</strong></p>
                </th>
                <th>
                    <p><strong>manufacturing_year</strong></p>
                </th>
            </tr>
            </thead>
            <tbody>
            <?php if (!empty($alns)): ?>
                <?php foreach ($alns as $al): ?>
                <tr>
                    <td>
                        <p><?php echo htmlspecialchars($al['airline_id']); ?></p>
                    </td>
                    <td>
                        <p><?php echo htmlspecialchars($al['airline_name']); ?></p>
                    </td>
                    <td>
                        <p><?php echo htmlspecialchars($al['country']); ?></p>
                    </td>
                    <td>
                        <p><?php echo htmlspecialchars($al['headquarters']); ?></p>
                    </td>
                    <td>
                        <p><?php echo htmlspecialchars($al['aircraft_id']); ?></p>
                    </td>
                    <td>
                        <p><?php echo htmlspecialchars($al['aircraft_model']); ?></p>
                    </td>
                    <td>
                        <p><?php echo htmlspecialchars($al['capacity']); ?></p>
                    </td>
                    <td>
                        <p><?php echo htmlspecialchars($al['manufacturing_year']); ?></p>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php else: ?>
                <tr>
                    <td colspan="8">No airline found!</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
        </div>
    </div>
    <br><br><br><br><br><br><br><br>
    <div class="miniline" style="color: rgb(159, 157, 157); font-size: x-large; opacity: 0.85">Airports Available :</div>
    <br><br><br><br>
    <div class="container">
        <div class="cont">
        <table border="1">
            <caption>AIRPORTS</caption>
            <thead>
            <tr>
                <th>
                    <p><strong>airport_id</strong></p>
                </th>
                <th>
                    <p><strong>airport_name</strong></p>
                </th>
                <th>
                    <p><strong>city</strong></p>
                </th>
                <th>
                    <p><strong>country</strong></p>
                </th>
                <th>
                    <p><strong>IATA_code</strong></p>
                </th>
            </tr>
            </thead>
            <tbody>
            <?php if (!empty($airs)): ?>
                <?php foreach ($airs as $ar): ?>
                <tr>
                    <td>
                        <p><?php echo htmlspecialchars($ar['airport_id']); ?></p>
                    </td>
                    <td>
                        <p><?php echo htmlspecialchars($ar['airport_name']); ?></p>
                    </td>
                    <td>
                        <p><?php echo htmlspecialchars($ar['city']); ?></p>
                    </td>
                    <td>
                        <p><?php echo htmlspecialchars($ar['country']); ?></p>
                    </td>
                    <td>
                        <p><?php echo htmlspecialchars($ar['IATA_code']); ?></p>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php else: ?>
                <tr>
                    <td colspan="8">No airport details available!</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
        </div>
    </div>
    <br><br><br>
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