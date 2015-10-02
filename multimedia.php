<html>
	<?php include 'head.php';?>
	
	<body>
			
		<?php include 'navbar.php';?>

		<div id="page">
			<img src="teste.png" >
			<img src="teste2.png" >
			
			<audio controls>
			  <source src="sound/a_mixdown1.mp3" type="audio/mpeg">
			Your browser does not support the audio element.
			</audio>
			
			<?
				$image = imagecreatefrompng("teste.png");
				imagefilter($image, IMG_FILTER_COLORIZE, 0, 255, 0, 30);
			?>
			
			
			
			<? include 'footer.php'; ?>
		</div>
	</body>
</html>
