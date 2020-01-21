<?php 
include_once('../includes/links.php');
include_once('../includes/admininit.php');

if(!empty($_FILES['image']) && $_POST){

    $message = '';
    // set height
    $height = '300';
    $width = '300';
    $upload_folder = 'uploads';
    $thumbnail_folder = 'thumbs';
        //call thumbnail creation function and store thumbnail name
        $upload_img = cwUpload('image','uploads/','',TRUE,'uploads/thumbs/',$height,$width, $_POST);
        
        //full path of the thumbnail image
        $thumb_src = 'uploads/thumbs/'.$upload_img;
        
        //set success and error messages
        /*if($upload_img) {
          echo  $message = 'Image Upload successfully';
        } else {
           echo $message = 'Some error occurred, please try again.';
        }*/
}else{
    
    //if form is not submitted, below variable should be blank
    $thumb_src = '';
    $message = '';
}


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


function cwUpload($field_name = '', $target_folder = '', $file_name = '', $thumb = FALSE, $thumb_folder = '', $thumb_width = '', $thumb_height = '',$ajaxData){
  global  $mycms,$mycommoncms,$cfg;
    //folder path setup
    $target_path = $target_folder;
    $thumb_path = $thumb_folder;

    // total file count
    $total = $ajaxData['total'];
    $counter=1;
    $thumbnalImgName = $fileName = '';
    for( $i=1 ; $i <= $total ; $i++ ) {
        $counter++;
        //file name setup
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
                        
                    //     $sqlUpdate                 =   array();
                    //     $sqlUpdate['QUERY']        =   "UPDATE ".DB_IMAGE_GALLERY."
                    //                                       SET 
                    //                                      `postId`            = ?,
                    //                                      `galleryImageFile` = ?,
                    //                                      `thumbnalImgName`  = ?,
                    //                                      `fileType`         = ?,
                    //                                      `imageTagLine`     = ?,
                    //                                      `altTag`           = ?,
                    //                                      `effectiveUrl`     = ?,
                    //                                      `ModifiedDateTime`  = ?,
                    //                                      `modifiedIp`        = ?
                    //                                      WHERE `id`          =   ?";
                                                 
                    //     $sqlUpdate['PARAM'][] =   array('FILD' => 'postId' ,              'DATA' => $_POST['postId'] ,         'TYP' => 's');
                    //     $sqlUpdate['PARAM'][] =   array('FILD' => 'galleryImageFile' ,    'DATA' => $fileName ,                 'TYP' => 's');
                    //     $sqlUpdate['PARAM'][] =   array('FILD' => 'thumbnalImgName' ,     'DATA' => $thumbnalImgName ,          'TYP' => 's');
                    //     $sqlUpdate['PARAM'][] =   array('FILD' => 'fileType' ,            'DATA' => "image/jpeg" ,               'TYP' => 's');
                    //     $sqlUpdate['PARAM'][] =   array('FILD' => 'imageTagLine' ,        'DATA' => $_POST['tag'][$i],           'TYP' => 's');
                    //     $sqlUpdate['PARAM'][] =   array('FILD' => 'altTag' ,              'DATA' => $_POST['altTag'][$i] ,       'TYP' => 's');
                    //     $sqlUpdate['PARAM'][] =   array('FILD' => 'effectiveUrl' ,        'DATA' => " " ,                        'TYP' => 's');
                    
                    //     $sqlUpdate['PARAM'][] =   array('FILD' => 'ModifiedDateTime' ,    'DATA' => date('Y-m-d H:i:s'),          'TYP' => 's');
                    //     $sqlUpdate['PARAM'][] =   array('FILD' => 'modifiedIp' ,          'DATA' => $_SERVER['REMOTE_ADDR'] ,     'TYP' => 's');
                    //     $sqlUpdate['PARAM'][] =   array('FILD' => 'id' ,                  'DATA' => $_POST['imgId'][$i],           'TYP' => 's');
                    //     $res = $mycms->sql_update($sqlUpdate);
                    //     echo "<pre>";
                    //     print_r($sqlUpdate);
                    
                 
                    // echo 5;
                
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
        $finalArr['galleryImageFile']     = $fileName;
        $finalArr['thumbnalImgName']      = $thumbnalImgName;
        $finalArr['altTag']               = $ajaxData['altTag'][$i];
        runUpdateSQL($finalArr, $ajaxData['imgId'][$i], $ajaxData['postId']);
    } // end of for loop
}


function runUpdateSQL($data, $imgId, $postId) {

    foreach ($data as $key => $value) {
        if(!empty($value)) {
            $sqlUpdate                 =   array();
            $sqlUpdate['QUERY']        =   "UPDATE ".DB_IMAGE_GALLERY." SET `".$key."` = ?, `ModifiedDateTime`  = ?,
                                                         `modifiedIp` = ? WHERE `postId` = ? AND `id` =  ?";

            $sqlUpdate['PARAM'][]      =   array('FILD' => "$key",'DATA' => $value ,'TYP' => 's');
            $sqlUpdate['PARAM'][]      =   array('FILD' => 'ModifiedDateTime' , 'DATA' => date('Y-m-d H:i:s'), 'TYP' => 's');
            $sqlUpdate['PARAM'][]      =   array('FILD' => 'modifiedIp' , 'DATA' => $_SERVER['REMOTE_ADDR'] , 'TYP' => 's');
            $sqlUpdate['PARAM'][]      =   array('FILD' => 'postId' , 'DATA' => $postId, 'TYP' => 's');
            $sqlUpdate['PARAM'][]      =   array('FILD' => 'id' , 'DATA' => $imgId, 'TYP' => 's');
            $res = $mycms->sql_update($sqlUpdate);
        }
    }

}

?>
