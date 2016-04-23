
$(document).ready(function() {  
        var userinuse;  //specifies whether the username is in use
        var emailinuse; //specifies whether the email is in use
        var url = 'php/signup.php';
        //when button is clicked  
        $('#username').keyup(function(){  
        	if($('#username').val().length<5){
        		$("#usernameavailable").html("User name must be greater than 5 characters");
        }
        	else{
               check_availability();
            }   
        }
        );

        $('#Email').keyup(function(){
            check_email_inuse();
        }
        );

        $('#conPassword').keyup(function(){
        	check_password_match();
        }
        );
        
         
        //function to check username availability  
        function check_availability(){  
        
                //get the username  
                var username = $('#username').val();  
        
                //use ajax to run the check  
                $.post(url, { username: username, checkusername:'checkusername'},  
                    function(result){  
                        //if the result is 1  
                        if(result == 1){  
                            //show that the username is available  
                            $('#usernameavailable').html(username + ' is Available');
                            userinuse = 0;  
                        }else{  
                            //show that the username is NOT available  
                            $('#usernameavailable').html(username + ' is not Available');
                            userinuse = 1; 

                        }  
                });  
        
        }

        //checks to see that the password and confirm password textboxes contain the same values
        function check_password_match(){
            if($('#Password').val() != $('#conPassword').val()){
                $('#passwordmatch').html("passwords do not match");

            }
            else{
                $('#passwordmatch').html("passwords match");
            }
        }

        //checks to see if the email is in use
        function check_email_inuse(){
            var email = $("#Email").val();

            $.post(url, { email: email, checkemail: 'checkemail'},  
                function(result){  
                    //if the result is 1
                    if(result == 1){  
                        //show that the username is available  
                        $('#emailavailable').html(email + ' is Available');
                        emailinuse = 0;  
                    }else{  
                        //show that the username is NOT available  
                        $('#emailavailable').html(email + ' is not Available');
                        emailinuse = 1;  
                    }  
            });  
        }

        //on submitting the sign up form, the values are first checked to ensure they are correct
        $("#signup").submit(function(event){
        if($('#Password').val() != $('#conPassword').val() || userinuse == 1 || emailinuse == 1){
            event.preventDefault();
            alert("There are errors in the form, please solve these");
        }
        else{
            alert("Thank you for registering, please log in.");
        }
        });

});  
 