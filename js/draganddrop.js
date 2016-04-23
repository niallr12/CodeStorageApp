$(function(){
    var dropZoneTwo = document.querySelector('#dd-files'); //specifies the drop zone
	var fileContentPane = document.querySelector('#code'); //specifies where the contents of a the file will be placed

	// Event Listener for when the dragged file is over the drop zone.
	dropZoneTwo.addEventListener('dragover', function(e) {
	  if (e.preventDefault) e.preventDefault(); 
	  if (e.stopPropagation) e.stopPropagation();
	  $("#code").css("background-color","#D9E8BC");
	  e.dataTransfer.dropEffect = 'copy';
	});

	// Event Listener for when the dragged file enters the drop zone.
	dropZoneTwo.addEventListener('dragenter', function(e) {
	  
	});

	// Event Listener for when the dragged file leaves the drop zone.
	dropZoneTwo.addEventListener('dragleave', function(e) {
	  $("#code").css("background-color","white");
	});

	// Event Listener for when the dragged file dropped in the drop zone.
	dropZoneTwo.addEventListener('drop', function(e) {
	  if (e.preventDefault) e.preventDefault(); 
	  if (e.stopPropagation) e.stopPropagation();
	  $("#code").css("background-color","white");
	  var file = e.dataTransfer.files;  
	  if (file[0].type.match('image.*')) {
	  	alert("that is not a valid file");
	  }
	  else if(file[0].type.match('video.*')){
	  	alert("that is not a valid file");
	  }
	  else{
	  	readTextFile(file[0]);
	  	
	  }
	  
	});

	// Read the contents of a file and calls the save_content function to save the code 
	function readTextFile(file) {
	  var reader = new FileReader();

	  reader.onloadend = function(e) {
	    if (e.target.readyState == FileReader.DONE) {
	      var content = reader.result;
	      $('#code').text(content);
	      $('#code').removeClass("prettyprinted");
	  	  PR.prettyPrint();
	  	  var code = $('#code').html();
    	  code=code.replace(/^(\r\n)|(\n)/,'');
		  save_content(code);
	    }
	  }

	  reader.readAsBinaryString(file);

	}

    
});
	

    
