function rate(y){
	var x = new XMLHttpRequest();
	x.open("POST", "rate.php", true);
	x.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	x.send("rating=" + y);
	
	x.onreadystatechange=function() {
  		if (x.readyState==4 && x.status==200){
   			alert("Processed");	
    }	
  }


}
function yellow(x){
	var elem;
	
	//the first img has id = 1, NOT 0
	for(i=x; i>=1; i--){
		elem = document.getElementById(i);
		elem.src="images/yellow.png";
	}
}

function white(z){
	var elem;
	
	for(j=z; j<=5; j++){
		elem = document.getElementById(j);
		elem.src="images/white.png";
	}

}

function initialRating(rating){
	var elem;

	for(i=5; i>=1; i--){
		elem = document.getElementById(i);
		if(i>rating){
			elem.src="images/white.png";
		}

		else{
			elem.src="yellow/white.png";
		}
	}


}
