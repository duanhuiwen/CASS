function takePhoto() {
	navigator.camera.getPicture(uploadPhoto,
        function(message) { alert('get picture failed'); },
            { quality: 50, 
              destinationType: navigator.camera.DestinationType.FILE_URI,
              sourceType: navigator.camera.PictureSourceType.CAMERA }
             );
}

function uploadPhoto(imageURI) {
	var options = new FileUploadOptions();
    options.fileKey="image";
    options.fileName=imageURI.substr(imageURI.lastIndexOf('/')+1);
    options.mimeType="image/jpg";
	var params = new Object();
	params.qid = "13438";
	options.params = params;
    var ft = new FileTransfer();
	ft.upload(imageURI, "http://23.23.93.172/cassVersion5.0/ul.php", succ , fail, options);

    }

function succ(r) {
    alert("Code = " + r.responseCode + " Response = " + r.response + " Sent = " + r.bytesSent);
        }

function fail(error) {
            alert("An error has occurred: Code = " = error.code);
        }
