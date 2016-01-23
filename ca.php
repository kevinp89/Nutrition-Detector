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
    echo "Sorry, your file is too large.";
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
    echo "Sorry, your file was not uploaded.";
// if everything is ok, try to upload file
} else {
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {  
        //echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
	
        $path =  "http://52.90.192.92/images/".basename( $_FILES["fileToUpload"]["name"]);
	//echo $path;
	run($path);
    } else {
        echo "Sorry, there was an error uploading your file.";
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
header("location: res.php");
}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Calorie Detector!</title>
	<link rel="stylesheet" type="text/css" href="animate.css">
</head>
<body>

<script src="jquery-1.12.0.js"></script>

<script src="jquery-1.12.0.min.js"></script>


<form action="ca.php" method="post" enctype="multipart/form-data" style="display:inline-block;">
      		<span class="btn btn-default btn-file">
                        Select Image<input name="fileToUpload" type="file" accept="image/*" capture="camera"> 
                    </span>       
                    <button name="picture" type="submit" class="btn btn-default">
                        Upload it!
                    </button>
                </form>
<?php
if ( isset($_SESSION["photo"]) ) {
echo "<img src=".$_SESSION["photo"]." style='width:200px; height:200px;'>";
echo "<h1>".$_SESSION["name"]."</h1>";
echo "<h1>".$_SESSION["calories"]."</h1>";

} 
?>
<script>

</script>
</body>
</html>

