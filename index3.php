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
			for(let i=1;i<=$(".abc").length;i++){
				let filename = '#curr'+i;
				let tagname = '#tag'+i;
				file_data = $(filename).prop("files")[0]; // Getting the properties of file from file field
			    form_data.append('image['+i+']', file_data);
			    form_data.append('tag['+i+']', $(tagname).val());
			    form_data.append('postId', $(".id").val());
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
			  	console.log('data ',data);
			  }
			});
		}
	</script>
</body>
</html>
