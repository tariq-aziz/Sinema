<?php
session_start();

//if not logged in (pressed log out then tried to access mySinema) redirect to sign-in.
//If they just close they page (or go back), SESSION exists thus can access this page with
//account that was logged in before closing.
if (!isset($_SESSION['username'])){
	header("location: signin.php");
	exit();
}

$ini_array = parse_ini_file("moviedata.ini");
$user = $_SESSION['username'];

//include("connectServer.php");
include("connectLocal.php");

$foundRecomm = array();


$recommActorsQuery = "SELECT a.movie_title, b.name FROM users a, actors b WHERE a.username = '$user' 
AND a.rating IS NULL AND a.movie_title = b.movie AND EXISTS(SELECT c.movie_title FROM users c, 
	actors d WHERE c.rating>3 and c.username=a.username AND c.movie_title = d.movie AND d.name =b.name)";

$recommActorsResult = mysqli_query($connection, $recommActorsQuery);

while($rowActors = mysqli_fetch_array($recommActorsResult)){
	$foundRecomm[] = $rowActors['movie_title'];
}

//CAN ADD THAT AVG_RATING SHOULD BE GREATER THAN 70

$recommDirectorQuery = "SELECT a.movie_title, b.director FROM users a, movies b WHERE 
	a.movie_title = b.title AND a.username = '$user' AND a.rating IS NULL AND EXISTS(SELECT 
	c.movie_title FROM users c, movies d WHERE c.movie_title = d.title AND c.username = a.username 
	AND c.rating > 3 AND d.director = b.director)";
	
$recommDirectorResult = mysqli_query($connection, $recommDirectorQuery);

while($rowDirector = mysqli_fetch_array($recommDirectorResult)){
	if(!in_array($rowDirector['title'], $foundRecomm)){
		$foundRecomm[] = $rowDirector['title'];
	}
}

$genresAllArray  = array();

$recommGenresQuery = "SELECT a.movie_title, b.genre FROM users a, movies b WHERE a.movie_title = b.title 
	AND a.username='$user' AND a.rating IS NULL AND EXISTS (SELECT c.movie_title, d.genre FROM users c, 
	movies d WHERE c.username = a.username and c.rating>3 AND c.movie_title = d.title AND d.genre = b.genre)";

$recommGenresResult = mysqli_query($connection, $recommGenresQuery);

//use same process as finding similar movies in movieDetails to recommend movies with 2 matching genres.
	while($rowGenre = mysqli_fetch_array($recommGenresResult)){
		$genresAllArray[] = $rowGenre['movie_title'];
	
	}


$genreMatchesCount = array_count_values($genresAllArray);

for($i=0; $i<sizeof($genresAllArray); $i++){
	if($genreMatchesCount[$genresAllArray[$i]]==3 && !in_array($genresAllArray[$i], $foundRecomm)){
		$foundRecomm[] = $genresAllArray[$i];
	}
}

?>

<html>

<head>
<title>mySinema</title>
<link href="Sinema.css" rel="stylesheet" type="text/css">
<link rel="SHORTCUT ICON" type="image/x-icon" href="images/Sinema.ico" />
</head>

<header>
	<?php include("header.html"); ?>
</header>

<body>
<div id="container">
<h2><?php echo "Recommended for ". $user; ?></h2>
	<?php
		
		foreach ($foundRecomm as $title) {
			$movieInfoQuery = "SELECT DISTINCT director, avg_rating FROM movies WHERE title='$title'";
			$movieInfoResult = mysqli_query($connection, $movieInfoQuery);

			while($rowInfo = mysqli_fetch_array($movieInfoResult)){
				$avgRating = $rowInfo['avg_rating'];
				$director = $rowInfo['director'];
			}

			$imgSearch = $title . ".image.url";
			$imgSource = $ini_array["$imgSearch"];
			$infoSearch = $title . ".info";
			$movieInfo = $ini_array["$infoSearch"];

			$thisGenreQuery = "SELECT genre FROM movies WHERE title='$title'";
			$thisGenreResults = mysqli_query($connection, $thisGenreQuery);
			$numRows = mysqli_num_rows($thisGenreResults);


			echo "<div class='recommended'>";
			echo "<a class='recommPosterLink' href='movieDetails.php?movie=$title'> 
				<img class='recommPoster' src='$imgSource' alt='sorry'> </a>";

			echo "<div class='recommInfo'>";
			echo "<a class='recommTitleLink' href='movieDetails.php?movie=$title'><h2 class='recommTitle'>$title</h2> </a>";
			echo "<h2 class='recommRating'>$avgRating%</h2>";
		
			echo "</div>";
			echo "<div class='leftInfoRecomm'>";
			echo "<p>Director: $director</p>";
			echo "<p>";

			$i=1;
	
			while($thisGenre = mysqli_fetch_array($thisGenreResults)){
				echo $thisGenre['genre'];

				//only the last genre should not have a comma after it
				if($i!=$numRows){
					echo ", ";
					$i++;
				} 
			}

			echo "</p>";
			echo "<p> Stars: ";

			$thisActorsQuery = "SELECT name FROM actors WHERE movie='$title'";
			$thisActorsResults = mysqli_query($connection, $thisActorsQuery);
			$numActors = mysqli_num_rows($thisActorsResults);
			$actorCount=1;
			while($thisActor = mysqli_fetch_array($thisActorsResults)){

				echo $thisActor['name'];
				//only the last actor should not have a comma after it
				if($actorCount!=$numActors){
					echo ", ";
					$actorCount++;
				} 
			}

			echo "</p>";
			echo "</div>";
			echo "<div class='rightInfoRecomm'>";
			echo "<p>$movieInfo</p>";
			echo "</div> </div>";
		}
		
	?>

	
</div>

</body>


</html>
