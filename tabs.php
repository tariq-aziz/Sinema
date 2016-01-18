<?php
	//include("connectServer.php");
	include("connectLocal.php");
	
	$ini_array = parse_ini_file("moviedata.ini");	
	
	
	$tabChoice = $_GET['tab'];
	
	/*
	if($tabChoice=="HighestRated"){

		$tdCount=0;
				
        $query = "SELECT DISTINCT title FROM movies WHERE avg_rating>=60 ORDER BY `avg_rating` DESC";
        $result = mysqli_query($connection, $query);
				
		echo "<table id='tabsContent'>";
		echo"<tr>";
		
		//NEED TO USE .ini SINCE IMG URLS ARENT EXACTLY THE MOVIE TITLE
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
			echo "<td> <a href='movieDetailsPlan.php?movie=".$row['title']."'> 
					<img src='$imgSource' alt='sorry'>";


			$queryRating = "SELECT DISTINCT avg_rating FROM movies WHERE title='$title'";
       		$resultRating = mysqli_query($connection, $queryRating);

            //should only output once (only one avg per movie)
           	while($ratingRow = mysqli_fetch_array($resultRating)){
           		echo "<div class='posterRating'>" . $ratingRow['avg_rating']. "% </div> </a> </td>";

       		}

			$tdCount++;
		
		}

		echo "</table>";

	}

	else{
		*/
		$tdCount=0;
				
            	$query = "SELECT title, avg_rating FROM movies WHERE genre='$tabChoice' ORDER BY avg_rating DESC";
            	$result = mysqli_query($connection, $query);
				
				echo "<table id='tabsContent'>";
				echo"<tr>";
				//NEED TO USE .ini SINCE IMG URLS ARENT EXACTLY THE MOVIE TITLE
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
		
					echo "<td> <a href='movieDetailsPlan.php?movie=".$row['title']."'> 
							<img class='posterImg' src='$imgSource' alt='$imgSource'>";


					$avgRating = $row['avg_rating'];
					echo "<div class='posterRating'>" . $avgRating. "% </div> </a> </td>";
					/*
					//$queryRating = "SELECT DISTINCT avg_rating FROM movies WHERE title='$title'";
            		//$resultRating = mysqli_query($connection, $queryRating);

            		//should only output once (only one avg per movie)
            		while($ratingRow = mysqli_fetch_array($resultRating)){
            			echo "<div class='posterRating'>" . $avgRating. "% </div> </a> </td>";

            		}
					*/
					$tdCount++;
		
			}
			echo "</table>";


		


	?>
			