var CodeApp = (function($, undefined) {
    var currentnotebook = -1;        //keeps track of the current notebook the user is on
    var currentfile = -1;		     //keeps track of the current file the user is on
    var urlaction = 'php/applicationactions.php'; //url for ajax requests
    var ordernotebooks = 'DESC';     //keeps track of the notebook sort the user has currently selected
    var orderfiles = 'DESC';		 //keeps track of the file sort the user has currently selected
    var timeoutnow = 600000;         //the amount of time will takes for the user to time out when they do not click any button
    var timeouttimer;				 //the timer which keeps track of the amount of time the mouse hasn't moved. 
    var currentselect = 0;
    var select = 0;
    $('#showcreated').click();
    
    $.post(urlaction, { action: 'getusername' }, function(result) {
        $('#signedinuser').text("You are signed in as " + result);
    })

    load_notebooks(ordernotebooks);
    
    start_timer();

    //function to start the time out timer. 
    function start_timer() {
        timeouttimer = window.setTimeout("idle_timeout()", timeoutnow);
    }

    //function to reset the time out timer. 
    function reset_timer() {
        window.clearTimeout(timeouttimer);
        start_timer();
    }

    //after the user has timed out, this function is used to click the sign out button
    function idle_timeout() {
        $('#signout')[0].click();
    }

    /*this function is to load all the notebooks for the given user. the argument depends on the type of notebook loaded. 
    'created notebooks' or 'shared notebooks'*/
    function load_notebooks(select) {
        $.post('php/applicationload.php', { select: select, order: ordernotebooks }, function(result) {
            $('#notebooks').empty();
            $('#notebooks').append(result);
            $('#files').empty();
            $('#code').empty();
            $('#output').empty();
            currentnotebook = -1;
            currentfile = -1;
            $('#notebooks li:first').click();
        })

    }

    //This function loads the files for the notebook the user selects. the argument is the notebookid. 
    function load_files(id) {
        $.post(urlaction, { notebookid: id, order: orderfiles, action: 'loadfiles' }, function(result) {
            $('#code').empty();
            $('#output').empty();
            currentfile = -1;
            $('#files').empty();
            $('#files').append(result);
            $('#files li:first').click();
        })
    }

    //This function loads the specific content of the file the user selects. The argrument is the file id. 
    function load_content(id) {
        $.ajax({
            type: "POST",
            dataType: "json",
            async: true,
            url: urlaction, //Relative or absolute path to response.php file
            data: { fileid: id, action: 'loadcontent' },
            success: function(data) {
                $('#code').removeClass("prettyprinted");
                $("#code").empty();

                $('#output').html(data['output']);
                if (data['lock'] == 'true') {
                    $('#code').html(data['firstline']);
                    $('#code').append(data['content']);
                    $("#code").attr('contenteditable', 'false');
                    $("#output").attr('contenteditable', 'false');
                }
                else {
                    $('#code').append(data['content']);
                    $("#code").attr('contenteditable', 'true');
                    $("#output").attr('contenteditable', 'true');
                }
            }
        });
    }

    //Saves the code in the file to the database. 
    function save_content(content) {
        $.post(urlaction, { fileid: currentfile, notebookid: currentnotebook, content: content, action: 'savecontent' });
    }

    //Saves the output to the database. 
    function save_output(output) {
        $.post(urlaction, { fileid: currentfile, notebookid: currentnotebook, output: output, action: 'saveoutput' });
    }

    //Adds the notebook to the database.
    function add_notebook(name, userswrite, usersread) {
        $.post(urlaction, { notebookname: name, sharedwrite: userswrite, sharedread: usersread, action: 'addnotebook' },
            function(result) {
                if (jQuery.trim(result) === "error") {
                    $('#Incorrectusername').modal('toggle');
                }
                else {
                    $('#NotebookModal').modal('toggle');
                    $('#NotebookName').val('');
                    $('#shared').val('');
                    load_notebooks(currentselect);
                }
            });
    }

    //this function is used to update the details of a file(name, writeaccess, readaccess).
    function update_notebook(notebookid, notebookname, userswrite, usersread) {
        $.post(urlaction, { notebookid: notebookid, notebookname: notebookname, write: userswrite, read: usersread, action: 'updatenotebook' },
            function(result) {
                console.log(result);
                if (result == 0) {
                    $('#Incorrectusername').modal('toggle');
                }
                else {
                    $('#EditNotebook').modal('toggle');
                    $('#notebooknameupdate').val('');
                    $('#userswrite').val('');
                    $('#usersread').val('');
                    load_notebooks(currentselect);
                }
            });
    }

    //adds a file to the database. 
    function add_file(filename) {
        $.post(urlaction, { filename: filename, notebookid: currentnotebook, action: 'addfile' },
            function() {
                $('#FileModal').modal('toggle');
                load_files(currentnotebook);
                $('#code').text("");
                $('#output').text("");
                $('#FileName').val('');
            });
    }

    //updates the details of a file(name).
    function update_file(fileid, filename) {
        $.post('php/applicationactions.php', { filename: filename, fileid: fileid, action: 'updatefile' },
            function(result) {
                $('#code').html(result);
                $('#EditFileModal').modal('hide');
                load_files(currentnotebook);
            });
    }

    //adds request to the database. 
    function add_request(subject, message) {
        $.post(urlaction, { subject: subject, message: message, action: 'request' },
            function() {
                $('#RequestModal').modal('toggle');
                $('#MessageReceived').modal('toggle');
            });
    }

    //used to handle the file upload
    function file_upload() {
        var fileInput = document.getElementById('fileInput');
        var fileDisplayArea = document.getElementById('code');
        var file = fileInput.files[0];
        if (file.type.match('image.*')) {
            alert("that is not a valid file");
        }
        else if (file.type.match('video.*')) {
            alert("that is not a valid file");
        }
        else {
            var filename = file.name;
            var reader = new FileReader();

            reader.onload = function(e) {
                $('#code').text(reader.result);
                $('#code').removeClass("prettyprinted");
                PR.prettyPrint();
                var code = $('#code').html();
                code = code.replace(/^(\r\n)|(\n)/, '');
                save_content(code);
            }

            reader.readAsText(file);
        }
    }

    //this function is for when the user is downloading the contents of a file
    function savetextasfile(filenametosave) {

        var codetosave = document.getElementById('code').innerText;
        var textFileAsBlob = new Blob([codetosave], { type: 'text/plain' });
        var fileNameToSaveAs = filenametosave;
        var downloadLink = document.createElement("a");


        downloadLink.download = fileNameToSaveAs;
        downloadLink.innerHTML = "Link for download";
        window.URL = window.URL || window.webkitURL;
        downloadLink.href = window.URL.createObjectURL(textFileAsBlob);
        downloadLink.onclick = destroyclickedelement;
        downloadLink.style.display = "none";
        document.body.appendChild(downloadLink);
        downloadLink.click();
    }

    //this removes the link from the DOM once the user has downloaded the file. 
    function destroyclickedelement(event) {
        // remove the link from the DOM
        document.body.removeChild(event.target);
    }



    //click handler for the download file button
    $('#downloadfile').on("click", function() {
        var savefilename = $('#savefilename').val()
        if (savefilename == '') {
            $('#ErrorModal').modal('toggle');
            return;
        }
        savetextasfile(savefilename);
        $('#DownloadFile').modal('toggle');

    })


    //this is used to reset the logout timer when the user moves the mouse. 
    $(document).on('keyup keypress blur change mousemove', function() {
        reset_timer();
    });



    //this is the click hander for when a user submits notebook. The values are validated before the add_notebook function is called 
    $('#submitnotebook').on("click", function() {
        var name = $('#NotebookName').val();
        var userswrite = $.trim($("#write").val()).toLowerCase().split(/\n/g);
        var usersread = $.trim($("#read").val()).toLowerCase().split(/\n/g);

        if ($('#write').val() != "" || $('#read').val() != "") {
            var allusers = userswrite.concat(usersread).sort();
            for (var i = 0; i < allusers.length - 1; i++) {
                if (allusers[i + 1] == allusers[i]) {
                    $('#Incorrectusername').modal('toggle');
                    return;
                }
            }
        }
        if (name == '') {
            $('#ErrorModal').modal('toggle');
            return;
        }

        add_notebook(name, userswrite, usersread);
    })

    //click handler for submit request
    $('#submitrequest').on("click", function() {
        var subject = $('#Subject').val();
        var message = $("#Message").val();
        if (subject == '' || message == '') {
            $('#ErrorModal').modal('toggle');
            return;
        }
        add_request(subject, message);

    })

    //click handler for submit file
    $('#submitfile').on("click", function() {
        var filename = $('#FileName').val();
        if (filename == '') {
            $('#ErrorModal').modal('toggle');
            return;
        }
        add_file(filename);

    })


    //click handler for when the name of a file is being updated
    $('#savefilechanges').on("click", function() {
        var filename = $('#FileNameUpdate').val();
        var fileid = $('#FileNameUpdate').attr('value');
        if (filename == '') {
            $('#ErrorModal').modal('toggle');
            return;
        }

        update_file(fileid, filename);
    })

    //click handler for when the details of a notebook are being updated
    $('#savenotebookchanges').on("click", function() {
        var notebookid = $('#notebooknameupdate').attr('value');
        var notebookname = $('#notebooknameupdate').val();
        var sharedwrite = $.trim($('#writeusers').val());
        var userswrite = sharedwrite.toLowerCase().split(/\n/g);

        var sharedread = $.trim($('#readusers').val());
        var usersread = sharedread.toLowerCase().split(/\n/g);

        if ($('#writeusers').val() != "" || $('#readusers').val() != "") {
            var allusers = userswrite.concat(usersread).sort();
            for (var i = 0; i < allusers.length - 1; i++) {
                if (allusers[i + 1] == allusers[i]) {
                    $('#Incorrectusername').modal('toggle');
                    return;
                }
            }
        }

        update_notebook(notebookid, notebookname, userswrite, usersread);
    })

    //click handler for showing shared notebooks
    $('#showshared').on("click", function() {
        load_notebooks(1);
        currentselect = 1;
        $('#showcreated').removeClass('activebutton');
        $('#showshared').addClass('activebutton');

    });

    //click handler for showing created notebooks
    $('#showcreated').on("click", function() {
        load_notebooks(0);
        currentselect = 0;
        $('#showcreated').addClass('activebutton');
        $('#showshared').removeClass('activebutton');

    });

    //click handler for showing notebooks in ascending order
    $('#notebookasc').on("click", function() {
        ordernotebooks = "ASC";
        load_notebooks(currentselect);
        $('#notebookasc').addClass('activebutton');
        $('#notebookdesc').removeClass('activebutton');

    });

    //click handler for showing notebooks in descending order
    $('#notebookdesc').on("click", function() {
        ordernotebooks = "DESC";
        load_notebooks(currentselect);
        $('#notebookdesc').addClass('activebutton');
        $('#notebookasc').removeClass('activebutton');

    });

    //click handler for showing files in ascending order
    $('#fileasc').on("click", function() {
        orderfiles = "ASC";
        load_files(currentnotebook);
        $('#fileasc').addClass('activebutton');
        $('#filedesc').removeClass('activebutton');

    });

    //click handler for showing files in descending order
    $('#filedesc').on("click", function() {
        orderfiles = "DESC";
        load_files(currentnotebook);
        $('#filedesc').addClass('activebutton');
        $('#fileasc').removeClass('activebutton');

    });

    //click handler for when a user clicks a notebook
    $('#notebooks').on("click", "#loadfiles li", function() {
        var id = $(this).attr('value');
        $("li").removeClass('activenotebook');
        $(this).addClass('activenotebook');
        currentnotebook = id;
        load_files(id);
    });

    //click handler for when a user clicks a file
    $('#files').on("click", "#loadcontent li", function() {
        var id = $(this).attr('value');
        $("li").removeClass('activefile');
        $(this).addClass('activefile');
        currentfile = id;
        load_content(id);
        reset_timer();
    });

    //click handler for when a user is uploading a file
    $('#fileInput').on("change", function() {
        file_upload();
    })

    //each time the user presses a key in the code area, the contents are saved to the database
    $('#code').keyup(function() {
        var code = $('#code').html();
        code = code.replace(/^(\r\n)|(\n)/, '');
        save_content(code);
    });

    //when the user pastes code into code area, it needs to be handled
    $('#code').on('paste', function() {
        $('#code').empty();
        setTimeout(function() {
            $('#code').removeClass("prettyprinted");
            PR.prettyPrint();
            var code = $('#code').html();
            code = code.replace(/^(\r\n)|(\n)/, '');
            save_content(code);
        }, 100);
    });


    //calls save_output function when a key is pressed in the output area
    $('#output').keyup(function() {
        var output = $('#output').html();
        output = output.replace(/^(\r\n)|(\n)/, '');
        save_output(output);
    });

    return {
        loadnotebooks: load_notebooks,
        loadfiles: load_files, 
        notebookid: function(){ return notebookid; },
        fileid: function(){ return fileid; },
        currentnotebook: function(){ return currentnotebook; },
        currentfile: function(){ return currentfile; }, 
        currentselect: function(){ return currentselect; },
        urlaction: function(){return urlaction; }
    }
})(jQuery);















