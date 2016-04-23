<?php
   session_start();
   if(!$_SESSION['loggedin']==1){
     header( 'Location: index.php');
   }
   
   if($_SESSION['usertype'] == 'User'){
     header('Location: application.php');
   }
       
?>
<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title>CodeShare</title>
      <link href="css/bootstrap.css" rel="stylesheet">
      <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
      <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
      <![endif]-->
   </head>
   <body>
      <div class="container-fluid">
         <div class="row">
            <div class = "col-md-12">
               <label class="pull-right"><label  id="signedinuser"></label><a href="php/signout.php">(Sign out)</a></label>
            </div>
         </div>
         <div class="row">
            <div class="col-md-2">
               <a class="btn btn-primary btn-md" id="viewusers">View Users</a>
               <a class="btn btn-success btn-md" id="viewrequests">View Requests</a>
               <a class="btn btn-success btn-md" id="addadmin">Add Admin</a>
            </div>
            <div id="target" class="col-md-10">
            </div>
         </div>
      </div>

      <!--modal for adding a comment to a request-->
      <div id='RequestModal' class='modal fade'>
         <div class='modal-dialog'>
            <div class='modal-content'>
               <div class='modal-header'>
                  <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                  <h4 class='modal-title'>Edit Request</h4>
               </div>
               <div class='modal-body'>
                  <div class='form-group'>
                     <label for='RequestID'>Request ID:</label>
                     <input type='text' class='form-control'  id='RequestID' disabled required/>
                  </div>
                  <div class='form-group'>
                     <label for='Comment'>Comment:</label>
                     <input type='text' class='form-control'  id='Comment' required/>
                  </div>
                  <button class='btn btn-md btn-primary' id='saverequestchanges' data-dismiss='modal'>Save Changes</button> 
               </div>
               <div class='modal-footer'>
                  <button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
               </div>
            </div>
         </div>
      </div>

      <!--modal for adding a new admin-->
      <div id='AddAdminModal' class='modal fade'>
         <div class="modal-dialog">
               <div class="modal-content">
                  <div class="modal-header">
                     <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                     <h4 class="modal-title">Add Admin</h4>
                  </div>
                  <div class="modal-body">
                     <form class="form" id="signupadmin" method="post" autocomplete="off" action ="<?php echo 'php/signup.php';?>">
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
                        <button class="btn btn-lg btn-primary btn-block" type="submit" name="submit" id="submit">Add admin</button>
                     </form>
                  </div>
               </div>
         </div>
      </div>

      <script src="js/jquery-1.11.2.js"></script>
      <script src="js/signup.js"></script>
      <script src="js/admin.js"></script>
      <script src="js/bootstrap.min.js"></script>
   </body>
</html>