<!DOCTYPE html>
<html>
<head>
	<title><?php echo $page_title; ?></title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

</head>
<script>
	function processPrintFunction () {
		window.print()
	}
</script>
<body onload="processPrintFunction()">

	
	<div class="container-fluid" id="container">
		<div class="row" >
		<?php
		$response = "";
		
		// echo $epins;
		
		
			$epins = json_decode($epins);
			for($i = 0; $i < count($epins); $i++){
				$pin = $epins[$i]->pin;
				$response .= "<div class='card col-sm-3' style='margin: 0px;'>";
				$response .= "<div class='card-body'>";
				$response .= "<p><b>Amount: </b> ₦".$amount."</p>";
				$response .= "<p><b>Network: </b> MTN, GLO, AIRTEL, 9MOBILE</p>";
				if(isset($_GET['track'])){
					$response .= "<p><b>Dial: </b> ".$pin."</p>";
				}else{
					$response .= "<p><b>Dial: </b> *174*".$pin."#</p>";
				}
				$response .= "</div>";
				$response .= "</div>";
			}
		

		echo $response;
	
	?>	
		</div>
	</div>

	<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>