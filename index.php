<?php
session_start();

if (!isset($_SESSION['username'])){
  header("location: signin.php");
  exit();
}
include ("/home3/tariqaziz/private/connectServer.php");

//include("connectLocal.php");
  
  $ini_array = parse_ini_file("moviedata.ini");

?>
<!doctype html>
<html lang="en">

<head>
<meta charset="UTF-8">

<title>Sinema</title>
<link rel="SHORTCUT ICON" type="image/x-icon" href="images/Sinema.ico" />

<link href="SinemaPlan.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flickity/1.1.0/flickity.css">


<script src="http://code.jquery.com/jquery-1.10.2.js"></script>
<script src="http://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>

<script>
  $(function() {
    $( "#tabs" ).tabs();
  });
</script>

</head>

<header>
  <?php include("header.html"); ?>
</header>

<body>
<div id="container">


<h2> In Theatres </h2>
<!--to put proper links, fetch_array all in theaters, then add to array with link = "$row['title'].php"-->
<div class="slider">

<?php
  $spotlightQuery = "SELECT DISTINCT title from movies WHERE release_date BETWEEN 
                        DATE_SUB(CURDATE(),INTERVAL 3 MONTH) AND CURDATE()";
  $spotlightResults = mysqli_query($connection,$spotlightQuery );
  
  while($spotlightRow = mysqli_fetch_array($spotlightResults)){

    $spotlightTitle = $spotlightRow['title'];
    $spotlightInfo = "hello";
    $spotlightImg = $ini_array["$spotlightTitle". ".image.url"]; 
    $fixedImgURL = addslashes($spotlightImg);
    echo "<div> <a href='movieDetailsPlan.php?movie=".$spotlightTitle."'> 
          <img class='slickSlide' src='$fixedImgURL' alt='$spotlightTitle'> </a></div>";
  }                    
?>

</div>

<!--<script src="http://code.jquery.com/jquery-1.11.3.min.js"></script>
<script src="http://code.jquery.com/jquery-migrate-1.2.1.min.js"></script> -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/flickity/1.1.0/flickity.pkgd.js"></script>

<script type="text/javascript">
 $(document).ready(function(){
   $('.slider').flickity({  
    wrapAround:true,
    autoPlay:true,
    imagesLoaded: true,
    pageDots:false
    
  });
});
</script>


<div id="tabs">
         <ul>
            <li><a href="tabs.php?tab=Action">Action</a></li>
            <li><a href="tabs.php?tab=Thriller">Thriller</a></li>
            <li><a href="tabs.php?tab=Mystery">Mystery</a></li>
            <li><a href="tabs.php?tab=Adventure">Adventure</a></li>
            <li><a href="tabs.php?tab=Drama">Drama</a></li>
            <li><a href="tabs.php?tab=Horror">Horror</a></li>
            <li><a href="tabs.php?tab=Comedy">Comedy</a></li>
            <li><a href="tabs.php?tab=Animation">Animation</a></li>
            <li><a href="tabs.php?tab=Romance">Romance</a></li>
            <li><a href="tabs.php?tab=Science Fiction">Sci-Fi</a></li>
         </ul>
</div>

</div>
</body>

</html>
