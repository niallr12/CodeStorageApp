
(function ($, window) {
    var id;     //holds the file or notebook id
    var type;   //holds the type i.e. file or notebook
    var urlaction = CodeApp.urlaction();
    $.fn.contextMenu = function (settings) {

        return this.each(function () {

            // Open context menu
            $(this).on("contextmenu", function (e) {
                // return native menu if pressing control
                if (e.ctrlKey) return;
                
                //open menu
                $(settings.menuSelector)
                    .data("invokedOn", $(e.target))
                    .show()
                    .css({
                        position: "absolute",
                        left: getLeftLocation(e),
                        top: getTopLocation(e)
                    })
                    .off('click')
                    .on('click', function (e) {
                        $(this).hide();
                
                        var $invokedOn = $(this).data("invokedOn");
                        var $selectedMenu = $(e.target);
                        if($invokedOn.attr("value") == undefined) return;
                        settings.menuSelected.call(this, $invokedOn, $selectedMenu);
                });
                
                return false;
            });

            //make sure menu closes on any click
            $(document).click(function () {
                $(settings.menuSelector).hide();
            });
        });

        function getLeftLocation(e) {
            var mouseWidth = e.pageX;
            var pageWidth = $(window).width();
            var menuWidth = $(settings.menuSelector).width();
            
            // opening menu would pass the side of the page
            if (mouseWidth + menuWidth > pageWidth &&
                menuWidth < mouseWidth) {
                return mouseWidth - menuWidth;
            } 
            return mouseWidth;
        }        
        
        function getTopLocation(e) {
            var mouseHeight = e.pageY;
            var pageHeight = $(window).height();
            var menuHeight = $(settings.menuSelector).height();

            // opening menu would pass the bottom of the page
            if (mouseHeight + menuHeight > pageHeight &&
                menuHeight < mouseHeight) {
                return mouseHeight - menuHeight;
            } 
            return mouseHeight;
        }

    };
    //deals with when the context menu is opened on a notebook
    $("#notebooks").contextMenu({
        menuSelector: "#contextMenu",
        menuSelected: function (invokedOn, selectedMenu) {
           doNotebook(selectedMenu.text(), invokedOn.attr('value'), invokedOn.text());
        }
    });

    //deals with when the context menu is opened on a file
    $("#files").contextMenu({
        menuSelector: "#contextMenu",
        menuSelected: function (invokedOn, selectedMenu) {
            doFile(selectedMenu.text(), invokedOn.attr('value'), invokedOn.text());
        }
    });

    //used for when the user clicks a context menu item on a notebook
    function doNotebook(actiontype, notebookid, notebookname){
        if(actiontype == 'Delete'){
            var deleteitem = 'Are you sure you want to delete notebook ' + notebookname;
            $('#deleteitem').html(deleteitem);
            id = notebookid;
            type = 'deletenotebook';
            $('#ConfirmDelete').modal('toggle'); 
        }
        else{
            $('#notebooknameupdate').val(notebookname);
            $('#notebooknameupdate').attr('value', notebookid);
            $.ajax({
                type: "POST",
                dataType: "json",
                async: true,
                url: urlaction, 
                data: {notebookid:notebookid, action:'getaccess'},
                success: function(data) {
                    $('#writeusers').val(data['writeaccess']);
                    $('#readusers').val(data['readaccess']);
                    $('#EditNotebook').modal('show');
                }
            }); 
        }
        
    }

    function removenotebook(actiontype, notebookid, notebookname){
        if(actiontype == "Edit"){
            return;
        }
        var deleteitem = 'Are you sure you no longer want this notebook shared with you: ' + notebookname;
        $('#deleteitem').html(deleteitem);
        id = notebookid;
        type = 'removenotebook';
        $('#ConfirmDelete').modal('toggle');

    }

    //used for when the user clicks a context menu item on a file
    function doFile(actiontype, fileid, filename){
        if(actiontype == 'Delete'){
            var deleteitem = 'Are you sure you want to delete file ' + filename;
            $('#deleteitem').html(deleteitem);
            id = fileid;
            type = 'deletefile';
            $('#ConfirmDelete').modal('toggle');
            

            $('#deleteconfirmed').on("click", function(){
            confirmDelete(fileid, 'deletefile');  
            });
        }else{
            $('#FileNameUpdate').val(filename);
            $('#FileNameUpdate').attr('value', fileid);
            $('#EditFileModal').modal('show');
        }
        
    }

    //when the user deletes a notebook or file, this function is to delete the item. 
    //This function deals with file delete and notebook delete
    function confirmDelete(){
        if(type == 'deletefile'){
            $.post('php/applicationactions.php', {fileid:id, action:'deletefile'},
                function(result){
                    if (result === 0){
                        $('#ConfirmDelete').modal('hide');
                        $('#DeleteDenied').modal('toggle');
                    }else{
                        $('#ConfirmDelete').modal('hide');
                        CodeApp.loadfiles(CodeApp.currentnotebook);
                    }  
                });
        }
        else if(type == 'deletenotebook'){
            $.post('php/applicationactions.php', {notebookid:id, action:'deletenotebook'},
                function(result){
                    if(result == 0){
                        $('#ConfirmDelete').modal('toggle');
                        $('#DeleteDenied').modal('toggle');
                    }else{;
                        $('#ConfirmDelete').modal('hide');
                        CodeApp.loadnotebooks(CodeApp.currentselect);
                    }
                    
                });
        }
        else if(type == 'removenotebook'){
            $.post('php/applicationactions.php', {notebookid:id, action:'removenotebook'},
                function(){
                    $('#ConfirmDelete').modal('hide');
                    load_notebooks(CodeApp.currentselect);  
            });
        }       
    }


    //user confirms delete and confirmDelete function is called
    $('#deleteconfirmed').on("click", function(){
        confirmDelete();  
    });
})(jQuery, window);

