<html>
	<head>
		<title> Calorie Detector </title>
		<link rel="stylesheet" type="text/css" href="animate.css"> 
		<link rel="stylesheet" type="text/css" href="main.css"> 
		<link rel="stylesheet" type="text/css" href="normalize.css"> 
		
	</head>
	<body>
		<script src="jquery-1.12.0.js"></script>
		
		<script src="jquery-1.12.0.min.js"></script>
	
		<script type="text/javascript">
			$(document).ready(function() {
				
			});
		</script>
		
		<div class="nav-container">
			<nav>
			<ul>
				<li><a href="index.php"> <img height="40px" width="40px" src="simple-orange-house-md.png"> </a></li>
				<li><a href="search.php"> <img height="40px" width="40px" src="search.png"> </a></li>
				<li><a href="facts.php"> <img height="40px" width="40px" src="facts.png"> </a></li>
				<li><a href="aboutUs.php"> <img height="40px" width="40px" src="aboutUs.png"> </a></li>
				
			</ul>
			</nav>
			
		</div>
  
</section>

<section class="panel b-red" id="3">
  <article class="panel__wrapper">
    <div class="BMI-Calc">
           <h3> Calculate your BMI!</h3>
           
		   <form method="post" action="facts.php">
			   <label> Sex* </label>
			    <select name="sex" required> 
				    <option value="not selected"> Select One </option>
				    <option value="male"> Male </option>
				    <option value="female"> Female </option>
			    </select>
		   
			    <label> Height* </label>
			    <select name="height" required>
				    <option value="not selected"> Select One </option>
				    <option value="4ft 0in"> 4ft 0in </option>
				    <option value="4ft 1in"> 4ft 1in </option>
				    <option value="4ft 2in"> 4ft 2in </option>
				    <option value="4ft 3in"> 4ft 3in </option>
				    <option value="4ft 4in"> 4ft 4in </option>
				    <option value="4ft 5in"> 4ft 5in </option>
				    <option value="4ft 6in"> 4ft 6in </option>
				    <option value="4ft 7in"> 4ft 7in </option>
				    <option value="4ft 8in"> 4ft 8in </option>
				    <option value="4ft 9in"> 4ft 9in </option>
				    <option value="4ft 10in"> 4ft 10in </option>
				    <option value="4ft 11in"> 4ft 11in </option>
				    <option value="5ft 0in"> 5ft 0in </option>
				    <option value="5ft 1in"> 5ft 1in </option>
				    <option value="5ft 2in"> 5ft 2in </option>
				    <option value="5ft 3in"> 5ft 3in </option>
				    <option value="5ft 4in"> 5ft 4in </option>
				    <option value="5ft 5in"> 5ft 5in </option>
				    <option value="5ft 6in"> 5ft 6in </option>
				    <option value="5ft 7in"> 5ft 7in </option>
				    <option value="5ft 8in"> 5ft 8in </option>
				    <option value="5ft 9in"> 5ft 9in </option>
				    <option value="5ft 10in"> 5ft 10in </option>
				    <option value="5ft 11in"> 5ft 11in </option>
				    <option value="6ft 0in"> 6ft 0in </option>
				    <option value="6ft 1in"> 6ft 1in </option>
				    <option value="6ft 2in"> 6ft 2in </option>
				    <option value="6ft 3in"> 6ft 3in </option>
				    <option value="6ft 4in"> 6ft 4in </option>
				    <option value="6ft 5in"> 6ft 5in </option>
				    <option value="6ft 6in"> 6ft 6in </option>
				    <option value="6ft 7in"> 6ft 7in </option>
				    <option value="6ft 8in"> 6ft 8in </option>
				    <option value="6ft 9in"> 6ft 9in </option>
				    <option value="6ft 10in"> 6ft 10in </option>
				    <option value="6ft 11in"> 6ft 11in </option>
				    <option value="7ft 0in"> 7ft 0in </option>
				    <option value="7ft 1in"> 7ft 1in </option>
				    <option value="7ft 2in"> 7ft 2in </option>
				    <option value="7ft 3in"> 7ft 3in </option>
				    <option value="7ft 4in"> 7ft 4in </option>
				    <option value="7ft 5in"> 7ft 5in </option>
			    </select>

			    <label>Age</label>
			    	<input type="text" name="age" size="10">
			    <label>Mass</label>
			        <input type="text" name="mass" size="6">
			        <select name="massUnit">
				        <option value="lbs"> lbs </option>
				        <option value="kg"> kg </option> </select> <br> </br>
				<input type="submit" value="calculate" id="calButton">
			</form>
			<p id="bmi">
			<?php
				include("header");
				$sex = $_POST["sex"];
				
				if($sex != "not selected"){
					
				
				
					$height = $_POST["height"];
					$age = $_POST["age"];
					$mass = $_POST["mass"];
					$massUnit = $_POST["massUnit"];
					
					$kg = 0;
					
					if($massUnit === "lbs"){
						$kg = $mass / 2.2046;
					}else{
						$kg = $mass;
					}
				
					$meters = (int)$height[0]*0.3048 + (int)$height[4]*0.0254;
					
					$bmi = $kg / $meters**2;	
					$msg = '';
					if($bmi >=18.5 && $bmi<=24.9){
						$msg .= "You're in a healthy range!";
						
					}
					elseif($bmi<18.5){
						$msg .= "You're underweight";
						
					}
					else{
						$msg .= "You're overweight";
					}

					
					echo "Your Body Mass Index is: ".(int)$bmi;
					echo "<br> </br>";
					echo $msg;
	
					header('Location: facts.php');
								
				}
				
				
				
			?></p>
    </div>
  </article>
</section>

			
			
		</div>
		<script src="http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js">
		
		<script src="index.js"></script>
		
		
	</body>
	
	
</html>