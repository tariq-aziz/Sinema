<?php

session_start();

$movie = $_GET['movie'];
$_SESSION['title'] = $movie;

$user = $_SESSION['username'];

include ("/home3/tariqaziz/private/connectServer.php");
//include("connectLocal.php");


	$ini_array = parse_ini_file("moviedata.ini");	

	$trailerURL = $movie . ".trailer.url";
	$trailerSource = $ini_array["$trailerURL"];

	$imageURL = $movie . ".image.url";
	$imgSource = $ini_array["$imageURL"];
	$audienceString = $movie . ".audience";
	$audience = $ini_array["$audienceString"];
	$runtime = $ini_array[$movie. ".runtime"];
	$releaseDate = $ini_array[$movie. ".date"];
	$summary = $ini_array[$movie. ".info"];
	
	$fixedSummary = addslashes($summary);

	$myRatingQuery = "SELECT DISTINCT rating FROM users WHERE username='$user' AND movie_title='$movie'";
	$myRatingResult = mysqli_query($connection, $myRatingQuery);

	while($rowRating = mysqli_fetch_array($myRatingResult)){
		$myRating = $rowRating['rating'];
	}

	$infoQuery = "SELECT DISTINCT avg_rating, director, genre FROM movies WHERE title='$movie'";
	$infoResult = mysqli_query($connection, $infoQuery);

	$thisGenres = array();
	$thisActors = array();
    //$actorsCount = 0;
	while($row = mysqli_fetch_array($infoResult)){
		$avgRating = $row['avg_rating'];
		$director = $row['director'];
		$thisGenres[] = $row['genre'];
		//enter values into actorArray once (otherwise will repeat)
		//if($actorsCount<1){
		//	array_push($thisActors, $row['star_1'],$row['star_2'],$row['star_3']);
			$actorsCount++;
		//}
	}

	$actorsQuery = "SELECT name FROM actors WHERE movie='$movie'";
	$actorsResult = mysqli_query($connection, $actorsQuery);
	while($row = mysqli_fetch_array($actorsResult)){
		$thisActors[] = $row['name'];
	}
		//use this to determine director similar movies
		//$similarDirector = "SELECT title FROM new_movies WHERE director = 'Christopher Nolan'":
		
		$similarQuery = "SELECT title FROM movies WHERE genre IN (SELECT genre FROM movies
						WHERE title = '$movie') AND title != '$movie'";

		$similarResult = mysqli_query($connection, $similarQuery);
		$similarArray  = array();

		while($row = mysqli_fetch_array($similarResult)){
			$similarArray[] = $row['title'];

		}
		//when a match is found, put the title in foundArray. If you find the same title again 
		// (would only happen if it shares 3 genres with the movieDetails movie), check if the title
		//is in the foundArray. If it's already there, don't add it again. This guarantees no repeated similar movies

		$foundSimilar = [];
		
		for($i=0; $i<(count($similarArray)-1); $i++){

			for($j=($i+1); $j<count($similarArray); $j++){

				if($similarArray[$i] == $similarArray[$j] && !in_array($similarArray[$i], $foundSimilar)){ 
					$foundSimilar[] = $similarArray[$i];
				}
			}
		}

		//ADD FUNCTIONALITY TO FILL 5 VALUES WITH 1 GENRE MATCH, ALSO USE DIRECTORS ETC.

?>

<html>

<head>
<title><?php echo $movie ?></title>
<link href="SinemaPlan.css" rel="stylesheet" type="text/css">
<link rel="SHORTCUT ICON" type="image/x-icon" href="images/Sinema.ico" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flickity/1.1.0/flickity.css">
<script src="http://code.jquery.com/jquery-1.10.2.js"></script>
<script src="http://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
<script src = "stars.js"> </script>
</head>


<header>
	<?php include("header.html"); ?>
</header>

<body>


<div id="container">
	<div id="totalDetails">

		<div id="moviePoster">
			 <?php
			 echo "<img src=' ".$imgSource." '> </div>";

		echo "<div id='movieInfo'>";
			echo "<div id='movieIntro'>";
				echo "<h2 id='movieTitle'>$movie</h2>";	
				echo "<ul>";
					echo "<li>$audience</li>";
					echo "<li>$runtime</li>";
					echo "<li>";
					for($i=0;$i<count($thisGenres); $i++){
						echo $thisGenres[$i]." ";
					}
					echo "</li>";
					echo "<li>$releaseDate</li>";
				echo "</ul>";
			echo "</div>";

			echo "<div id='movieRatings'>";

				echo "<div id='avgRating'>Average Rating: $avgRating%</div>";
				?>

				<div id="userRatingText">Rate this movie:</div>

				<ul id="userRatingStars">

				<?php   
				for($i=1; $i<=5; $i++){	
					if($i>$myRating){
						$defaultStar="images/white.png";
					}
					else{
						$defaultStar="images/yellow.png";
					}

					echo "<li><img class='star' id='$i' onclick = 'rate(this.id)' 
						onmouseover='yellow($i)' onmouseout='white($i)' src='$defaultStar' alt='sorry'> </li>";		 
				}
				?>
				</ul>
				
			</div>


			<div id="movieDescrip"
				<p id="movieSummary"><?php echo $summary; ?></p>
			</div>

			<div id="crew">
				<p id="director">Director: <?php echo $director; ?></p>
				<p id="actors">Starring:
				<?php for($i=0;$i<3;$i++){
						echo "$thisActors[$i]";
						if($i!=2){
							echo ", ";
						}
					} ?> </p>
			</div>
				
			
			<iframe id="trailer" width="300" height="200" src="<?php echo $trailerSource; ?>" frameborder="0" allowfullscreen></iframe>
			
			<div id="similarMovies">
				<h4 id="similarHead">Similar Movies:</h4>


				<script src="https://cdnjs.cloudflare.com/ajax/libs/flickity/1.1.0/flickity.pkgd.js"></script>

				<script type="text/javascript">
 				$(document).ready(function(){
   					$('.similarSlider').flickity({  
  				 		 wrapAround:true,
  						  autoPlay:true,
  						  pageDots:false
 					 });
				});
				</script>


				<?php

				echo "<div class='similarSlider'>";

				foreach($foundSimilar as $title){
		
					//Print the given image, regardless of whether or not it's at a new row.		
					$fileSearch = $title . ".image.url";
					$similarImgSource = $ini_array["$fileSearch"];

					echo "<div> <a href='movieDetailsPlan.php?movie=".$title."'> 
							<img class='similarImage' src='$similarImgSource' alt='sorry'> </a> </div>";

				}

				?>	
			</div>

		</div>

	
	</div>
</div>
<body>
