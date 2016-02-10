<?php
	//include("connectServer.php");
	include("connectLocal.php");
	
	$ini_array = parse_ini_file("moviedata.ini");	
	
	
	$tabChoice = $_GET['tab'];
	
		$tdCount=0;
		//obtain a list of movies from this genre, in order of descending rating
            	$query = "SELECT title, avg_rating FROM movies WHERE genre='$tabChoice' ORDER BY avg_rating DESC";
            	$result = mysqli_query($connection, $query);
				//create a table to display the movie mosters
				echo "<table id='tabsContent'>";
				echo"<tr>";
			
				while($row = mysqli_fetch_array($result)){
			
					//First check if the row should end. If so, print a new row and reset count
					if($tdCount>3){
						echo "</tr> <tr>";
						$tdCount=0;
					}
		
					//Print the given image, regardless of whether or not it's at a new row.
					$title = $row['title'];
		
					$fileSearch = $title . ".image.url";
					$imgSource = $ini_array["$fileSearch"];
					   $fixedImgURL = addslashes($spotlightImg);
		
					echo "<td> <a href='movieDetails.php?movie=".$row['title']."'> 
							<img class='posterImg' src='$imgSource' alt='$imgSource'>";

					//obtain and display the average rating of the movie
					$avgRating = $row['avg_rating'];
					echo "<div class='posterRating'>" . $avgRating. "% </div> </a> </td>";
					
					$tdCount++;
		
			}
			echo "</table>";


		


	?>
			
