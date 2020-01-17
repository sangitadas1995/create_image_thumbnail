<?php 

/**
*
* Function Name: cwUpload()
* $field_name => Input file field name.
* $target_folder => Folder path where the image will be uploaded.
* $file_name => Custom thumbnail image name. Leave blank for default image name.
* $thumb => TRUE for create thumbnail. FALSE for only upload image.
* $thumb_folder => Folder path where the thumbnail will be stored.
* $thumb_width => Thumbnail width.
* $thumb_height => Thumbnail height.
*
**/


function cwUpload($field_name = '', $target_folder = '', $file_name = '', $thumb = FALSE, $thumb_folder = '', $thumb_width = '', $thumb_height = ''){

    //folder path setup
    $target_path = $target_folder;
    $thumb_path = $thumb_folder;

    // total file count
    $total = count($_FILES['image']['name']);
    
    for( $i=0 ; $i < $total ; $i++ ) {
        //file name setup
        $filename_err = explode(".",$_FILES[$field_name]['name'][$i]);
        $filename_err_count = count($filename_err);
        $file_ext = $filename_err[$filename_err_count-1];

        $allowed_file_types = array('png','jpeg','jpg');  

        if (in_array($file_ext,$allowed_file_types)) {
            // if($file_name != ''){
            //     $fileName = $file_name.'.'.$file_ext;
            // }else{
            //     $fileName = $_FILES[$field_name]['name'][$i];
            // }
            
            // rename file name
            $newfilename = uniqid().'.'.$file_ext;
            $fileName = $newfilename;

            //upload image path
            $upload_image = $target_path.basename($fileName);
            
            //upload image
            if(move_uploaded_file($_FILES[$field_name]['tmp_name'][$i],$upload_image))
            {
                //thumbnail creation
                if($thumb == TRUE)
                {
                    $thumbnail = $thumb_path.$fileName;
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
                    switch($file_ext){
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

                // get file name - $fileName
                // file uploaded successfully

                // return $fileName;
            } else {
               echo 'failed to upload';
            }
        } else {
            echo 'file type not supported';
            return false;
        }
    }
}

?>