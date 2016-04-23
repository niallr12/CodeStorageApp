<?php
   session_start();
   //checks to see if the user is already logged on. If they are logged on it redirects to the application page. 
   if(isset($_SESSION['loggedin'])){
      if($_SESSION['usertype'] == 'User'){
         header( 'Location: application.php');
      }
      else{
         header('Location: admin.php');
      }
   }

   //If the user enters incorrect login details an error message is displayed. 
  if(isset($_SESSION['Login.Error'])){ 
   $message = $_SESSION['Login.Error'];
    echo "<script>alert('$message')</script>";
    unset($_SESSION['Login.Error']);
  }
?>
<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title>CodeShare</title>
      <!-- Bootstrap -->
      <link href="css/bootstrap.css" rel="stylesheet">
      <link href="css/mystyle.css" rel="stylesheet">
      <link href='http://fonts.googleapis.com/css?family=Josefin+Sans' rel='stylesheet' type='text/css'>
      <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
      <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
      <![endif]-->
   </head>
   <body>
      <!--Navigation-->
      <nav class = 'navbar navbar-default'>
         <div class='container'>
            <div class='navbar-header'>
               <a class='navbar-brand' href='index.php'><img src = "img/logo.jpg"></a>
            </div>
            <ul class='nav navbar-nav navbar-right'>
               <div class='pull-right navbuttons'>
                  <a class='btn btn-primary btn-md' href='#SignInModal' id="signinbutton" data-toggle="modal">Sign In</a>
                  <a class='btn btn-success btn-md' href='#SignUpModal' id="signupbutton" data-toggle="modal">Sign Up</a>
               </div>
            </ul>
         </div>
      </nav>
      <div class="jumbotron">
         <div class="container-fluid text-center">
            <h1>Welcome to CodeShare!</h1>
            <p>Easily store and share your code with anyone. Simply want someone to see your code on any device without needing access to an IDE? This application is for you!</p>
            <p>Why not try it now? Its free! <a href="#SignUpModal" data-toggle="modal">Sign Up</a></p>
         </div>
      </div>
      <div class="container">
         <div class="row">
            <div class="col-md-4 text-center">
               <img class="img-circle" src="img/keyboard.jpg">
               <h2>Edit</h2>
               <p>Edit and update your code from anywhere. All you need is an internet connection.</p>
            </div>
            <div class="col-md-4 text-center">
               <img class="img-circle" src="img/share.jpg">
               <h2>Share</h2>
               <p>Easily share your code with anyone by simply providing their username. Allow them read or write access.</p>
            </div>
            <div class="col-md-4 text-center">
               <img class="img-circle" src="img/code.jpg">
               <h2>Store</h2>
               <p>Store all your code snippets so they can be viewed later on different devices.</p>
            </div>
         </div>
      </div>
      <!--Footer-->
      <footer class='container-fluid panel-footer text-center'>
         <div class='col-md-4 col-md-offset-4'>
            <div class='row'>
               <p>CodeShare</p>
               <p>Copyright of Niall Ryan</p>
            </div>
         </div>
      </footer>
      <!--Sign up Modal-->
      <div id="SignUpModal" class="modal fade">
         <div class="modal-dialog">
            <div class="modal-content">
               <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                  <h4 class="modal-title">Sign Up</h4>
               </div>
               <div class="modal-body">
                  <form class="form" id="signup" method="post" autocomplete="off" action ="<?php echo 'php/signup.php';?>">
                      <div class="form-group">
                        <label for="username">User Name:</label>
                        <input type="text" class="form-control" placeholder="UserName" id="username" name="user" maxlength="10" pattern="[a-zA-Z0-9]{4,}" title="No spaces are permitted in username, length must be between 5 and 10 characters" required>
                      </div>
                      <div id="usernameavailable"></div>
                     <div class="form-group">
                        <label for="firstname">First Name:</label>
                        <input type="text" class="form-control" placeholder="First Name" id="firstname" maxlength="15" name="fname" pattern="[A-Za-z']{1,}" title="Only letters are allowed and no spaces" required>
                     </div>
                     <div class="form-group">
                        <label for="lastname">Last Name:</label>
                        <input type="text" class="form-control" placeholder="Last Name" id="lastname" maxlength="15"  name="lname" pattern="[A-Za-z']{1,}" title="Only letters are allowed and no spaces" required>
                     </div>
                     <div class="form-group">
                        <label for="Email">Email:</label>
                        <input type="email" class="form-control" placeholder="Email" id="Email" name="email" required>
                        <div id="emailavailable"></div>
                     </div>
                     <div class="form-group">
                        <label for="Password">Password:</label>
                        <input type="password" class="form-control" placeholder="Password" title="at least eight symbols containing at least one number, one lower, and one upper letter" type="text" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" id="Password" name="password" required>
                     </div>
                     <div class="form-group">
                        <label for="conPassword">Confirm Password:</label>
                        <input type="password" class="form-control" placeholder="Confirm Password" id="conPassword" required>
                        <div id="passwordmatch"></div>
                     </div>
                     <button class="btn btn-lg btn-primary btn-block" type="submit" name="submit" id="submit">Register</button>
                  </form>
               </div>
            </div>
         </div>
      </div>
      <!--Sign In Modal-->
      <div id="SignInModal" class="modal fade">
         <div class="modal-dialog">
            <div class="modal-content">
               <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                  <h4 class="modal-title">Sign In</h4>
               </div>
               <div class="modal-body">
                  <form class="form" method="post" autocomplete="off"  action ="<?php echo 'php/checklogin.php';?>">
                     <div class="form-group">
                        <label for="usr">Username:</label>
                        <input type="text" class="form-control" placeholder="User name" id="user" name="user" required>
                     </div>
                     <div class="form-group">
                        <label for="password">  Password:</label>
                        <input type="password" class="form-control" id="password" placeholder="Password" name="password" required>
                     </div>
                     <button class="btn btn-lg btn-primary btn-block" name ="submit" id="submit">Sign In</button>
                  </form>
               </div>
            </div>
         </div>
      </div>
      
      <script src="js/jquery-1.11.2.js"></script>
      <script src="js/bootstrap.min.js"></script>
      <script src="js/signup.js"></script>
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
   </body>
</html>