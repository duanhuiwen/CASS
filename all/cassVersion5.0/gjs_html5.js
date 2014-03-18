var r = new XMLHttpRequest();

function addSpaces(st) {
		var stLength = st.length;
		for (i = 0; i < 128 - stLength; i++) {
			st = st+" ";
		}
		return st;
	}

function handleFiles(files) {
	var file; 
	for (var i = 0, numFiles = files.length; i < numFiles; i++) {	
		file = files[i];
		var reader = new FileReader();
		reader.onload = function(e) {
			document.getElementById("myimg").setAttribute("src",e.target.result);
       }
       reader.readAsDataURL(file);
	}
//	sendXml();
//	sendArray();
	sendMedia(file);
}

function sendXml()
	{
		r.onreadystatechange = sendServerResp;

        var dat = '<?xml version="1.0" encoding="ISO-8859-1"?>';
        dat = dat+'<surveyAnswer>';
        dat = dat+'<timestamp stamp="2012-08-13 19:36:13"/>';
        dat = dat+'<surveyId id="301"/>';
        dat = dat+'<userName name="775"/>';
        dat = dat+'<item q_id="13438" type="7" answer="kuva.png"/>';
        dat = dat+'</surveyAnswer>';
		alert(dat);
		r.open("post", "http://23.23.93.172/cassVersion5.0/MobileIO/mobileanswer.php", true);
		r.setRequestHeader("Content-type","text/xml; charset=ISO-8859-1");
		r.send(dat);
	}

function sendServerResp() { 
	if (r.readyState==4) {	
		if (r.status==200) {
			var serverStr=r.responseText;
			alert("server: "+serverStr);
		}
		else {
			alert("Problem retrieving data:" + r.statusText); 
		}
	} 
}
	
function sendArray()
	{

	var str="kuva.png";
	str = str+";";
	var quid="13438";
	str = str + quid;
	var initStr = addSpaces(str);
	alert(initStr);
	var startArray = new Uint8Array(str.length);
	startArray = TextEncoder("utf-8").encode(initStr);
	alert(startArray.length);
	
	var xhr = new XMLHttpRequest();
	xhr.open("post", "http://23.23.93.172/cassVersion5.0/MobileIO/mobileMediaFiles.php", true);
	xhr.onload = function(e) {
  			if (this.status == 200) {
				alert(this.response);
			}
  
	};
	xhr.send(startArray.buffer);
}

function sendMedia(f) {
	var xr = new XMLHttpRequest();
	var fd = new FormData();
	fd.append('qid', '13482');
	fd.append('image', f);
	xr.open("post", "http://23.23.93.172/cassVersion5.0/ul.php", true);
	xr.onload = function(e) {
  			if (this.status == 200) {
				alert(this.response);
			}
  
	};

  // Listen to the upload progress.
	var progressBar = document.querySelector('progress');
	xr.upload.onprogress = function(e) {
		if (e.lengthComputable) {
			progressBar.value = (e.loaded / e.total) * 100;
			progressBar.textContent = progressBar.value; // Fallback for unsupported browsers.
		}
	};
	xr.send(fd);
}

