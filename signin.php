
<?php
	session_start();

?>

<html>
<head>
<title>Sinema</title>
<link href="SinemaPlan.css" rel="stylesheet" type="text/css">
<link rel="SHORTCUT ICON" type="image/x-icon" href="images/Sinema.ico" />
</head>

<body>
<div class="backgroundImage"></div>

<div id="logContainer">

	<img id = "signinLogo" src="images/SinemaLogo.png">

	<div id="signinContainer">
		<form class="loginForm" action="signin.php" method = "post">
		<input type="text" name="usernameSignin" placeholder="username" ><br>

		<input type="password" name="passwordSignin" placeholder="password" ><br>
		<input type="submit" value="Log in" name="submitSignin" >
	</form>
	
	<h1 id="signUpPrompt">Sin-em-up</h1>
	<form class="loginForm" action = "signin.php" method = "post">
	<input type="text" name="usernameSignup" placeholder="desired username"><br>
	<input type="password" name="passwordSignup" placeholder="desired password"><br>
	<input type="submit" value="Sign up" name="submitSignup">
	</form>
	
	</div>

</div>
</body>
</html>

<?php
	/**
	* To combine signin.html into the .php, the .php file must only check values of POST if 
	* they exist (using isset method in if statements), otherwise program will use blank forms
	* and show alert
	*/

	function validEntry ($string) { 
		if(!ctype_alnum($string)){
			echo "<script> alert('Fields may only contain letters and numbers'); 
				window.location = 'signin.php'; </script>";
			return false;

		}
    	else{
    		return true;
    	}
	}


if(isset($_POST["submitSignin"]) && validEntry($_POST["usernameSignin"]) && validEntry($_POST["passwordSignin"])){
	$username = $_POST["usernameSignin"];
	$password = $_POST["passwordSignin"];
	
	
	$incorrect = false;
	
	//include("connectServer.php");
	include("connectLocal.php");


	$sql = "SELECT * FROM login WHERE username='$username' and password = '$password'";
	
	$result = mysqli_query($connection, $sql);

	$row = mysqli_fetch_array($result);


	$count = mysqli_num_rows($result);
	
	
	if($count>0){	

		session_start();
		$_SESSION['username'] = $username;
		
		echo "<script>window.location = 'index.php'</script>";
	
	}
	
	
	else{
		//its using empty forms and showing no match, therefore alerting
		//THIS IS CORRECT REASON because the .php GETS whenever it is called (ie when the
		//.html first calls it when the form is submitted, and here after alert as well)
		//READ TOP COMMENT if combining .html file
		
		echo "<script> alert('Incorrect username or password'); 
			   window.location = 'signin.php'; </script>";
			   
			   //MAKE SURE USER ENTERRED SOMETHING, OTHERWISE CAN SIGNUP & LOG IN WITH
			   //NO VALUES FOR USERNAME OR PASSWORD.
		
		
	}	

}

	if(isset($_POST["submitSignup"]) && validEntry($_POST["usernameSignup"]) && validEntry($_POST["passwordSignup"])){
		session_start();

		//$file = fopen("movieTitles.txt", "r") or die("Unable to read file!");
		//$file = fopen("dataImport.txt", "r") or die("Unable to read dataImport file");
	
		$username = $_POST["usernameSignup"];
		$password = $_POST["passwordSignup"];
		
		//include("connectServer.php");
		include("connectLocal.php");
	
		$sqlCheck = "SELECT * FROM login WHERE username='$username'";
		$result = mysqli_query($connection, $sqlCheck);
		$count = mysqli_num_rows($result);
	
		if($count>0){
			echo "<script> alert('Username is not available'); 
				   window.location = 'signin.php'; </script>";
		}
		

		else{
			$sql = "INSERT INTO login (username, password) 
				VALUES('$username', '$password')";
			//$temp = " ";
			
			$movieSQL = "SELECT DISTINCT title FROM movies";
			$movieSQLResult = mysqli_query($connection, $movieSQL);

			while($row = mysqli_fetch_array($movieSQLResult)){
				$title = $row['title'];
				$userMovieSQL = "INSERT INTO users (username, movie_title) 
						VALUES ('$username', '$title')";
				mysqli_query($connection, $userMovieSQL);

			}

			/*
			while($line = trim(fgets($file))){
				list($title, $genre, $director, $star_1,$star_2,$star3,$date) = explode(";", $line);
				//don't insert the same title twice into new_users 
				if($title!=$temp){
					$movieUpdateQuery = "INSERT INTO users (username, movie_title) 
						VALUES ('$username', '$title')";
					mysqli_query($connection, $movieUpdateQuery);
				}
				//use temp to test if the next line is the same movie with different genre 
				//(don't insert if that is the case)
				$temp = $title;
			}
			*/

		
			if(mysqli_query($connection, $sql)){
				$_SESSION['username'] = $username;
			
				echo "<script>window.location = 'index.php'</script>";
			}
	
			else{
				echo "Error";
			}
		
	}	
}



?>


