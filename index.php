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
	
        //$path =  "http://52.90.192.92/images/".basename( $_FILES["fileToUpload"]["name"]);
	$path = "https://sprayitaway.com/wp-content/uploads/2013/08/apple_by_grv422-d5554a4.jpg";
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
for ($i=0; $i<sizeof($output["hits"]); $i++) {
	//echo $output["hits"][$i]["fields"]["item_name"] . PHP_EOL;
	//echo $output["hits"][$i]["fields"]["nf_calories"] . PHP_EOL;
	$sum = $sum + $output["hits"][$i]["fields"]["nf_calories"];
	$count = $count + 1;
}
$calories = round($sum / $count);

/*
$photo = $path; 
$name = $output["hits"][0]["fields"]["item_name"]; 
$calories = $output["hits"][0]["fields"]["nf_calories"];
echo $photo.PHP_EOL;
echo $name.PHP_EOL;
echo $calories.PHP_EOL;
*/
curl_close ($ch);

$_SESSION["photo"] = $path;
$_SESSION["name"] = $name;
$_SESSION["calories"] = round($sum / $count);
//header("location: res.php");
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
	position: relative;
    float: left;
    border-radius: 3px;
    background-color: #fff;
    box-shadow:
        inset 1px 1px 3px #eee,
        inset -1px -1px 3px #888,
        1px 1px 3px #222;
    font-weight: bold;
    font-family: Courier New, fixed;
    background: url('grapes.jpg');
    background-size: cover;
    height: 720px;
    width: 100%;
    font-size: 12px;
    padding: 1px 4px;

    
}
.upload_button{
	
	margin: auto;
    left:0; right:0;
    top:0; bottom:0;
    position: absolute;
    width: 210px;
    height: 210px;
	border: 2px solid;
	border-radius: 100px;
	background-color:#e2ec31;
	text-align:center;
	float: center;
    padding-left: 15px;

}

.upload_button img:hover{
	height: 300px;
	width: 300px;
}


div.input-container {
    position: absolute;
    top:0;
    left: 0;
    bottom: 0;
    right:0;
    margin-left: auto;
    margin-top: auto; 
   
}



div.input-container input {
    opacity: 0; 
    top: 0; bottom: 0; right:0; left: 0;
    margin-bottom: 500px;
    text-align: center;
    margin:auto;
    float: center;
    position: absolute;
}

.PopUp{
	z-index: 1000000;
	top: 0; bottom: 0;
	left: 0;
	right:0;
	clear:both;
	float: center;
	text-align:center;
	margin: auto;
	background: aqua;
	margin-bottom: 500px;
	
}
</style>

</head>

<body>
<div class="nav-container">
	<nav>
		<ul>
			<li><a title="Home" href="index.php"> <img height="40px" width="40px" src="simple-orange-house-md.png"></a></li>
			<li><a title="Search for food facts" href="search.php"> <img height="40px" width="40px" src="search.png"> </a></li>
			<li><a title="BMI and facts" href="facts.php"> <img height="40px" width="40px" src="BMI.png"> </a></li>
<<<<<<< HEAD
			<li><a title="Contact Us" href="aboutUs.php"> <img height="40px" width="40px" src="fruit-hoot.jpg"> </a></li>		
=======
			<li><a title="Contact Us" href="aboutUs.php"> <img height="40px" width="40px" src="aboutUs.png"> </a></li>		
>>>>>>> d8d365e7dfa7cf1e90fe7a02268f9a1c714a1b47
		</ul>
	</nav>			
</div>



<form action="index.php" method="post" enctype="multipart/form-data" id="form">
<div class='fancy-file'>
    <div class='fancy-file-name'>
	    <div class="backCircle"></div>
	    <button class="upload_button"><img src="upload.png" height="120px" width="120px"></button>
	</div>
	
    <div class='input-container'> 
	
      <input name="fileToUpload" type="file" id="file" accept="image/*" capture="camera" style="width:220px; height:220px;"> 
    </div>
      
      <a href="#openModal">Open Modal</a>

	<!-- <div id="openModal" class="modalDialog"> -->
		
		<div>
			<?php
				if ( isset($_SESSION["photo"]) ) { ?>
					<div class="modal hide fade" id="myModal">
						<div class="modal-header">
							<a class="close" data-dismiss="modal">×</a>
								<h3>Success</h3>
						</div>
						<div class="modal-body">
							<?php
								echo "<img src=".$_SESSION["photo"]." style='width:200px; height:200px;'>";
								echo "<h1 class='animated fadeInDown'>".$_SESSION["name"]."</h1>";
								echo "<h1 class='animated zoomInRight'>Calories: ".$_SESSION["calories"]."</h1>"; ?>
							
						</div>
						<div class="modal-footer">
							<a class="close btn" data-dismiss="modal">Close</a>
    					</div>
					</div>
					
			<?php}?> 
					
		</div>
</div>
</form>


<script type="text/javascript">
		$(window).load(function () {
			$('#myModal').modal('show');
		});
					</script>

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

/*
function popOpen(){
$para = document.getElementsById("pop").innerHTML;


if($para.length > 0){
	document.getElementsByClassName("modalDialog").style.opacity=1;
}}
*/
</script>
</body>
</html>
