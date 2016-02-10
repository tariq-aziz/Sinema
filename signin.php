
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

//user is loggin in 
if(isset($_POST["submitSignin"]) && validEntry($_POST["usernameSignin"]) && validEntry($_POST["passwordSignin"])){
	$username = $_POST["usernameSignin"];
	$password = $_POST["passwordSignin"];
	
	
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
		echo "<script> alert('Incorrect username or password'); 
			   window.location = 'signin.php'; </script>";\
		
	}	

}
	//user is signing up
	if(isset($_POST["submitSignup"]) && validEntry($_POST["usernameSignup"]) && validEntry($_POST["passwordSignup"])){
		session_start();
	
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
			
			$movieSQL = "SELECT DISTINCT title FROM movies";
			$movieSQLResult = mysqli_query($connection, $movieSQL);

			while($row = mysqli_fetch_array($movieSQLResult)){
				$title = $row['title'];
				$userMovieSQL = "INSERT INTO users (username, movie_title) 
						VALUES ('$username', '$title')";
				mysqli_query($connection, $userMovieSQL);

			}

		
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


