<? $sf = $_SERVER["SCRIPT_FILENAME"]; 
$cv = "cv.php";
$mm = "multimedia.php";
$mc = "loading.php";
$ix = "index.php";
$dl = "downloads.php";
$ga = "games.php"; ?>

<div id="navbar" class="transition">
	<? if( $sf == "/aws/cv.php" ) { ?>
		<a href=<?echo $mm?>><img src="svgs/vids.svg" class="centered transition movable first"></a>
		<a href=<?echo $mc?>><img src="svgs/mc.svg" class="centered transition movable second"></a>
		<a href=<?echo $ix?>><img src="svgs/w.svg"  class="centered third"></a>
		<a href=<?echo $dl?>><img src="svgs/dls.svg" class="centered transition movable fourth"></a>
		<a href=<?echo $ga?>><img src="svgs/games.svg" class="centered transition movable fifth"></a>	
	<? } else if( $sf == "/aws/multimedia.php" ) { ?>
		<a href=<?echo $cv?>><img src="svgs/cv.svg" class="centered transition movable first"></a>
		<a href=<?echo $mc?>><img src="svgs/mc.svg" class="centered transition movable second"></a>
		<a href=<?echo $ix?>><img src="svgs/w.svg"  class="centered third"></a>
		<a href=<?echo $dl?>><img src="svgs/dls.svg" class="centered transition movable fourth"></a>
		<a href=<?echo $ga?>><img src="svgs/games.svg" class="centered transition movable fifth"></a>
	<? } else if( $sf == "/aws/minecraft.php" ) { ?>
		<a href=<?echo $cv?>><img src="svgs/cv.svg" class="centered transition movable first"></a>
		<a href=<?echo $mm?>><img src="svgs/vids.svg" class="centered transition movable second"></a>
		<a href=<?echo $ix?>><img src="svgs/w.svg"  class="centered third"></a>
		<a href=<?echo $dl?>><img src="svgs/dls.svg" class="centered transition movable fourth"></a>
		<a href=<?echo $ga?>><img src="svgs/games.svg" class="centered transition movable fifth"></a>
	<? } else if( $sf == "/aws/downloads.php" ) { ?>
		<a href=<?echo $cv?>><img src="svgs/cv.svg" class="centered transition movable first"></a>
		<a href=<?echo $mm?>><img src="svgs/vids.svg" class="centered transition movable second"></a>
		<a href=<?echo $ix?>><img src="svgs/w.svg"  class="centered third"></a>
		<a href=<?echo $mc?>><img src="svgs/mc.svg" class="centered transition movable fourth"></a>
		<a href=<?echo $ga?>><img src="svgs/games.svg" class="centered transition movable fifth"></a>
	<? } else if( $sf == "/aws/games.php" ) { ?>
		<a href=<?echo $cv?>><img src="svgs/cv.svg" class="centered transition movable first"></a>
		<a href=<?echo $mm?>><img src="svgs/vids.svg" class="centered transition movable second"></a>
		<a href=<?echo $ix?>> <img src="svgs/w.svg"  class="centered third"></a>
		<a href=<?echo $mc?>><img src="svgs/mc.svg" class="centered transition movable fourth"></a>
		<a href=<?echo $dl?>><img src="svgs/dls.svg" class="centered transition movable fifth"></a>
	<? } ?>
</div>