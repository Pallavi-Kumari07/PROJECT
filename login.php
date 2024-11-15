<!-- http://localhost/Sem3_dbms_final/login.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <h1 class="heading">Login/Register</h1>
    <div class="airplane-icon"></div>
    <div class="logform">
    <div class="container">
        <div class="innercont">
        <div class="box">
            <input
            type="text"
            id="username"
            required
            >
            <label for="username">Name</label>
        </div>
        <div class="box">
            <input
            type="password"
            id="password"
            required
            >
            <label for="password">Password</label>
        </div>
        <div class="logcont">
            <button class="inactive logbutton">
                LOGIN
            </button>
        </div>
        <div class="diverror" id="diverror">
            <label class="errormessage" id="errormessage"></label>
        </div>
        <h5 style="font-family: sans-serif; font-style: oblique;">Haven't yet made an account?
        <a href="register.php" id="regNow" style="color: goldenrod;">Register</a></h5>
        </div>
    </div>
    </div>
    <div class="lockscreen" id="lockscreen">
        <div class="spinner" id="spinner"></div>
        <label class="plwait" id="plwait">PLEASE WAIT</label>
    </div>
    <script src="js/jquery.js"></script>
    <script src="js/login.js"></script>
</body>
</html>
