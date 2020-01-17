<?php 
// PHP error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/*

Note create a uploads folder 
Under it create thumbnail folder

under uploads folder real image will be saved
and thumbnail will be set under thumbs folder

*/




// include file where image resizer function is set
include 'process2.php';

if(!empty($_FILES['image'])){

	$message = '';
	// set height
	$height = '300';
	$width = '300';
	$upload_folder = 'uploads';
	$thumbnail_folder = 'thumbs';
        //call thumbnail creation function and store thumbnail name
        $upload_img = cwUpload('image','uploads/','',TRUE,'uploads/thumbs/',$height,$width);
        
        //full path of the thumbnail image
        $thumb_src = 'uploads/thumbs/'.$upload_img;
        
        //set success and error messages
        if($upload_img) {
        	$message = 'Image Upload successfully';
        } else {
        	$message = 'Some error occurred, please try again.';
        }
}else{
    
    //if form is not submitted, below variable should be blank
    $thumb_src = '';
    $message = '';
}

?>


<!DOCTYPE html>
<html>
<head>
	<title>Image Resize</title>
</head>
<body>
<form method="post" enctype="multipart/form-data">
    <input type="file" name="image[]" multiple="multiple"/>
    <input type="submit" name="submit" value="Upload"/>
</form>

<?php if($thumb_src != ''){ ?>
<img src="<?php echo $thumb_src; ?>" alt="">
<?php } ?>
</body>
</html>

