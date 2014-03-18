/*SAVING LOCAL STORAGE*/
function saveLocal(thisform){
	with(thisform){
		bt = bt_id.value;
	}
		localStorage.setItem('uid', bt);
}

/*RESET LOCAL STORAGE*/
function resetLocal(){
	localStorage.clear();
	document.getElementById("local").innerHTML='';
}


function loadXMLString(txt){
	if (window.DOMParser){
		parser=new DOMParser();
		xmlDoc=parser.parseFromString(txt,"text/xml");
	}else{// Internet Explorer
		xmlDoc=new ActiveXObject("Microsoft.XMLDOM");
		xmlDoc.async="false";
		xmlDoc.loadXML(txt);
	}
	return xmlDoc;
}

/*SynchronousJAX - NOT USING -  */
function loadXMLDoc(){			
	if (window.XMLHttpRequest){// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp=new XMLHttpRequest();
	}else{// code for IE6, IE5
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.open("POST","../MobileIO/XMLGen.php?uid="+localStorage.uid,false);
	xmlhttp.send();
	x = xmlhttp.responseText;
	x = x.replace(/%3F/gi,"?");	//%3F cause was for mobile client/java
	document.getElementById("xml_area").innerHTML=x;
	return (loadXMLString(x));			
}

//AJAX - callback function			
function loadXMLDocA(url,cfunc){			 
	if (window.XMLHttpRequest){// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp=new XMLHttpRequest();
	}else{// code for IE6, IE5
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.onreadystatechange=cfunc;
	xmlhttp.open("POST",url,true);
	xmlhttp.send();
}

/* SHOW SURVEY FROM XML */
function ShowSurvey(){
	loadXMLDocA("../MobileIO/XMLGen.php?uid="+localStorage.uid,function(){
		if (xmlhttp.readyState==4 && xmlhttp.status==200){
			document.getElementById("xml_area").innerHTML =	xmlhttp.responseText;	
			x = xmlhttp.responseText;
			x = x.replace(/%3F/gi,"?");	//%3F cause was for mobile client/java
			xmlDoc = loadXMLString(x);
			item = xmlDoc.getElementsByTagName("item");
			survey = '<form id="sur" method="POST" onsubmit="return validSurvey(this, xmlDoc)">';
			for (i=0;i<item.length;i++){
				switch (item[i].getAttribute("type")){
					case "1":{		//normal question
						survey = survey + "<hr><h2>" + item[i].childNodes[0].nodeValue + "</h2>";
						survey = survey + '<p><input type="text" name="item'+i+'" size="28" /></p>';
						break;
					}
					case "2":{		//open number
						survey = survey + "<hr><h2>" + item[i].childNodes[0].nodeValue + "</h2>";
						survey = survey + '<p style="color:#4e5357">(insert values between: ' + item[i].getAttribute("min") + " and " +item[i].getAttribute("max") + ")</p>";
						survey = survey + '<p><input type="text" name="item'+i+'" size="10" /></p>';
						break;
					}
					case "3":{		//Sound
						survey = survey + "<hr><h2>" + item[i].childNodes[0].nodeValue + "</h2>";
						survey = survey + '<p><input type="file" name="item'+i+'" size="10" /></p>';
						break;
					}
					case "4":{		//Radio question
						survey = survey + "<hr><h2>" + item[i].childNodes[0].nodeValue + "</h2>";
						option = item[i].getElementsByTagName("option");
						for (j=0;j<option.length;j++){
							survey = survey + '<p><input type="radio" name="item'+i+'" value="' + option[j].getAttribute("value") + '" /> ';
							survey = survey + option[j].getAttribute("value") + "</p>";
						}
						break;
					}
					case "5":{		//Super Question
						break;
					}
					case "6":{		//Comment field
						survey = survey + "<hr><h2>" + item[i].childNodes[0].nodeValue + "</h2>";
						break;
					}
					case "7":{		//Photo
						survey = survey + "<hr><h2>" + item[i].childNodes[0].nodeValue + "</h2>";
						survey = survey + '<p><input type="file" name="item'+i+'" size="10" /></p>';
						break;
					}
					case "8":{		//Video
						survey = survey + "<hr><h2>" + item[i].childNodes[0].nodeValue + "</h2>";
						survey = survey + '<p><input type="file" name="item'+i+'" size="10" /></p>';
						break;
					}
					case "9":{		//Slider question
						survey = survey + "<hr><h2>" + item[i].childNodes[0].nodeValue + "</h2>";
						if (screen.width > 350){
							survey = survey + '<div id="slid'+i+'" style="margin-left:22%"></div>';
							survey = survey + '<div style="float:left;margin-left:44%"><p>Slider value is:</p></div><div><p id="pslid'+i+'" style="text-align:left"></p></div>';
						} else {	//for HANDHELD SCREEN
							survey = survey + '<div id="slid'+i+'"style="margin-left:8%"></div>';
							survey = survey + '<div style="float:left;margin-left:22%"><p>Slider value is:</p></div><div><p id="pslid'+i+'" style="text-align:left"></p></div>';	
						}
						break;
					}
					case "10":{		//multi choice question --> to db is save id_option of the question...
						survey = survey + "<hr><h2>" + item[i].childNodes[0].nodeValue + "</h2>";
						option = item[i].getElementsByTagName("option");
						for (j=0;j<option.length;j++){
							survey = survey + '<p><input type="checkbox" name="item'+i+'" value="' + option[j].getAttribute("value") + '" /> ';
							survey = survey + option[j].getAttribute("value") + "</p>";
						}
						break;
					}
				}
			}
			survey = survey + '<hr><input type="submit" value="Send" /></form><br />';
			if (i!=0){
				document.getElementById("xml_area").innerHTML ='<h1 style="color:#4e5357">Please fill the survey:</h1>';
				document.getElementById("q").innerHTML = survey;								
			}
			//SLIDERS INIT
			var slider = new Array();
			for (i=0;i<item.length;i++)
				switch (item[i].getAttribute("type")){
					case "9":{
						slid = "slid"+i;
						pslid = "pslid"+i;
						min = parseInt(item[i].getAttribute("min"));
						max = parseInt(item[i].getAttribute("max"));
						minlabel = parseInt(item[i].getAttribute("minlabel"));
						maxlabel = parseInt(item[i].getAttribute("maxlabel"));
						if (screen.width > 350){
							slider[i] = new dhtmlxSlider(slid, 400, "ball", false, min, max);
						} else {//for HANDHELD SCREEN
							slider[i] = new dhtmlxSlider(slid, 200, "ball", false, min, max);
						}
						slider[i].setSteppingMode(true);
						slider[i].linkTo(pslid);
						slider[i].init();
						break;
					}				
				}//END SLIDER INI	
		}
	});
}

//form validation
function validSurvey(thisform, xmlDoc){		
	var msg = "";
	item = xmlDoc.getElementsByTagName("item");
	for (i=0,j=0;i<item.length;i++){
		switch (item[i].getAttribute("type")){
			case "1":{		//normal question
				if (thisform.elements[j].value == "" || thisform.elements[j].value == null){
					msg = msg + "You did not give the anserw to the question: "+(i+1)+"\n";
					thisform.elements[j].focus();
				}
				j++;
				break;
			}
			case "2":{		//open number
				min = parseInt(item[i].getAttribute("min"));
				max = parseInt(item[i].getAttribute("max"));
				if (thisform.elements[j].value == "" || thisform.elements[j].value == null){
					msg = msg + "You did not give the anserw to the question: "+(i+1)+"\n";
					thisform.elements[j].focus();
				} else {
					val = parseInt(thisform.elements[j].value);
					if ((val < min) || (val > max)){
						msg = msg + "You gave wrong value in question: "+(i+1)+"\n";
						thisform.elements[j].focus();
					}
				}
				j++;
				break;
			}
			case "3":{		//Sound
				if (thisform.elements[j].value == "" || thisform.elements[j].value == null){
				msg = msg + "You did not give the anserw to the question: "+(i+1)+"\n";
				thisform.elements[j].focus();
				}
				j++;
				break;
			}
			case "4":{		//Radio question
				option = item[i].getElementsByTagName("option");
				var ch = false;
				for (k=0;k<option.length;k++){
					if (thisform.elements[j].checked){
						ch = true;
					}
				j++;
				}
				if (ch==false){
					msg = msg + "You did not give the anserw to the question: "+(i+1)+"\n";
					thisform.elements[j].focus();
				}
				break;
			}
			case "5":{		//Super Question
				break;
			}
			case "6":{		//Comment field
				break;
			}
			case "7":{		//Photo
				if (thisform.elements[j].value == "" || thisform.elements[j].value == null){
				msg = msg + "You did not give the anserw to the question: "+(i+1)+"\n";
				thisform.elements[j].focus();
				}
				j++;
				break;
			}
			case "8":{		//Video
				if (thisform.elements[j].value == "" || thisform.elements[j].value == null){
					msg = msg + "You did not give the anserw to the question: "+(i+1)+"\n";
					thisform.elements[j].focus();
				}
				j++;
				break;
			}
			case "9":{		//Slider question
				break;
			}
			case "10":{		//multi choice question							
				option = item[i].getElementsByTagName("option");
				for (k=0;k<option.length;k++){
					j++;						
				}
				break;
			}
		}
	}
	if (msg != ""){
		alert(msg);
		return false;
	}
	sendSurvey(thisform, xmlDoc);
	return true;	
}

//making XML string and sending to server
function sendSurvey(thisform, xmlDoc){		
	surv = xmlDoc.getElementsByTagName("survey");
	item = xmlDoc.getElementsByTagName("item");

	xmlSend = "<"+"?xml version=\"1.0\" encoding=\"ISO-8859-1\"?"+">\n";
	xmlSend = xmlSend+"<surveyAnswer>\n";
	//TIMESTAMP
	var d = new Date();
	year = d.getFullYear();
	month = d.getMonth()+1;
	if (month<10){
		month = "0"+month;
	}
	day = d.getDate();
	if (day<10){
		day = "0"+day;
	}
	hour = d.getHours();
	if (hour<10){
		hour = "0"+hour;
	}
	minute = d.getMinutes();
	if (minute<10){
		minute = "0"+minute;
	}
	sec = d.getSeconds();
	if (sec<10){
		sec = "0"+sec;
	}
	timestamp = year+'-'+month+'-'+day+' '+hour+':'+minute+':'+sec;
	//END OF TIMESTAMP
	xmlSend = xmlSend+'<timestamp stamp="'+timestamp+'"/>\n';
	surveyId = surv[0].getAttribute("surveyId");
	xmlSend = xmlSend+'<surveyId id="'+surveyId+'"/>\n';
	uid = surv[0].getAttribute("uid");
	xmlSend = xmlSend+'<userName name="'+uid+'"/>\n';
					
	//ADDING ITEM NODES AND ANSWERS
	for (i=0,j=0;i<item.length;i++){
		switch (item[i].getAttribute("type")){
			case "1":{		//normal question													
				ans = thisform.elements[j].value;
				xmlSend = xmlSend+"<item q_id=\""+item[i].getAttribute("q_id")+"\" type=\""+item[i].getAttribute("type")+"\" answer=\""+ans+"\"/>\n";
				j++;
				break;
			}
			case "2":{		//open number
				ans = thisform.elements[j].value;
				xmlSend = xmlSend+"<item q_id=\""+item[i].getAttribute("q_id")+"\" type=\""+item[i].getAttribute("type")+"\" answer=\""+ans+"\"/>\n";
				j++;
				break;
			}
			case "3":{		//Sound
				ans = thisform.elements[j].value;
				xmlSend = xmlSend+"<item q_id=\""+item[i].getAttribute("q_id")+"\" type=\""+item[i].getAttribute("type")+"\" answer=\""+ans+"\"/>\n";
				j++;
				break;
			}
			case "4":{		//Radio question
				option = item[i].getElementsByTagName("option");
				var a;
				for (k=0;k<option.length;k++){
					if (thisform.elements[j].checked){
						a = k;
					}
					j++;
				}
				ans = option[a].getAttribute("o_id");
				xmlSend = xmlSend+"<item q_id=\""+item[i].getAttribute("q_id")+"\" type=\""+item[i].getAttribute("type")+"\" answer=\""+ans+"\"/>\n";
				break;
			}
			case "5":{		//Super Question
				break;
			}
			case "6":{		//Comment field
				break;
			}
			case "7":{		//Photo
				ans = thisform.elements[j].value;
				xmlSend = xmlSend+"<item q_id=\""+item[i].getAttribute("q_id")+"\" type=\""+item[i].getAttribute("type")+"\" answer=\""+ans+"\"/>\n";
				j++;
				break;
			}
			case "8":{		//Video
				ans = thisform.elements[j].value;
				xmlSend = xmlSend+"<item q_id=\""+item[i].getAttribute("q_id")+"\" type=\""+item[i].getAttribute("type")+"\" answer=\""+ans+"\"/>\n";		
				j++;
				break;
			}
			case "9":{		//Slider question
				ans = document.getElementById("pslid"+i).innerHTML;
				xmlSend = xmlSend+"<item q_id=\""+item[i].getAttribute("q_id")+"\" type=\""+item[i].getAttribute("type")+"\" answer=\""+ans+"\"/>\n";
				break;
			}
			case "10":{		//multi choice question --> to db is save id_option of the question...									
				option = item[i].getElementsByTagName("option");
				ans ="";
				for (k=0;k<option.length;k++){
					if (thisform.elements[j].checked){														
						ans = ans+option[k].getAttribute("o_id")+",";
					}
					j++;
				}
				xmlSend = xmlSend+"<item q_id=\""+item[i].getAttribute("q_id")+"\" type=\""+item[i].getAttribute("type")+"\" answer=\""+ans+"\"/>\n";
				break;
			}
		}
	}
	xmlSend = xmlSend+'</surveyAnswer>';
	//SENDING BY SJAX
	if (window.XMLHttpRequest){// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp=new XMLHttpRequest();
	}else{// code for IE6, IE5
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.open("POST","../MobileIO/mobileanswer.php?xmlSend="+xmlSend,false);
	xmlhttp.send();
	alert(xmlhttp.responseText);
}