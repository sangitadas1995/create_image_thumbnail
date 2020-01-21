<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if(!empty($_POST) || !empty($_FILES)) {

	echo "<pre> files "; print_r($_FILES);
	echo "<pre>"; print_r($_POST);
	    // set height
    $height = '300';
    $width = '300';
    $upload_folder = 'uploads/';
    $thumbnail_folder = 'uploads/thumbs/';
    $checkTypeError = 0;
    $total = $_POST['total'];

        
            //call thumbnail creation function and store thumbnail name
            $upload_img = cwUpload('image',$upload_folder,'',TRUE,$thumbnail_folder,$height,$width,$_POST);
            //full path of the thumbnail image
            $thumb_src = 'uploads/thumbs/'.$upload_img;
            //set success and error messages
            if($upload_img) {
                echo 1; die;
              // echo  $message = 'Image Upload successfully';
            } else {
                echo 0; die;
               // echo $message = 'Some error occurred, please try again.';
            }
    	
}


function cwUpload($field_name = '', $target_folder = '', $file_name = '', $thumb = FALSE, $thumb_folder = '', $thumb_width = '', $thumb_height = '', $ajaxData){
        //folder path setup
    $target_path = $target_folder;
    $thumb_path = $thumb_folder;

    // total file count
    $total = $ajaxData['total'];
    $counter = 1;
    $checkErrorType=0;
    for( $i=1 ; $i <= $total ; $i++) {

        //file name setup
        $counter++;
        $thumbnalImgName = $fileName = '';

    		if(!empty($_FILES[$field_name]['name'][$i])) {
			        $filename_err = explode(".",$_FILES[$field_name]['name'][$i]);
			        $filename_err_count = count($filename_err);
			        $file_ext = $filename_err[$filename_err_count-1];
			        $allowed_file_types = array('png','jpeg','jpg', 'JPG');
			        
			        if (in_array($file_ext,$allowed_file_types)) {
			            
			            // rename file name
			            $newfilename = uniqid().'.'.$file_ext;
			            $fileName = $newfilename; // original pic

			            //upload image path
			            $upload_image = $target_path.basename($fileName);
			            
			            //upload image
			            if(move_uploaded_file($_FILES[$field_name]['tmp_name'][$i],$upload_image))
			            {
			                //thumbnail creation
			                if($thumb == TRUE)
			                {
			                    $thumbnalImgName = 'thumb_'.$fileName;
			                    $thumbnail = $thumb_path.$thumbnalImgName; // thumbnail pic
			                    list($width,$height) = getimagesize($upload_image);
			                    $thumb_create = imagecreatetruecolor($thumb_width,$thumb_height);
			                    switch($file_ext){
			                        case 'jpg':
			                            $source = imagecreatefromjpeg($upload_image);
			                            break;
			                        case 'jpeg':
			                            $source = imagecreatefromjpeg($upload_image);
			                            break;

			                        case 'png':
			                            $source = imagecreatefrompng($upload_image);
			                            break;
			                        case 'gif':
			                            $source = imagecreatefromgif($upload_image);
			                            break;
			                        default:
			                            $source = imagecreatefromjpeg($upload_image);
			                    }

			                    imagecopyresized($thumb_create,$source,0,0,0,0,$thumb_width,$thumb_height,$width,$height);
			                    switch ($file_ext) {
			                        case 'jpg' || 'jpeg':
			                            imagejpeg($thumb_create,$thumbnail,100);
			                            break;
			                        case 'png':
			                            imagepng($thumb_create,$thumbnail,100);
			                            break;

			                        case 'gif':
			                            imagegif($thumb_create,$thumbnail,100);
			                            break;
			                        default:
			                            imagejpeg($thumb_create,$thumbnail,100);
			                    }
			                }
			                
			            } else {
			               echo 'failed to upload';
			               return false;
			            }
			        } else {
			            echo 'file type not supported';
			            return false;
			        }
    		}
    			$finalArr = array();
    			$finalArr['org_img'] 	 = $fileName;
    			$finalArr['thumb_img']   = $thumbnalImgName;
    			$finalArr['tag'] 		 = $ajaxData['tag'][$i];
    			// $finalArr['id']    	 = $ajaxData['docId'][$i];
    			runSql($finalArr, $ajaxData['docId'][$i]);
    } // end of for loop

    if ($counter>1) {
        return true;
    } else {
        return false;
    }
}



function runSql($data,$id) {
	$con = mysqli_connect("localhost","root","password","practice");
    // Check connection
    if (!$con) {
      echo "Failed to connect to MySQL: " . mysqli_connect_error();
      exit();
    }
	foreach ($data as $key => $value) {
		if(!empty($value)) {
			$sql = "UPDATE `thumbail_img` SET `".$key."` = '".$value."' WHERE id=".$id;
			$res = mysqli_query($con, $sql);
		}
	}
}


?>