

<?php


session_start();

$rating = $_POST['rating'];
$id = $_SESSION['username'];
$movie = $_SESSION['title'];

//include("connectServer.php");
include("connectLocal.php");

$newQuery = "UPDATE users SET rating = '$rating' WHERE username='$id' 
			AND movie_title = '$movie'";
			
mysqli_query($connection, $newQuery);

$queryAverage = "UPDATE movies SET avg_rating = (100*(SELECT AVG(rating) FROM users 
				WHERE movie_title='$movie')/5) WHERE title='$movie'";

$resultSet = mysqli_query($connection, $queryAverage);
mysqli_query($resultSet);


?>
