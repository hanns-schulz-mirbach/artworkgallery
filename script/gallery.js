function getImage() {
	var request;
	request = new XMLHttpRequest();

	request.onreadystatechange = function() {
		if (request.readyState == 4 && request.status == 200) {
			document.getElementById("gallery-image").src = request.responseText;
		}
	}
	request.open("GET", "get_image.php", true);
	request.send();
}


