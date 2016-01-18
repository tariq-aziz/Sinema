

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

//MUST USE BACKTICKS for columns that have spaces (Hunger Games)!
//$query = "UPDATE users SET `$movie` = '$rating' WHERE user_id='$id'";

//mysqli_query($connection, $query);

//MUST USE BACKTICKS for columns that have spaces!
//$queryRatings = "SELECT * FROM users WHERE `$movie` IS NOT NULL";
$queryAverage = "UPDATE movies SET avg_rating = (100*(SELECT AVG(rating) FROM users 
				WHERE movie_title='$movie')/5) WHERE title='$movie'";

$resultSet = mysqli_query($connection, $queryAverage);
mysqli_query($resultSet);

//$resultSet = mysqli_query($connection, $queryRatings);
/*
$total=0;
$numRatings = mysqli_num_rows($resultSet);


$average=0;
$averageRounded=0;

while($row = mysqli_fetch_array($resultSet)){
	//use double quotes to include a variable in quotations (OR CAN JUST HAVE NO QUOTES)
	//  --> DONT use single quotes! (those are for column names that you have; NOT VARIABLES)
	$total+=$row["$movie"];
}
	
$average = $total/$numRatings;
$averageRounded = round($average, 1);

$setAverage = "UPDATE movies SET average='$averageRounded' WHERE title='$movie'";
mysqli_query($connection, $setAverage);
*/

?>
