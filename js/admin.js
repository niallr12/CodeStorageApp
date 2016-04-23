//used to get the username and display the users once the user has logged in
$(function(){  
    var adminurl = "php/adminactions.php";
    var activetable;
    
	$.post(adminurl, {change: 'getusername'}, function(result){
    	$('#signedinuser').text("You are signed in as " + result);
        $('#viewusers').click();
    })
    
    //sends ajax request to update users details
    function update_user(id, fname, lname, email, accountlocked, loginattempts){
        $.post(adminurl, {pk:'UserName', table:'users', change:'edit', primarykey:id, firstname:fname, lastname:lname, email:email, accountlocked: accountlocked, loginattempts: loginattempts});
        $('#viewusers').trigger("click");
    }

    //send ajax request to delete a user
    function delete_user(value){
        $('#target').load(adminurl, {pk:'UserName', table: "users",change: 'delete', primarykey: value});
        $('#viewusers').trigger("click");
    }

    //sends a ajax request to add a comment to a request
    function update_request(id, comment){
        $.post(adminurl, {pk:'RequestID',primarykey:id, comment:comment, change:'editrequest', table:'requests'},
            function(){
                $('#viewrequests').trigger("click");
                $('#RequestModal').modal('hide');       
        });
    }

    //sends ajax request to delete a request
    function delete_request(value){
        $('#target').load(adminurl, {pk:'RequestID', table: "requests" ,change: 'deleterequest', primarykey: value}); 
        $('#viewrequests').trigger("click");
    }




    //view users button handler
    $('#viewusers').click(function(){
        $('#target').load(adminurl, {pk:'UserName', table: "users", change:'none'} );
        activetable = 1;
    });


    //view requests button handler
    $('#viewrequests').click(function(){
        $('#target').load(adminurl, {pk:'RequestID', table: "requests", change:'none'});
        activetable = 2;
    }); 


    //dealing with delete. checks if a user or request is being deleted
    $('#target').on("click", "#delete",function(){
        var value = $(this).attr('value');
        if(activetable === 1){
            delete_user(value);
        }
        else if(activetable === 2){
        delete_request(value);
        }  
    });

    //as the user modal is loaded into the DOM, the first line of the below code is needed to pick up on the edit being
    //saved. 
    $('#target').on("click", "#edit",function(){
        var value = $(this).attr('value');
        if(activetable == 1){
            $.post(adminurl, { pk: 'UserName', table: 'users', change:'loadusermodal', primarykey: value},  
                function(result){  
                    $('body').append(result);
                    $('#modal_').modal('show');
                        $(document).on('hidden.bs.modal', function (e) {
                            $(e.target).removeData('bs.modal');
                            $('#modal_').remove();
                        });
                
            });  
            }
            else{
            var requestid = $(this).attr('value');
            $('#RequestID').val(requestid);
            $('#RequestModal').modal('show');
            }
    }); 

    //button handler to show AddAdminModal
    $('#addadmin').on("click", function(){
        $('#AddAdminModal').modal('show');
    });

    //handler for the updating users details
    $('body').on("click", '#submitchanges', function(){
        $('#target').text("");
        var id = $('#UserIDUpdate').val();
        var fname = $('#FNameUpdate').val();
        var lname = $('#LNameUpdate').val();
        var email = $('#EmailUpdate').val();
        var accountlocked = $('#Lock').val();
        var loginattempts = $('#LoginAttempts').val();
        update_user(id, fname, lname, email, accountlocked, loginattempts);
    }); 


    //button handler for adding comment to request
    $('#saverequestchanges').on("click", function(){
        var id = $('#RequestID').val();
        var comment = $("#Comment").val();
        update_request(id, comment);
    })                


    //checks values before submitting form    
    $("#signupadmin").submit(function(event){
    if($('#Password').val() != $('#conPassword').val() || userinuse == 1 || emailinuse == 1){
        event.preventDefault();
        alert("There are errors in the form, please solve these");
    }
    else{
        alert("Admin has been added");
    }
    });

});



