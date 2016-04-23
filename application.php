<?php
  session_start();
  if(!$_SESSION['loggedin']==1){
    header('Location: index.php');
  }     
  if($_SESSION['usertype'] == 'Admin'){
      header('Location: admin.php');
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
     <link href="css/mystyle.css" rel="stylesheet">
    <link href = "prettify/prettify.css" rel="stylesheet">
    <script src="https://google-code-prettify.googlecode.com/svn/loader/run_prettify.js"></script>
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
 
  </head>
  <body> 
    <div class="container-fluid">
      <div class="row options">
        <div class="col-sm-2">
         <div class="btn-group" role="group" aria-label="...">
         <a class="btn btn-sm btn-default" id="showshared">Shared with</a>
         <a class="btn btn-sm btn-default" id="showcreated">Created</a>                  
       </div>
        </div>
        <div class="col-sm-2">
          <input type="file" id="fileInput" class="hidden"/>
          <label for="fileInput" id="fileinputlabel" class="btn btn-sm btn-default">Upload file</label>
          <label for="showdownloadfile" id="showdownloadfile" href='#DownloadFile' data-toggle="modal" class="btn btn-sm btn-default">Download file</label>
        </div>
        <div class="col-sm-8">
          <button class="btn btn-sm btn-primary" a href="#RequestModal" id="requestbutton" data-toggle="modal">Request</button>
          <label class="pull-right"><label  id="signedinuser"></label><a id="signout" href="php/signout.php">(Sign out)</a></label>
      </div>
    </div>
      <div class="row">
        <div class="col-sm-2" id="NotebookNames">
          <a class="btn btn-sm btn-primary" id="addnotebookbutton" a href="#NotebookModal" data-toggle="modal">Add Notebook</a>
           <div class="btn-group" role="group" aria-label="...">
             <a class="btn btn-sm btn-default" id="notebookdesc">DESC</a>
             <a class="btn btn-sm btn-default" id="notebookasc">ASC</a>                  
          </div>
          <h2>Notebooks</h2>
            <div id="notebooks">
            </div>
        </div>
        <div class="col-sm-2" id="FileNames">
          <a class="btn btn-sm btn-primary" id="addfilebutton" a href="#FileModal" data-toggle="modal">Add File</a>
          <div class="btn-group" role="group" aria-label="...">
             <a class="btn btn-sm btn-default" id="filedesc">DESC</a>
             <a class="btn btn-sm btn-default" id="fileasc">ASC</a>                  
          </div>
          <h2>Files</h2>
          <div id="files">
          </div>
        </div>
        <div class="col-sm-8">
          <div class="row" id="dd-files">
            <pre contenteditable="true" spellcheck="false" class="prettyprint linenums" id="code">

            </pre>

          </div>
          <div class="row" id="outputarea">
            <pre id="output" spellcheck="false" contenteditable="true">
              
            </pre>
          </div>
        </div>
      </div>
    </div>

    <!--Modal for adding a new notebook -->
    <div id="NotebookModal" class="modal fade">
      <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
              <h4 class="modal-title">Add Notebook</h4>
            </div>
            <div class="modal-body">
              <div id="addnotebook" class="form">
                <div class="form-group">
                  <label for="NotebookName">Notebook Name:</label>
                  <input type="text" class="form-control" name="notebookname" placeholder="Notebook Name" id="NotebookName" required/>
                </div>
                <div class="form-group">
                  <label for="shared">Read Access (one username per line)</label>
                  <textarea class="form-control" name="sharedread" rows="5" id="read" spellcheck="false"></textarea>
                  <label for="shared">Write Access (one username per line)</label>
                  <textarea class="form-control" name="sharedwrite" rows="5" id="write" spellcheck="false"></textarea>
                </div>
                <button class="btn btn-md btn-primary" type="submit" id="submitnotebook">Add Notebook</button> 
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" name="addform" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

    <!--Modal for adding a new File-->
    <div id="FileModal" class="modal fade">
      <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
              <h4 class="modal-title">Add File</h4>
            </div>
            <div class="modal-body">
              <div id="addfile" class="form">
                <div class="form-group">
                  <label for="FileName">File Name:</label>
                  <input type="text" class="form-control" placeholder="File Name" id="FileName" required/>
                </div>
                <button class="btn btn-md btn-primary" type="submit" id="submitfile">Add File</button> 
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

    <!--modal for adding a request-->
    <div id="RequestModal" class="modal fade">
      <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
              <h4 class="modal-title">Send Request</h4>
            </div>
            <div class="modal-body">
              <div id="sendrequest" class="form" method="post">
                <div class="form-group">
                  <label for="Subject">Subject:</label>
                  <input type="text" class="form-control" placeholder="Subject" id="Subject" required/>
                </div>
                <div class="form-group">
                  <label for="message">Message:</label>
                  <textarea class="form-control" rows="5" id="Message" placeholder="Message" required></textarea>
                </div>
                <button class="btn btn-md btn-primary" type="submit" id="submitrequest">Send Request</button> 
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

    <!--Modal for editing a file name-->
    <div id="EditFileModal" class="modal fade">
       <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
              <h4 class="modal-title">Edit File</h4>
            </div>
            <div class="modal-body">
              <div id="sendrequest" class="form" method="post">
                <div class="form-group">
                  <label for="FileName">File Name:</label>
                  <input type="text" class="form-control"  id="FileNameUpdate" required/>
                </div>
                <button class="btn btn-md btn-primary" type="submit" id="savefilechanges">Save Changes</button> 
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

    <!--Modal to be displayed when incorrect values are entered-->
    <div class="modal fade" id="ErrorModal" tabindex="-1" role="dialog"  aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
              <h4>Error</h4>
            </div>
            <div class="modal-body">
              <p>Please ensure all values are entered</p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

    <!--Modal for confirming the deletion of a file or notebook-->
    <div class="modal fade" id="ConfirmDelete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog">
          <div class="modal-content">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
              </div>
              <div class="modal-body">
                  <p id='deleteitem'></p>
              </div>
              <div class="modal-footer">
                  <a class="btn btn-danger btn-ok" id='deleteconfirmed'>Delete</a>
                  <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
              </div>
          </div>
     </div>
  </div>

  <!--Modal for telling a user the delete is not allowed-->
  <div class="modal fade" id="DeleteDenied" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
                <p id='deleteitem'>I'm sorry, you do not have permission to delete this item</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Okay</button>
            </div>
        </div>
    </div>
  </div>

  <!--Modal for editing the details of a notebook-->
  <div class="modal fade" id="EditNotebook" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
              <div class="form">
                <div class="form-group">
                  <label for="NotebookNameUpdate">Notebook Name:</label>
                  <input type="text" class="form-control" name="notebooknameupdate" id="notebooknameupdate" placeholder="Notebook Name" id="NotebookName" required/>
                </div>
                <div class="form-group">
                  <label for="shared">Read Access (one username per line)</label>
                  <textarea class="form-control" name="shared" rows="5" id="readusers" spellcheck="false"></textarea>
                  <label for="shared">Write Access (one username per line)</label>
                  <textarea class="form-control" name="shared" rows="5" id="writeusers" spellcheck="false"></textarea>
                </div>
                  <button class="btn btn-md btn-primary" type="submit" id="savenotebookchanges">Submit Changes</button> 
              </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
  </div>

  <!--modal for telling the user that the username they entered is incorrect-->
  <div class="modal fade" id="Incorrectusername" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
              <p>Please ensure all usernames are entered correctly and try again. A username should also only appear once</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
  </div>

  <!--modal for informing the user their request has been received-->
  <div class="modal fade" id="MessageReceived" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
              <p>Thank You! Your message has been received!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
  </div>

  <!--modal used for the user specifying the name of the file before they download it-->
   <div id="DownloadFile" class="modal fade">
         <div class="modal-dialog">
            <div class="modal-content">
               <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                  <h4 class="modal-title">Save file content to text file</h4>
               </div>
               <div class="modal-body">
                     <div class="form-group">
                        <label for="FileName">Please specify the name and type of the file(E.G. index.html, createusers.php):</label>
                        <input type="text" class="form-control" placeholder="File Name" id="savefilename" name="savefilename" required>
                     </div>
                     <button class="btn btn-lg btn-primary btn-block" name ="submit" id="downloadfile">Download File</button>
                  
               </div>
            </div>
         </div>
      </div>

    <!--custom context menu-->
    <ul id="contextMenu" class="dropdown-menu" role="menu">
      <li><a tabindex='-1' href='#'>Edit</a></li>
      <li><a tabindex='-1' href='#'>Delete</a></li>
    </ul>
   
    <script src="js/jquery-1.11.2.js"></script>
    <script src="js/draganddrop.js"></script>
    <script src="prettify/prettify.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/application.js"></script>
    <script src="js/contextmenu.js"></script>
  </body>
</html>