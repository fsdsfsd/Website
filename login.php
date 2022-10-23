<?php
/* Database credentials. Assuming you are running MySQL
server with default setting (user 'root' with no password) */
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'log');
 
/* Attempt to connect to MySQL database */
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
 
// Check connection
if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
// Define variables and initialize with empty values
$username = 
$password = "";
$username_err = 
$username_errs =
$password_err = 
$password_errs =
$login_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
  if(isset($_POST['username'])) {
  $username = $_POST['username'];
  $number = preg_match('@[0-9]@', $username);
  $lowercase = preg_match('@[a-z]@', $username);
  $specialChars = preg_match('@[^\w]@', $username);
  if(strlen($username) < 8 || !$number || !$lowercase || !$specialChars){
    $username_err = "Username must have 8 characters and must contain at least one number and one special character.";
  }else{
        $username = trim($_POST["username"]);
    }
    
  }
 if(isset($_POST['password'])) {
    $password = $_POST['password'];
  $number = preg_match('@[0-9]@', $password);
  $lowercase = preg_match('@[a-z]@', $password);
 
  if(strlen($password) <8 || !$number || !$lowercase) {
     $password_err = "Password must have 8 characters and must contain at least one number.";
  }else{
        $password = trim($_POST["password"]);
    }
    
  }
  // Check if username is empty
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter username.";
    } else{
        $username = trim($_POST["username"]);
    }
    
    // Check if password is empty
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter your password.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    
    // Validate credentials
    if(empty($username_err) && empty($password_err)){
        // Prepare a select statement
        $sql = "SELECT id, username, password FROM users WHERE username = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            // Set parameters
            $param_username = $username;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Store result
                mysqli_stmt_store_result($stmt);
                
                // Check if username exists, if yes then verify password
                if(mysqli_stmt_num_rows($stmt) == 1){                    
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password);
                    if(mysqli_stmt_fetch($stmt)){
                        if(password_verify($password, $hashed_password)){
                            // Password is correct, so start a new session
                            session_start();
                            
                            // Store data in session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;                            
                            
                            // Redirect user to welcome page
                            header("location: welcome.php");
                        } else{
                            // Password is not valid, display a generic error message
                            $login_err = "Invalid username or password.";
                        }
                    }
                } else{
                    // Username doesn't exist, display a generic error message
                    $login_err = "Invalid username or password.";
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Close connection
    mysqli_close($link);
}
?>

<!DOCTYPE html>
<html>
<title> Login </title>
    
<head>
<link rel="preconnect" href="https://fonts.gstatic.com">
<link href="https://fonts.googleapis.com/css2?family=Kalam:wght@300&family=Kaushan+Script&family=Zen+Dots&display=swap" rel="stylesheet">
<link rel="preconnect" href="https://fonts.gstatic.com">
<link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
body {font-family: Arial, Helvetica, sans-serif;}
body {
  background-image: url('pixar.jpg');
  background-repeat: no-repeat;
  background-attachment: fixed;
  background-size: 100%;
}
header {
  height: 20px;
  background: transparent;
  width: 100%;
  z-index: 10;
  position: fixed;
  top: -10px;
  left: -2px
}
input[type=text], input[type=password] {
  width: 100%;
  padding: 12px 13px;
  margin: 10px 0;
  display: inline-block;
  border: 1px solid #ccc;
  box-sizing: border-box;
}

button {
  background-color: #f25278;
  color: white;
  padding: 14px 20px;
  margin: 8px 0;
  border: none;
  cursor: pointer;
  width: 100%;
}

button:hover {
  opacity: 0.8;
}
.container h2{
position: fixed;
font-family:'Pacifico', cursive;
left: 47%;
top: 8%;
font-size: 40px;
}

.cancelbtn {
  width: auto;
  padding: 10px 18px;
  background-color: #FFFFFF;
}

.imgcontainer {
  text-align: center;
  margin: 73px 0 12px 0;
  position: relative;
}

img.avatar {
  width: 10%;
  border-radius: 50%;
}

.container {
  padding: 20px;

}
.container img{
position: relative;
left: 35%;
clip-path: circle();
}
span.psw {
  float: right;
  padding-top: 16px;
}

.modal {
  display: none;
  position: fixed;
  z-index: 1;
  left: 0;
  top: 300%;
  width: 80%;
  height: 150%;
  overflow: auto;
  background-color: rgb(128,0,0); 
  padding-top: 35px;
}

.modal-content {
  background-color: #fefefe;
  margin: 5% auto 15% auto;
  border: 5px solid #fc4c4e;
  width: 35%;
  left: 33%;
  top: 0%;
  position: absolute;
}


.animate {
  -webkit-animation: animatezoom 0.6s;
  animation: animatezoom 0.6s
}

@-webkit-keyframes animatezoom {
  from {-webkit-transform: scale(0)} 
  to {-webkit-transform: scale(1)}
}
  
@keyframes animatezoom {
  from {transform: scale(0)} 
  to {transform: scale(1)}
}

    .posi2{
  position: fixed;
  top: 134px;
  left: 250px;
 }
@media screen and (max-width: 300px) {
  span.psw {
     display: block;
     float: none;
  }
  .cancelbtn {
     width: 100%;
  }
}
</style>
</head>
<body>
<header>
<a href="register.php" style="position:absolute;top:30px;left:90%;right:10px;color:#fff;text-decoration:none;border:none;font-family:'Pacifico', cursive; font-size: 30px;" class="button">Signup</a> <br>
</header>
<div id="id01">
 <form class="modal-content animate" \\ method="post">
    <div class="imgcontainer">
      <span href="Website.html" onclick="document.getElementById('id01').style.display='none'" </span>
    </div>
   <div class="container">
    <h2>Login</h2>
    &nbsp;
    <img src="avatar.jpg" width="125" height="125" />

  <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <div class="input-group">
      <input type="text" placeholder= "Username" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>" >
                <span class="invalid-feedback"><?php echo $username_err; ?><?php echo $username_errs; ?></span>
    </div>
    <div class="input-group">
      <input type="password" placeholder= "Password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" >
                <span class="invalid-feedback"><?php echo $password_err; ?><?php echo $password_errs; ?></span>
    </div>
    <div class="input-group">
      <button type="submit" class="btn" name="submit">Login</button>
    </div>
    <label>
        <input type="checkbox" name="remember"> Remember me
      </label>
        <div style="background-color:transparent;">

      <h4>Don't have an account? <a href="register.php">Signup here</a>.</h4>
  </form>
</div>
</body>
</html>