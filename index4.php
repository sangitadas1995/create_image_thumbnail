<?php 

    $con = mysqli_connect("localhost","root","password","practice");
    // Check connection
    if (!$con) {
      echo "Failed to connect to MySQL: " . mysqli_connect_error();
      exit();
    }

    $finalArr = array();
    $sql = "SELECT * FROM thumbail_img";
	$result = mysqli_query($con, $sql);
	if (mysqli_num_rows($result) > 0) {
	    // output data of each row
	    while($row = mysqli_fetch_assoc($result)) {
	    	$finalArr[] = $row;
	    }
	}

	if(!empty($_POST['removeItem'])) {
		$sql = "delete from thumbail_img where id = ".$_POST['removeItem'];
		$result = mysqli_query($con, $sql);
		if ($result) {
			echo 1; die;
		} else {
			echo ''; die;
		}

	}
?>

<!DOCTYPE html>
<head>
	<meta charset="utf-8">
	<title>AJAX File</title>
	<script
  src="https://code.jquery.com/jquery-3.4.1.min.js"
  integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
  crossorigin="anonymous"></script>
</head>
<html>
<body>

<h2>Basic HTML Table</h2>
<button type="button" onclick="appendHTML()">Add</button>
<table style="width:100%" id="myTable">
  <tr>
    <th>File</th>
    <th>Tag Name</th> 
  </tr>

  <tbody>
  	<tr>
  		<td><input type="file" name="imageFile[]" class="abc" id="curr1" accept="image/x-png,image/jpeg"></td>
  		<td><input type="text" name="tag[]" id="tag1"></td>
  	</tr>

  	<tr>
  		<td><input type="file" name="imageFile[]" class="abc" id="curr2" accept="image/x-png,image/jpeg"></td>
  		<td><input type="text" name="tag[]" id="tag2"></td>
  	</tr>
  </tbody>
</table>


	<button type="submit" name="submit" id="submit" onclick="saveImg()">Submit</button>

<br>
<hr>
<table style="width:100%" id="myTable1">
  <tr>
    <th>Thumbnail File</th>
    <th>Tag Name</th>
    <th>Action</th>
  </tr>

  <tbody>
  	<?php $counter=0; foreach ($finalArr as $key => $value) { $counter = $key+1;?>
  	<tr class="hide_<?php echo $value['id']; ?>">
  		<td>
  			<img style="width:100px; height: 100px" src="uploads/thumbs/<?php echo $value['thumb_img'] ?>" alt="">
  			<input type="file" name="image[]" class="newImg" id="<?php echo "newcurr$counter"; ?>">
  		</td>
  		<td>
  			<!-- <input type="hidden" name="docId[]" id="docId<?php echo $value['id']; ?>" data-doc="<?php echo $value['id']; ?>" value="<?php echo $value['tag']; ?>"> -->
  			<input type="text" name="tag[]" class="newTag" data-doc="<?php echo $value['id']; ?>" id="<?php echo "newtag$counter" ?>" value="<?php echo $value['tag']; ?>">
  		</td>
  		<td><a href="javascript:void(0)" data-removeId = "<?php echo $value['id'];?>" class="removeItem">Remove</a></td>
  	</tr>
  <?php } ?>
  </tbody>
</table>
<br>
<hr>
	<div style="text-align:center;"><button type="submit" name="submit" id="update" onclick="update()">Update</button></div>

	<script>

		function appendHTML() {
			let counter = $(".abc").length+1;
			let html = '<tr><td><input type="file" name="imageFile[]" class="abc" id="curr'+counter+'" accept="image/x-png,image/jpeg"></td><td><input type="text" name="tag[]" id="tag'+counter+'"></td></tr>';
			$('#myTable tr:last').after(html);
		}

		function saveImg() {
			var form_data = new FormData(); // Creating object of FormData class
			var file_data = '';
			let j = 0;
			for(let i=1;i<=$(".newImg").length;i++){
				let filename = '#curr'+i;
				let tagname = '#tag'+i;
				file_data = $(filename).prop("files")[0]; // Getting the properties of file from file field
			    form_data.append('image['+i+']', file_data);
			    form_data.append('tag['+i+']', $(tagname).val());
			    form_data.append('postId', 1);
			}

			// form_data.append('tag', $(".tag").val());
			$.ajax({
			  url: 'process3.php',
			  type: 'POST',
			  processData: false, // important
			  contentType: false, // important
			  dataType : 'html',
			  data: form_data,
			  success : function(data) {
			  	if(data == 1) {
			  		location.reload();
			  	} else {
			  		alert('failed to add data');
			  	}
			  }
			});
		}


		function update() {
			
			// alert("fff");		
			var form_data = new FormData(); // Creating object of FormData class
			var file_data = '';
			let j = 0;
			
			for(let i=1;i<=$(".newImg").length;i++){
				let filename = '#newcurr'+i;
				let tagname = '#newtag'+i;
				file_data = ($(filename).prop("files"))?$(filename).prop("files")[0]:null; // Getting the properties of file from file field
			    form_data.append('image['+i+']', file_data);
			    form_data.append('tag['+i+']', $(tagname).val());
			    form_data.append('docId['+i+']', $(tagname).attr('data-doc'));
			    form_data.append('total', $(".newImg").length);
			}
			// form_data.append('tag', $(".tag").val());
			$.ajax({
			  url: 'process4.php',
			  type: 'POST',
			  processData: false, // important
			  contentType: false, // important
			  dataType : 'html',
			  data: form_data,
			  success : function(data) {
			  	if(data == 1) {
			  		location.reload();
			  	} else {
			  		alert('failed to update data');
			  	}
			  }
			});
		}

		$(document.body).on('click', '.removeItem', function() {
			let id = $(this).attr('data-removeId');
			$.ajax({
			  url: 'index3.php',
			  type: 'POST',
			  dataType : 'html',
			  data: {"removeItem" : id},
			  success : function(data) {
			  	if(data == 1) {
			  		let currentClass = '.hide_'+id;
			  		$(currentClass).remove();
			  	} else {
			  		alert('failed to add data');
			  	}
			  }
			});
		});
	</script>
</body>
</html>


