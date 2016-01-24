<?php
//error_reporting(E_ALL);
session_start();

$target_dir = "/var/www/html/images/";
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$uploadOk = 1;
$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
// Check if image file is a actual image or fake image
if(isset($_POST["picture"])) {
    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    if($check !== false) {
        //echo "File is an image - " . $check["mime"] . ".";
        $uploadOk = 1;
    } else {
        //echo "File is not an image.";
        $uploadOk = 0;
    }
}
// Check file size
if ($_FILES["fileToUpload"]["size"] > 5000000) {
    //echo "Sorry, your file is too large.";
    $uploadOk = 0;
}
// Allow certain file formats
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
&& $imageFileType != "gif" ) {
    //echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
    $uploadOk = 0;
}
// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    //echo "Sorry, your file was not uploaded.";
// if everything is ok, try to upload file
} else {
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {  
        //echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
	
        $path =  "http://52.90.192.92/images/".basename( $_FILES["fileToUpload"]["name"]);
	//$path = "https://sprayitaway.com/wp-content/uploads/2013/08/apple_by_grv422-d5554a4.jpg";
	//echo $path;
	run($path);
    } else {
        //echo "Sorry, there was an error uploading your file.";
    }
}


function run($path) {
$ch = curl_init();
 
//$path = "https://www.hamptoncreek.com/img/p-just-cookies/panel-cookie-choc-cookie.png";
curl_setopt($ch, CURLOPT_URL,"http://api.cloudsightapi.com/image_requests");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Authorization: CloudSight Vk3XOjdrscpgKPYRI8_vsg' ) );
curl_setopt($ch, CURLOPT_POSTFIELDS, "image_request[remote_image_url]=".$path."&image_request[locale]=en-US");
//curl_setopt($ch, CURLOPT_POSTFIELDS, "image_request[image]=@".$path."&image_request[locale]=en-US");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$post_output = curl_exec($ch);
$post_output = json_decode($post_output, true);
curl_close ($ch);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://api.cloudsightapi.com/image_responses/".$post_output["token"]);
curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Authorization: CloudSight Vk3XOjdrscpgKPYRI8_vsg' ) );
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
while(true) {
	$get_output = curl_exec($ch);
	$get_output = json_decode($get_output, true);
	if ($get_output["status"] == "completed") {
		$name = $get_output["name"];
		//echo $name . PHP_EOL;
		//echo $path . PHP_EOL;
		break;	
	}
}
curl_close ($ch);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,"http://api.nutritionix.com/v1_1/search");
curl_setopt($ch, CURLOPT_POST, 1);
$json = array(  
	"appId" => "61c5e459",
    "appKey" => "b26d236d69578d322ba2f6aa99bbb05e",
    "fields" => [
	    "item_name",
	    "brand_name",
	    "nf_calories",
	    "nf_sodium",
	    "item_type"
    ],
    "query" => $name
);

curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($json));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$output = curl_exec($ch);
$output = json_decode($output, true);

$sum = 0;
$count = 0;
$item = array();
for ($i=0; $i<sizeof($output["hits"]); $i++) {
	if ($output["hits"][$i]["fields"]["nf_calories"] != 0) {
		array_push($item, $output["hits"][$i]["fields"]["item_name"]);
		array_push($item, $output["hits"][$i]["fields"]["nf_calories"]);
		$sum = $sum + $output["hits"][$i]["fields"]["nf_calories"];
		$count = $count + 1;
	}
}
$calories = round($sum / $count);

curl_close ($ch);

$_SESSION["item"] = $item;
$_SESSION["photo"] = $path;
$_SESSION["name"] = $name;
$_SESSION["calories"] = round($sum / $count);

// If there is a name match
/* 
$name_p = explode(" ", $name);
for ($i=0; $i<sizeof($item); $i=$i+2) {
        $item_p = explode(" ", $item[$i]);
	for ($j=0; $j<sizeof($item_p); $j++) {
		for ($k=0; $k<sizeof($name_p); $k++) {
			//if ($item_p[$j] == $name_p[$k]) {
			if (strcasecmp($item_p[$j], $name_p[$k]) == 0) {
				$_SESSION["calories"] = $item[$i+1];
				break;
			}
		}
	} 
}
*/

}
?>

<!DOCTYPE html>
<html>
<head>
<title> Calorie Detector </title>

<link rel="stylesheet" href="animate.css/animate.min.css">
<link rel="stylesheet" type="text/css" href="main.css"> 
<link rel="stylesheet" type="text/css" href="normalize.css"> 
<style>
input[type='file'] {
  color: transparent;
}
div.fancy-file {
    position: relative;
    overflow: hidden;
    cursor: pointer;
}

div.fancy-file-name {
    float: left;
    border-radius: 3px;
    background-color: #fff;
    box-shadow:
        inset 1px 1px 3px #eee,
        inset -1px -1px 3px #888,
        1px 1px 3px #222;
    font-weight: bold;
    font-family: Courier New, fixed;
    width: 100%;
    font-size: 12px;
    padding: 1px 4px;
    height: 210px;
}
div.input-container {
    position: absolute;
    top: 0; left: 0;
}

div.input-container input {
    opacity: 0;
}
</style>

</head>

<body>
<!--
<div class="nav-container">
	<nav>
		<ul>
			<li><a href="#home"> <img height="40px" width="40px" src="simple-orange-house-md.png"> </a></li>
			<li><a href="#2"> <img height="40px" width="40px" src="search.png"> </a></li>
			<li><a href="#3"> <img height="40px" width="40px" src="facts.png"> </a></li>
			<li><a href="#1"> <img height="40px" width="40px" src="aboutUs.png"> </a></li>		
		</ul>
	</nav>			
</div>

<section class="panel b-blue" id="home">
-->
<form action="index.php" method="post" enctype="multipart/form-data" id="form">
<div class='fancy-file'>
    <div class='fancy-file-name'><!--<img src="upload.png" style="width:100%; height:100px;">--><button class="upload_button" style="width:100%; height:210px";></div>
    <div class='input-container'>    	
	
      <input name="fileToUpload" type="file" id="file" accept="image/*" capture="camera" style="width:100%; height:210px;"> 
</div>
</div>

</form>
<?php

if ( isset($_SESSION["photo"]) ) {
	echo "<img src=".$_SESSION["photo"]." style='width:200px; height:200px;'>";
	echo "<h1 class='animated fadeInDown'>".$_SESSION["name"]."</h1>";
	echo "<h1 class='animated zoomInRight'>Calories: ".$_SESSION["calories"]."</h1>";
	
	for ($i=0; $i<sizeof($_SESSION["item"]); $i=$i+2) {
		echo "<h2>".$_SESSION["item"][$i]." has ".$_SESSION["item"][$i+1]." Calories"."</h2>";
	}	
} 
else {
	echo "Please Click above to take photo";
}
?>

<!--
</section>


<section class="panel b-orange" id="1">
    <div class="panel__content">
      	<h1 class="panel__headline" id="aboutUs">Contact Us</h1>
       	
       	<div id="sr">
	      	<ul >
	      	<li>Seungmoon Rieh</li>
	      		<ol>
	      		<li>Email: <a href="#">seungmoon.rieh@mail.utoronto.ca</a></li>
	      		<li>Phone: (647)-876-0888</li>
	      		<li>EngSci Grad 2015</li>
			</ol>
	      	</ul>
	</div>
	      
	      
	      <div id="ml">
	      <ul >
	      	<li>Michael Liu</li>
	      		<ol>
	      		<li>Email: <a href="#">michaelzb.liu@mail.utoronto.ca</a></li>
	      		<li>Phone: (416)-576-7101</a></li>
	      		<li>University Of Toronto (year 1)</li></ol>
	      </ul></div>
	            
	     <div id="nd">  
	     <ul >
	      	<li>Nikita Dua</li>
	      		<ol>
	      		<li>Email: <a href="#">nikita.dua@mail.utoronto.ca</a></li>
	      		<li>Phone: (647)-979-3634</a></li>
	      		<li>University Of Toronto (year 2)</li></ol>
	      	</ul></div>
	      
	      <div id="mi">
	      <ul >
	      	<li>Monica Iqbal</li>
	      		<ol>
	      		<li>Email: <a href="#">monica.iqbal@mail.utoronto.ca</a></li>
	      		<li>Phone: (647)-830-4256</a></li>
	      		<li>University Of Toronto (year 2)</li></ol>
	      		
	      	</ul></div>
	      
	      <div id="kp">
	      <ul >
	      	<li>Kevin Patel</li>
	      		<ol>
	      		<li>Email: <a href="#">kp.patel@mail.utoronto.ca</a></li>
	      		<li>Phone: (905)-463-1366</a></li>
	      		<li>University Of Toronto (year 2)</li></ol>
	      		
	      	</ul></div>

    </div>
  </article>
</section>
-->

<!--
<div class="search">
<section class="panel b-yellow" id="2">
  
    <div class="panel__content" id="about-content">
      <h1 class="panel__headline"><i class="fa fa-bolt"></i>&nbsp;Search</h1>
      <div class="panel__block"></div>
      
      
        </div>
</section></div>

  
  
  
</section>

<section class="panel b-red" id="3">
  <article class="panel__wrapper">
    <div class="panel__content">
      <h1 class="panel__headline"><i class="fa fa-music"></i>&nbsp;Facts</h1>
      <div class="panel__block"></div>
      <p>Beard sriracha kitsch literally, taxidermy normcore aesthetic wayfarers salvia keffiyeh farm-to-table sartorial gluten-free mlkshk. Selvage normcore 3 wolf moon, umami Kickstarter artisan meggings cardigan drinking vinegar bicycle rights.</p>
    </div>
  </article>
</section>

			
			
		</div>
-->


<script src="http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>		
<script src="index.js"></script>
<script>
document.getElementById("file").onchange = function() {
document.getElementById("form").submit();

$('div.fancy-file input:file').bind('change blur', function() {
    var $inp = $(this), fn;

    fn = $inp.val();
    if (/fakepath/.test(fn))
        fn = fn.replace(/^.*\\/, '');

    $inp.closest('.fancy-file').find('.fancy-file-name').text(fn);
});
}

</script>
</body>
</html>

