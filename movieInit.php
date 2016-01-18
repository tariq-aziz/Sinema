<?php
//include("connectServer.php");
include("connectLocal.php");

$genreArray = array();
$curl = curl_init();
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

//curl_setopt($curl, CURLOPT_URL,'http://api.themoviedb.org/3/configuration?api_key=62a08828f7e30429c49acf8a0a69c23d');
//$imageConfig = curl_exec($curl);
//echo $imageConfig . "<br><br>";
//$imageConfigDecoded = json_decode($imageConfig);
//$imageBase = $imageConfig = 

curl_setopt($curl, CURLOPT_URL,'http://api.themoviedb.org/3/genre/movie/list?api_key=62a08828f7e30429c49acf8a0a69c23d');
$genreIndex = curl_exec($curl);
$genreIndexDecoded = json_decode($genreIndex, true);


foreach($genreIndexDecoded['genres'] as $genre){

	$genreName = $genre['name'];
	$genreID = $genre['id'];
	$genreArray["$genreID"] = $genreName;
}

//$allResultsDecoded = array();
//can just change the page # whenever you want to add movies to db/file. For db, check if movie is already there before adding.
//for($i=1; $i<=5; $i++){
	curl_setopt($curl, CURLOPT_URL,"https://api.themoviedb.org/3/discover/movie?page=5&api_key=62a08828f7e30429c49acf8a0a69c23d&language=en&sortby=popularity.desc");
	$resultPopular = curl_exec($curl); 
	$resultPopularDecoded = json_decode($resultPopular, true);
	//$allResultsDecoded[] = $resultPopularDecoded;
//}


$file = fopen("moviedata.ini", "a") or die("Unable to open file");
//cannot do all pages together since there is a limit of 40 request/10 secs (this program cannot add >40 movies per execution )
//foreach($allResultsDecoded as $movieResults){
//echo "NEW PAGE <br><br><br>";
foreach($resultPopularDecoded['results'] as $movie){

	$title = $movie['title'];
	$movieID = $movie['id'];
	$summary = $movie['overview'];
	$releaseDate = $movie['release_date'];
	echo "DATE: ". $releaseDate. "<br>";
	echo "SUMMARY: ". $summary. "<br>";
	$imageURL = "http://image.tmdb.org/t/p/w500/".$movie['poster_path'];

	//get more info about this particular movie
	curl_setopt($curl, CURLOPT_URL, "http://api.themoviedb.org/3/movie/$movieID?api_key=62a08828f7e30429c49acf8a0a69c23d&append_to_response=trailers,credits,releases");
	$indivMovie = curl_exec($curl);
	echo $indivMovie;
	$indivMovieDecoded = json_decode($indivMovie, true);
	$runtime = $indivMovieDecoded['runtime']." min";
	echo "RUNTIME: $runtime <br>";

	//only get audience ratings (PG, etc) for USA
	foreach($indivMovieDecoded['releases']['countries'] as $country){
		if($country['iso_3166_1']=='US'){
			$audience = $country['certification'];
		}
	}

	$usersQuery = "SELECT DISTINCT username FROM users";
	$usersResult = mysqli_query($usersQuery, $connection);

	foreach($usersResult['username'] as $user){
		$insertUserQuery = "INSERT INTO users(username, movie_title) VALUES ('$user', '$title')";
		$insertUserResult = mysqli_query($insertUserQuery, $connection);
	}



	echo "AUDIENCE: $audience <br>";

	$firstTrailerExtension = $indivMovieDecoded['trailers']['youtube'][0]['source'];

   	$trailerSource = "https://www.youtube.com/embed/".$firstTrailerExtension;
   	echo "TRAILER: " . $trailerSource; 

   	$actorsCount = 0;

   	$movieCheck2 = "SELECT * FROM movies WHERE title='$title'";
	$resultMovieCheck2 = mysqli_query($connection, $movieCheck2);
	$numMovieRows2 = mysqli_num_rows($resultMovieCheck2);
	
	if($numMovieRows2==0){
		foreach($indivMovieDecoded['credits']['cast'] as $actor){
			if($actorsCount<3){
				$actorName = $actor['name'];
				//dont i first have to check if this movie is already in the database?
				$actorSQL = "INSERT INTO actors(name, movie) VALUES ('$actorName', '$title')";
				mysqli_query($connection, $actorSQL);
				$actorsCount++;
			}
			
		}

		$imageURLEntry = "$title.image.url = \"$imageURL\";\n";
		$trailerURLEntry = "$title.trailer.url = \"$trailerSource\";\n";
		$infoEntry = "$title.info = \"$summary\";\n";
		$audienceEntry = "$title.audience = \"$audience\";\n";
		$runtimeEntry = "$title.runtime = \"$runtime\";\n\n";
		fwrite($file, $imageURLEntry);
		fwrite($file, $trailerURLEntry);
		fwrite($file, $infoEntry);
		fwrite($file, $audienceEntry);
		fwrite($file, $runtimeEntry);

	}
	

	foreach($indivMovieDecoded['credits']['crew'] as $crewMember){
		if($crewMember['job']=='Director'){
			$director = $crewMember['name'];
		}

	}

	foreach($indivMovieDecoded['genres'] as $genre){
		$genreName = $genre['name'];
		//this check is somewhat unnecessary (will never come to repition of title and genre, could just add all without the check)
		//NVM, this CHECK IS MANDATORY since you don't want to add the same movie to to the database twice
		$movieCheck = "SELECT * FROM movies WHERE title='$title' AND genre='$genreName'";
		$resultMovieCheck = mysqli_query($connection, $movieCheck);
		$numMovieRows = mysqli_num_rows($resultMovieCheck);
	
		if($numMovieRows==0){
			$movieQuery = "INSERT INTO movies (title, genre, director, release_date) VALUES ('$title','$genreName','$director','$releaseDate')";
			mysqli_query($connection, $movieQuery);
		}

	}

	

}
//}
	fclose();


curl_close();



?>