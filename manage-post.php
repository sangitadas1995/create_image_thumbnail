<?php
include_once ('../includes/links.php');
include_once ('../includes/admininit.php');

page_header(ADMIN_TITLE . " - Manage Post");

$show = str_replace(' ', '', $_GET['show']);
include_once ("left_bar.php");

if($_GET['m']==1){$msg='Data inserted';}
if($_GET['m']==2){$msg='Data Updated';}
if($_GET['m']==3){$msg='Data Deleted';}
if($_GET['m']==4){$msg='Listing Renewed';}
?>
<script language="javascript" src="js/common.js"></script>
<link href="cal/tcal.css" rel="stylesheet" type="text/css" />
<style>
#galleryImg {
  font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

#galleryImg td, #galleryImg th {
  border: 1px solid #ddd;
  padding: 8px;
}

#galleryImg tr:nth-child(even){background-color: #f2f2f2;}

#galleryImg tr:hover {background-color: #ddd;}

#galleryImg th {
  padding-top: 5px;
  padding-bottom: 5px;
  text-align: left;
  background-color: #49565a;
  color: white;
}



#edit_galleryImg {
  font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

#edit_galleryImg td, #edit_galleryImg th {
  border: 1px solid #ddd;
  padding: 8px;
}

#edit_galleryImg tr:nth-child(even){background-color: #f2f2f2;}

#edit_galleryImg tr:hover {background-color: #ddd;}

#edit_galleryImg th {
  padding-top: 5px;
  padding-bottom: 5px;
  text-align: left;
  background-color: #49565a;
  color: white;
}
</style>
<script type="text/javascript">
	$(document).ready(function(){
	 $('.delete').click(function(){
	   var el = this;
	   var id = this.id;
	   var deleteid = $(this).attr('data-removeItem');
	   let BASE_URL = $('#BASE_URL').val();
	   
	   $.ajax({
	     url: 	BASE_URL+'webmaster/manage-post-upload-process.php',
	     type: 'POST',
	     data: { id:deleteid,act:'removedGalleryImg'},
	     success: function(response){

	   if(response == 1){
		   $(el).closest('tr').css('background','tomato');
		   $(el).closest('tr').fadeOut(800,function(){
		   $(this).remove();
	   });
	      }else{
   			alert('Invalid ID.');
	      }

	    }
	   });

	 });

	});
</script>

<script src="cal/tcal.js"></script>
<script src="js/jquery.base64.js"></script>
<script src="<?=BASE_URL?>js/autocomplete.js"></script>
<table class="mainTable" cellSpacing="0" cellPadding="0" width="100%" align="center">
	<tbody>
		<tr>
			<td vAlign=top align="top" width="100%">
				<? 
				if($show==''){
				/********** View Invoice List ***********/
				?>
					<form action="manage-post.php" method="get">
					<table width="98%" align="center" cellpadding="6" cellspacing="1" class="tborder" id="invoice_list_body">
						
						<thead>
							<tr class="tbhdr">
								<td colspan="10">
									Manage Post
									<input type="button" name="addNew" value="Add New" class="greenbutton" onClick="window.location.href='manage-post.php?show=add'" style="float:right;">
								</td>
							</tr>
							 <tr class="row1" id="removeFieldId">
								<td colspan="10" align="center">
									<table width="100%">
										<?
										if($msg){
										?>
											<tr class="msg">
												<td colspan="10" class="msg">
													<?=$msg?>
												</td>
											</tr>
										<?
										}
										?>
										<tr>
											<td align="right">Title :</td>
											<td>
												<input type="text" name="title" value="<?=$_GET['title']?>" class="fld">
											</td>
											<td align="right">Category :</td>
											<td>
												<?
												$sqlCategory	=	array();
												$sqlCategory['QUERY']	=	"SELECT `id`,
																				`name`
																			FROM ".DB_CATEGORY."";
												$resCategory	=	$mycms->sql_select($sqlCategory);
												?>
												<select name="category"  id="category" class="fld">
													<option value="">--Select Category--</option>
													<?
													foreach($resCategory as $key=>$rowCategory){
													?>
														<option value="<?=$rowCategory['id']?>"<? if($_GET['category']==$rowCategory['id']){?> selected="selected"<?}?>><?=$rowCategory['name']?></option>
													<?	
													}
													?>
												</select>
											</td>
										</tr>
										<tr>
											<td align="right">Package :</td>
											<td>
												<?
												$sqlPackage	=	array();
												$sqlPackage['QUERY']	=	"SELECT `id`,
																				`name`,
																				`price`,
																				`validity`
																			FROM ".DB_PACKAGE."
																			WHERE `status` = 'A'
																			ORDER BY CAST(price AS UNSIGNED) ASC";
												$resPackage	=	$mycms->sql_select($sqlPackage);
												?>
												<select name="package"  id="package" class="fld">
													<option value="">--Select Package--</option>
													<?
													foreach($resPackage as $key=>$rowPackage){
													?>
														<option value="<?=$rowPackage['id']?>"<? if($_GET['package']==$rowPackage['id']){?> selected="selected"<?}?>><?=$rowPackage['name'].' (Rs.'.$rowPackage['price'].' - '.$rowPackage['validity'].' Days)'?></option>
													<?	
													}
													?>
												</select>
											</td>
											<td colspan="2" align="right">
												<input  name="searchorder" type="submit" value="Search" class="loginbttn">
												<input type="button" onClick="window.location.href='<?=$_SERVER['PHP_SELF']?>';" class="resetbttn" value="Clear"/>																	
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr class="tbsubhdr">
								<th width="5%">Sl. No.</th>
								<th width="20%">Post</th>
								<th width="15%">Title</th>
								<th width="10%">Keywords</th>
								<th width="12%">Package</th>
								<th width="10%">Category</th>
								<th width="10%">User Name</th>
								<th width="10%">City</th>
								<th width="8%">Status</th>
								<th width="10%">Action</th>
							</tr>
						</thead>
						<tbody>
							<?
							$i		=	0;
							$sqlPost	=	array();
							$sqlPost['PARAM'][]	=	array('FILD' => '`post`.`status`', 'DATA' => 'D', 'TYP' => 's');
							$sqlPost['PARAM'][]	=	array('FILD' => '`post`.`approveStatus`', 'DATA' => 'A', 'TYP' => 's');
							
							$where	=	" ";
							if($_GET['title']!=''){
								$where .= " AND `post`.`title` LIKE ?";
								$sqlPost['PARAM'][]	=	array('FILD' => '`post`.`title`', 'DATA' =>'%'.$_GET['title'].'%', 'TYP' => 's');
								
							}
							if($_GET['category']!=''){
								$where .= " AND `post`.`category` = ?";
								$sqlPost['PARAM'][]	=	array('FILD' => '`post`.`category`', 'DATA' =>$_GET['category'], 'TYP' => 's');
								
							}
							if($_GET['package']!=''){
								$where .= " AND `post`.`package` = ?";
								$sqlPost['PARAM'][]	=	array('FILD' => '`post`.`package`', 'DATA' =>$_GET['package'], 'TYP' => 's');
								
							}
					$sqlPost['QUERY']	=	"SELECT `post`.*,

															`package`.`name` AS `packageName`,
															`package`.`validity` AS `packageValidity`,
															`category`.`name` AS `categoryName`,
															`user`.`name` AS `userName`
														FROM ".DB_POST." AS `post`
													LEFT JOIN ".DB_PACKAGE." AS `package`
														ON `package`.`id`	=	`post`.`package`
													LEFT JOIN ".DB_CATEGORY." AS `category`
														ON `category`.`id`	=	`post`.`category`
													LEFT JOIN ".DB_USER." AS `user`
														ON `user`.`id`		=	`post`.`userId`
														WHERE `post`.`status` != ?
														AND	  `post`.`approveStatus` = ?
														".$where."
														ORDER BY `post`.`id` DESC";
							
							$resPost			=	$mycms->sql_select($sqlPost);
							$numUser			=	$mycms->sql_numrows($resPost);
							$sqlPost['QUERY'] 	=	$sqlPost['QUERY']. " LIMIT $offset,$limit";
							$resPost			=	$mycms->sql_select($sqlPost);

							
			              

							if($numUser>0)
							{
								foreach($resPost as $key=>$rowPost)
								{
									$i++;
								?>

									<tr class="row1">
										<td align="center"><?=$i+$offset?></td>
										<td align="center">
											<?
											$fileType	=	explode('/',$rowPost['fileType']);
											if($fileType[0]=='image'){
											?>
												<img src="<?=BASE_URL?><?=UPLOADS_POST?><?=$rowPost['file']?>" height="180" width="250">
											<?
											}
											else if($fileType[0]=='video'){
											?>
									  			<img src="<?=BASE_URL?><?=UPLOADS_THUMNAILS?><?=$rowPost['thumnails']?>" height="180" width="250">
											<?
											}else{?>
												<img src="<?=BASE_URL?><?=UPLOADS_THUMNAILS?><?=$rowPost['thumnails']?>" width="200px" height="100px">
										<?php }
											?>
										</td>
										<td align="left"><?=$rowPost['title']?></td>
										<td align="center"><?=$rowPost['keywords']?></td>
										<td align="center"><?=$rowPost['packageName'].' ( '.$rowPost['packageValidity'].' Days)'?></td>
										<td align="center"><?=$rowPost['categoryName']?></td>
										<td align="center"><?=$rowPost['userName']?></td>
										<td align="center">
											<?php
												$sqlCity = array();
												$sqlCity['QUERY'] 		=	"SELECT `name` FROM ".DB_CITY." WHERE `status` = ? AND `id` = ?";
												$sqlCity['PARAM'][]	=	array('FILD' => 'status', 'DATA' =>'A', 'TYP' => 's');
												$sqlCity['PARAM'][]	=	array('FILD' => 'id', 'DATA' =>$rowPost['cityId'], 'TYP' => 's');
												$resCity			=	$mycms->sql_select($sqlCity);
												echo $resCity[0]['name'];
											?>
										</td>
										<td align="center">
											<?
											$expireStatus	=	checkListingExpirestatus($rowPost['id']);
											if($expireStatus=='Active'){
											?>
												<a href="manage-post-process.php?act=<?=($rowPost['status']=='A')?'Inactive':'Active'?>&id=<?=$rowPost['id']?>&pageno=<?=($_GET['pageno']!="")?$_GET['pageno']:'0' ?>" class="<?=($rowPost['status']=='A')?'greenbuttonelements':'redbuttonelements'?>"><?=($rowPost['status']=='A')?'Active':'Inactive'?></a>
											<?
											}
											else if($expireStatus=='Expired'){
												echo '<font color="red">Expired</font>';
											}
											?>
										</td>
										<td align="center">
											<a href="manage-post.php?show=edit&id=<?=$rowPost['id']?>">
												<img src="images/edit.gif" alt="Edit" title="Edit" width="16" height="16" border="0" />
											</a>
											<a href="manage-post.php?show=view&language=<?=$language?>&id=<?=$rowPost['id']?>">
												<img src="images/view.gif" alt="View" width="16" height="16" border="0" />
											</a>
											<a href="manage-post-process.php?act=Delete&id=<?=$rowPost['id']?>">
												<img src="images/drop.gif" alt="Delete" title="Delete" width="16" height="16" border="0" />
											</a>
											<?
											if($expireStatus=='Expired'){
											?>
												<br>
												<input type="button" name="addNew" value="Renew" class="greenbutton" onClick="window.location.href='manage-post.php?show=renew&id=<?=$rowPost['id']?>'">
											<?
											}
											?>
										</td>
									</tr>
								<?
								}
								?>
								<tr>
									<td height="30" colspan="12" align="right">									
										<div class="pageno">
											<?=$mycommoncms->paginate($numUser, $limit, $pageno, "pageno", "content")?>
										</div>
									</td>
								</tr>
								<?
							}
							else
							{
							?>
							<tr>
								<td align="center" colspan="<?=GSTSTATUS=='ON'?'10':'8'?>">No Record(s)</td>
							</tr>
							<?
							}
							?>
						</tbody>
					</table>
					</form>
					<?  
				}
				/********** End ***********/
				
				if($show=='add'){
				?>
					<form name="frm_changpass" onsubmit="return validate()" method="post" action="manage-post-process.php" enctype="multipart/form-data">
						<div id="formArea">
							<input type="hidden" name="act" value="insert" />
							<table width="90%" align="center" cellPadding=6 cellSpacing=1 class="tborder">
								<thead>
									<tr>
										<td colspan="2" align="left" class="tbhdr">Add Post</td>
									</tr>
								</thead>
								<tbody>
									<?
									if($msg!='')
									{
									?>
									<tr class="row1">
										<td colspan="2" align="left" class="msg"><?=$msg?></td>
									</tr>
									<?
									}
									?>
									<tr class="row2">
										<td width="42%" align="left">User</td>
										<td width="58%" align="left">
											<?
											$sqlUser	=	array();
											$sqlUser['QUERY']	=	"SELECT `id`,
																			`name`
																		FROM ".DB_USER."
																		WHERE `status`	=	'A'
																		AND   `userType`	=	'vendor'";
											$resUser	=	$mycms->sql_select($sqlUser);
											?>
											<select name="userName"  id="userName" class="fld">
												<option value="">--Select User--</option>
												<?
												foreach($resUser as $key=>$rowUser){
												?>
													<option value="<?=$rowUser['id']?>"><?=$rowUser['name']?></option>
												<?	
												}
												?>
											</select>
										</td>
									</tr>
									<tr class="row1">
										<td align="left">Category</td>
										<td align="left">
											<?
											$sqlCategory	=	array();
											$sqlCategory['QUERY']	=	"SELECT `id`,
																			`name`
																		FROM ".DB_CATEGORY."";
											$resCategory	=	$mycms->sql_select($sqlCategory);
											?>
											<select name="category"  id="category" class="fld">
												<option value="">--Select Category--</option>
												<?
												foreach($resCategory as $key=>$rowCategory){
												?>
													<option value="<?=$rowCategory['id']?>"><?=$rowCategory['name']?></option>
												<?	
												}
												?>
											</select>
										</td>
									</tr>
									<tr class="row2">
										<td align="left">Package</td>
										<td align="left">
											<?
											$sqlPackage	=	array();
											$sqlPackage['QUERY']	=	"SELECT `id`,
																			`name`,
																			`price`,
																			`validity`
																		FROM ".DB_PACKAGE."
																		WHERE `status` = 'A'
																		ORDER BY CAST(price AS UNSIGNED) ASC";
											$resPackage	=	$mycms->sql_select($sqlPackage);
											?>
											<select name="package"  id="package" class="fld">
												<option value="">--Select Package--</option>
												<?
												foreach($resPackage as $key=>$rowPackage){
												?>
													<option value="<?=$rowPackage['id']?>"><?=$rowPackage['name'].' (Rs.'.$rowPackage['price'].' - '.$rowPackage['validity'].' Days)'?></option>
												<?	
												}
												?>
											</select>
										</td>
									</tr>
									<tr class="row1">
										<td align="left">City</td>
										<td align="left">
											<?
											$sqCity	=	array();
											$sqCity['QUERY']	=	"SELECT `id`,
																			`name`
																		FROM ".DB_CITY."
																		WHERE `status` = 'A'
																		ORDER BY `name` ASC";
											$resCity	=	$mycms->sql_select($sqCity);
											?>
											<select name="city"  id="city" class="fld">
												<option value="">--Select City--</option>
												<?
												foreach($resCity as $key=>$rowCity){
												?>
													<option value="<?=$rowCity['id']?>"><?=$rowCity['name']?></option>
												<?	
												}
												?>
											</select>
										</td>
									</tr>
									<tr class="row1">
										<td align="left">Owner Name</td>
										<td align="left">
											<input type="text" name="companyName" id="companyName" class="fld" placeholder="Enter Owner Name"/>
										</td>
									</tr>
									<tr class="row1">
										<td align="left">Mobile</td>
										<td align="left" id="mobileColumn">
											<input type="text" name="companyMobile[]" id="companyMobile" class="fld" placeholder="Enter Mobile Number" />
											<img src="<?=BASE_URL?>webmaster/images/plus.png" width="16" id="addCompanyMobile" />
										</td>
									</tr>
									<tr class="row1">
										<td align="left">Email</td>
										<td align="left" id="emailColumn">
											<input type="text" name="companyEmail" id="companyEmail" class="fld" placeholder="Enter Email" />
										</td>
									</tr>
									<tr class="row1">
										<td align="left">Address</td>
										<td align="left">
											<textarea name="companyAddress" id="companyAddress" class="fld"></textarea>
										</td>
									</tr>
									<tr class="row1">
										<td align="left">Website</td>
										<td align="left">
											<input type="text" name="companyWebsite" id="companyWebsite" class="fld" placeholder="Enter Website"/>
										</td>
									</tr>
									<tr class="row1">
										<td align="left">Title</td>
										<td align="left">
											<textarea name="title" id="title" class="fld"></textarea>
										</td>
									</tr>
									<tr class="row1">
										<td align="left">Description</td>
										<td align="left">
											<textarea name="companyDescription" id="companyDescription" class="fld"></textarea>
										</td>
									</tr>
									<tr class="row1">
										<td align="left">Services</td>
										<td align="left" id="serviceColumn">
											<input type="text" name="companyServices[]" id="companyServices" class="fld" placeholder="Enter Service"/>
											<img src="<?=BASE_URL?>webmaster/images/plus.png" width="16"  id="addCompanyServices"/>
										</td>
									</tr>
									<tr class="row1">
										<td align="left">Year Of Establish</td>
										<td align="left">
											<input type="text" name="yearEstablish" id="yearEstablish" class="fld" placeholder="Enter Year"/>
										</td>
									</tr>
									<tr class="row1">
										<td align="left">Hours Of Operation</td>
										<td align="left">
											<table>
												<tr>
													<td colspan="3"><font color="#FF0000">Enter Time With AM / PM (10 AM - 7 PM)</font></td>
												</tr>
												<tr>
													<td><input type="checkbox" name="dayCount[]" value="0" />Sunday</td>
													<td><input type="text" name="dayFrom[]" placeholder="From" class="fld_small"/> -</td>
													<td><input type="text" name="dayTo[]" placeholder="To" class="fld_small"/></td>
												</tr>
												<tr>
													<td><input type="checkbox" name="dayCount[]" value="1"/>Monday</td>
													<td><input type="text" name="dayFrom[]" placeholder="From" class="fld_small"/> -</td>
													<td><input type="text" name="dayTo[]" placeholder="To" class="fld_small"/></td>
												</tr>
												<tr>
													<td><input type="checkbox" name="dayCount[]" value="2"/>Tuesday</td>
													<td><input type="text" name="dayFrom[]" placeholder="From" class="fld_small"/> -</td>
													<td><input type="text" name="dayTo[]" placeholder="To" class="fld_small"/></td>
												</tr>
												<tr>
													<td><input type="checkbox" name="dayCount[]" value="3"/>Wednesday</td>
													<td><input type="text" name="dayFrom[]" placeholder="From" class="fld_small"/> -</td>
													<td><input type="text" name="dayTo[]" placeholder="To" class="fld_small"/></td>
												</tr>
												<tr>
													<td><input type="checkbox" name="dayCount[]" value="4"/>Thursday</td>
													<td><input type="text" name="dayFrom[]" placeholder="From" class="fld_small"/> -</td>
													<td><input type="text" name="dayTo[]" placeholder="To" class="fld_small"/></td>
												</tr>
												<tr>
													<td><input type="checkbox" name="dayCount[]" value="5"/>Friday</td>
													<td><input type="text" name="dayFrom[]" placeholder="From" class="fld_small"/> -</td>
													<td><input type="text" name="dayTo[]" placeholder="To" class="fld_small"/></td>
												</tr>
												<tr>
													<td><input type="checkbox" name="dayCount[]" value="6"/>Saturday</td>
													<td><input type="text" name="dayFrom[]" placeholder="From" class="fld_small"/> -</td>
													<td><input type="text" name="dayTo[]" placeholder="To" class="fld_small"/></td>
												</tr>
											</table>
										</td>
									</tr>
									<tr class="row2">
										<td align="left">Keywords</td>
										<td align="left" id="keywordsColumn">
											<input type="text" name="keywords[]" id="keywords" class="fld"  placeholder="Enter Keyword"/>
											<img src="<?=BASE_URL?>webmaster/images/plus.png" width="16"  id="addKeywords"/>
										</td>
									</tr>

									

									<tr class="row1">
										<td align="left">Social Link</td>
										<td align="left">
											<table>
												<tr>
													<td><input type="checkbox" name="socialIcon[]" value="0" />Facebook</td>
													<td><input type="text" name="socialUrl[]" placeholder="https://" class="fld"/></td>
												</tr>
												<tr>
													<td><input type="checkbox" name="socialIcon[]" value="1"/>Twiter</td>
													<td><input type="text" name="socialUrl[]" placeholder="https://" class="fld"/></td>
												</tr>
												<tr>
													<td><input type="checkbox" name="socialIcon[]" value="2"/>Google Plus</td>
													<td><input type="text" name="socialUrl[]" placeholder="https://" class="fld"/></td>
												</tr>
												<tr>
													<td><input type="checkbox" name="socialIcon[]" value="3"/>Pinterest</td>
													<td><input type="text" name="socialUrl[]" placeholder="https://" class="fld"/></td>
												</tr>
												<tr>
													<td><input type="checkbox" name="socialIcon[]" value="4"/>Likedin</td>
													<td><input type="text" name="socialUrl[]" placeholder="https://" class="fld"/></td>
												</tr>
											</table>
										</td>
									</tr>
									<!-- <tr class="row1">
										<td align="left">Youtube Link</td>
											<td align="left">
											<input type="text" name="youtubeLink" id="youtubeLink" placeholder="https://" class="fld"/>
										</td>
									</tr> -->
									<!--<tr class="row2">
										<td align="left">Upload File</td>
										<td align="left">
											<input type="file" name="postFile" id="postFile" multiple>
										</td>
									</tr>
									<tr class="row1">
										<td align="left">Upload Thumnails</td>
										<td align="left">
											<input type="file" name="thumnailsFile" id="thumnailsFile" multiple>
										</td>
									</tr>-->
									<tr class="row2">
										<td>&nbsp;</td>
										<td align="left" valign="top">
											<input type="reset" name="Reset" id="Reset" class="resetbttn">
											<input type="submit" name="Save" id="Save" value="Submit" class="loginbttn" >
										</td>
									</tr>
									
								</tbody>
							</table>
						</div>
						<div align="center" id="processArea" style="display:none">
							<p>&nbsp;</p>
							<p>&nbsp;</p>
							<p>&nbsp;</p>
							<p><img src="ajax-loader-xx.gif" border="0"></p>
						</div>
					</form>
					<script>
						$(document).ready(function(){
							var clickCountMobile	=	1;
							$('#addCompanyMobile').click(function(){
								if(clickCountMobile<=2){
									$('#mobileColumn').append('<input type="text" name="companyMobile[]" id="companyMobile" class="fld"  placeholder="Enter Mobile Number"/>');
									clickCountMobile++;
								}
							});
							var clickCountService	=	1;
							$('#addCompanyServices').click(function(){
								if(clickCountService<=4){
									$('#serviceColumn').append('<input type="text" name="companyServices[]" id="companyServices" class="fld" placeholder="Enter Service"/>');
									clickCountService++;
								}
							});
							var clickCountKeywords	=	1;
							$('#addKeywords').click(function(){
								if(clickCountKeywords<=4){
									$('#keywordsColumn').append('<input type="text" name="keywords[]" id="keywords" class="fld"  placeholder="Enter Keyword"/>');
									clickCountKeywords++;
								}
							});
						});
						
						function validate(){
							var userName	=	$('#userName').val();
							var category	=	$('#category').val();
							var package		=	$('#package').val();
							var city		=	$('#city').val();
							var title		=	$('#title').val();
							var keywords	=	$('#keywords').val();
							var postFile	=	$('#postFile').val();
							
							if(userName==''){
								alert('Please Select User Name');
								$('#userName').focus();
								return false;
							}
							if(category==''){
								alert('Please Select Category');
								$('#category').focus();
								return false;
							}
							if(package==''){
								alert('Please Select Package');
								$('#package').focus();
								return false;
							}
							if(city==''){
								alert('Please Select City');
								$('#city').focus();
								return false;
							}
							if(title==''){
								alert('Please Enter Title');
								$('#title').focus();
								return false;
							}
							if(keywords==''){
								alert('Please Enter Keywords');
								$('#keywords').focus();
								return false;
							}
							if(postFile==''){
								alert('Please Select File');
								$('#postFile').focus();
								return false;
							}
						}
					</script>
				<?
				}
				
				if($show=='edit'){
					$sqlPost			=	array();
					$sqlPost['QUERY']	=	"SELECT `post`.*,
													`package`.`id` AS `packageId`,
													`package`.`name` AS `packageName`,
													`package`.`price` AS `packagePrice`,
													`package`.`validity` AS `packageValidity`,
													`category`.`name` AS `categoryName`,
													`user`.`name` AS `userName`,
													`imgGallery`.`id` AS imgId,
													`imgGallery`.`postId` AS imgPostId,
													`imgGallery`.`galleryImageFile`,
													`imgGallery`.`thumbnalImgName`,
													`imgGallery`.`fileType`,
													`imgGallery`.`imageTagLine`,
													`imgGallery`.`altTag`,
													`imgGallery`.`status`

												FROM ".DB_POST." AS `post`
											LEFT JOIN ".DB_PACKAGE." AS `package`
												ON `package`.`id`	=	`post`.`package`
											LEFT JOIN ".DB_CATEGORY." AS `category`
												ON `category`.`id`	=	`post`.`category`
												LEFT JOIN ".DB_IMAGE_GALLERY." AS `imgGallery`
												ON `imgGallery`.`postId`= `post`.`id`
											LEFT JOIN ".DB_USER." AS `user`
												ON `user`.`id`		=	`post`.`userId`
												WHERE `post`.`status` != 	?
												AND	  `post`.`id`		=	?";
												
					$sqlPost['PARAM'][]	=	array('FILD' => '`post`.`status`', 	'DATA' => 'D', 			'TYP' => 's');
					$sqlPost['PARAM'][]	=	array('FILD' => '`post`.`id`', 		'DATA' => $_GET['id'], 	'TYP' => 's');
					
					$resPost			=	$mycms->sql_select($sqlPost);
					$rowPost			=	$resPost[0];
					

					//////////////////////////////////////for edit Gallery img//////////////////////////
					$sqlImg  			=  array();
					$sqlImg['QUERY']	=	"SELECT *
												FROM ".DB_IMAGE_GALLERY."
												WHERE `status` != ?
												AND   `postId` = ?
												ORDER BY `id` DESC";
					$sqlImg['PARAM'][]	=	array('FILD' => 'status', 	'DATA' => 'D', 	'TYP' => 's');		
					$sqlImg['PARAM'][]	=	array('FILD' => 'status', 	'DATA' => $_GET['id'], 	'TYP' => 's');		
					$resImg			    =	$mycms->sql_select($sqlImg);
					$numUser			=	$mycms->sql_numrows($resUser);
				
				?>
					<form name="frm_changpass" onsubmit="" method="post" action="manage-post-process.php">
						<div id="formArea">
							<input type="hidden" name="act" value="update" />
							<input type="hidden" name="id" value="<?=$_GET['id']?>" />
							<table width="90%" align="center" cellPadding=6 cellSpacing=1 class="tborder">
								<thead>
									<tr>
										<td colspan="2" align="left" class="tbhdr">User Registration</td>
									</tr>
								</thead>
								<tbody>
									<?
									if($msg!='')
									{
									?>
									<tr class="row1">
										<td colspan="2" align="left" class="msg"><?=$msg?></td>
									</tr>
									<?
									}
									?>
									<tr class="row2">
										<td width="22%" align="left">User</td>
										<td width="78%" align="left">
											<?
											$sqlUser	=	array();
											$sqlUser['QUERY']	=	"SELECT `id`,
																			`name`
																		FROM ".DB_USER."
																		WHERE `status`	=	'A'
																		AND   `userType`	=	'vendor'";
											$resUser	=	$mycms->sql_select($sqlUser);
											?>
											<select name="userName"  id="userName" class="fld">
												<option value="">--Select User--</option>
												<?
												foreach($resUser as $key=>$rowUser){
												?>
													<option value="<?=$rowUser['id']?>"<? if($rowPost['userId']==$rowUser['id']){?> selected="selected"<?}?>><?=$rowUser['name']?></option>
												<?	
												}
												?>
											</select>
										</td>
									</tr>
									<tr class="row1">
										<td align="left">Category</td>
										<td align="left">
											<?
											$sqlCategory	=	array();
											$sqlCategory['QUERY']	=	"SELECT `id`,
																			`name`
																		FROM ".DB_CATEGORY."";
											$resCategory	=	$mycms->sql_select($sqlCategory);
											?>
											<select name="category"  id="category" class="fld">
												<option value="">--Select Category--</option>
												<?
												foreach($resCategory as $key=>$rowCategory){
												?>
													<option value="<?=$rowCategory['id']?>"<? if($rowPost['category']==$rowCategory['id']){?> selected="selected"<?}?>><?=$rowCategory['name']?></option>
												<?	
												}
												?>
											</select>
										</td>
									</tr>
									<tr class="row2">
										<td align="left">Package</td>
										<td align="left">
											<?
											/*$sqlPackage	=	array();
											$sqlPackage['QUERY']	=	"SELECT `id`,
																			`name`,
																			`price`,
																			`validity`
																		FROM ".DB_PACKAGE."
																		WHERE `status` = 'A'
																		ORDER BY CAST(price AS UNSIGNED) ASC";
											$resPackage	=	$mycms->sql_select($sqlPackage);*/
											?>
											<?php /*?><select name="package"  id="package" class="fld">
												<option value="">--Select Package--</option>
												<?
												foreach($resPackage as $key=>$rowPackage){
												?>
													<option value="<?=$rowPackage['id']?>"<? if($rowPost['package']==$rowPackage['id']){?> selected="selected"<?}?>><?=$rowPackage['name'].' (Rs.'.$rowPackage['price'].' - '.$rowPackage['validity'].' Days)'?></option>
												<?	
												}
												?>
											</select><?php */?>
											<?=$rowPost['packageName'].' (Rs.'.$rowPost['packagePrice'].' - '.$rowPost['packageValidity'].' Days)'?>
										</td>
									</tr>
									<tr class="row1">
										<td align="left">City</td>
										<td align="left">
											<?
											$sqCity	=	array();
											$sqCity['QUERY']	=	"SELECT `id`,
																			`name`
																		FROM ".DB_CITY."
																		WHERE `status` = 'A'
																		ORDER BY `name` ASC";
											$resCity	=	$mycms->sql_select($sqCity);
											?>
											<select name="city"  id="city" class="fld">
												<option value="">--Select City--</option>
												<?
												foreach($resCity as $key=>$rowCity){
												?>
													<option value="<?=$rowCity['id']?>" <?php if($rowCity['id']==$rowPost['cityId']) { echo 'selected'; } ?>><?=$rowCity['name']?></option>
												<?	
												}
												?>
											</select>
										</td>
									</tr>
									<tr class="row1">
										<td align="left">Owner Name</td>
										<td align="left">
											<input type="text" name="companyName" id="companyName" class="fld" placeholder="Enter Owner Name" value="<?=$rowPost['companyName']?>"/>
										</td>
									</tr>
									<tr class="row1">
										<td align="left">Mobile</td>
										<td align="left" id="mobileColumn">
											<?
											$mobileArray	=	explode(',',$rowPost['companyMobile']);
											for($i=0;$i<count($mobileArray);$i++){
											?>
												<input type="text" name="companyMobile[]" id="companyMobile" class="fld" placeholder="Enter Mobile Number" value="<?=$mobileArray[$i]?>" />
											<?
											}
											?>
											<img src="<?=BASE_URL?>webmaster/images/plus.png" width="16" id="addCompanyMobile" />
											<input type="hidden" id="companyMobileCount" value="<?=count($mobileArray)?>" />
										</td>
									</tr>
									<tr class="row1">
										<td align="left">Email</td>
										<td align="left" id="emailColumn">
											<input type="text" name="companyEmail" id="companyEmail" class="fld" placeholder="Enter Email" value="<?=$rowPost['companyEmail']?>" />
										</td>
									</tr>
									<tr class="row1">
										<td align="left">Address</td>
										<td align="left">
											<textarea name="companyAddress" id="companyAddress" class="fld"><?=$rowPost['companyAddress']?></textarea>
										</td>
									</tr>
									<tr class="row1">
										<td align="left">Website</td>
										<td align="left">
											<input type="text" name="companyWebsite" id="companyWebsite" class="fld" placeholder="Enter Website" value="<?=$rowPost['companyWebsite']?>"/>
										</td>
									</tr>
									<tr class="row1">
										<td align="left">Title</td>
										<td align="left">
											<textarea name="title" id="title" class="fld"><?=$rowPost['title']?></textarea>
										</td>
									</tr>
									<tr class="row1">
										<td align="left">Description</td>
										<td align="left">
											<textarea name="companyDescription" id="companyDescription" class="fld"><?=$rowPost['description']?></textarea>
										</td>
									</tr>
									<tr class="row1">
										<td align="left">Services</td>
										<td align="left" id="serviceColumn">
											<?
											$serviceArray	=	explode(',',$rowPost['companyServices']);
											for($i=0;$i<count($serviceArray);$i++){
											?>
												<input type="text" name="companyServices[]" id="companyServices" class="fld" placeholder="Enter Service" value="<?=$serviceArray[$i]?>"/>
											<?
											}
											?>
											<img src="<?=BASE_URL?>webmaster/images/plus.png" width="16"  id="addCompanyServices"/>
											<input type="hidden" id="companyServiceCount" value="<?=count($serviceArray)?>" />
										</td>
									</tr>
									<tr class="row1">
										<td align="left">Year Of Establish</td>
										<td align="left">
											<input type="text" name="yearEstablish" id="yearEstablish" class="fld" placeholder="Enter Year" value="<?=$rowPost['yearEstablish']?>"/>
										</td>
									</tr>
									<tr class="row1">
										<td align="left">Hours Of Operation</td>
										<td align="left">
											<table>
												<?
												$sqlhoursOperation 	=	array();
												$sqlhoursOperation['QUERY']	=	"SELECT * 
																					FROM ".DB_HOUR_OPERATION."
																					WHERE `postId`	=	?
																					AND   `status`	=	?";
																					
												$sqlhoursOperation['PARAM'][]=	array('FILD' => 'postId', 	'DATA' => $rowPost['id'], 	'TYP' => 's');
												$sqlhoursOperation['PARAM'][]=	array('FILD' => 'status', 	'DATA' => 'A', 				'TYP' => 's');	
												$reshoursOperation				=	$mycms->sql_select($sqlhoursOperation);								
												
												$dayNameArray	=	array('0'=>'Sunday',
																		  '1'=>'Monday',
																		  '2'=>'Tuesday',
																		  '3'=>'Wednesday',
																		  '4'=>'Thursday',
																		  '5'=>'Friday',
																		  '6'=>'Saturday'
																		);
												$newDayNameArray	=	array();
												$newFromTimeArray	=	array();
												$newToTimeArray		=	array();
												foreach($reshoursOperation as $key=>$rowhoursOperation){
													foreach($dayNameArray as $dayKey=>$dayName){
														if($rowhoursOperation['dayName']==$dayName){
															$newDayNameArray[]	=	$dayKey;
															$newFromTimeArray[$dayKey]	=	$rowhoursOperation['fromTime'];
															$newToTimeArray[$dayKey]	=	$rowhoursOperation['toTime'];
														}
													}
												}
												/*echo "<pre>";
												print_r($newFromTimeArray);*/
												?>
												<tr>
													<td colspan="3"><font color="#FF0000">Enter Time With AM / PM (10 AM - 7 PM)</font></td>
												</tr>
												<tr>
													<td><input type="checkbox" name="dayCount[]" value="0" <? if(in_array('0', $newDayNameArray)){?> checked="checked"<?}?>/>Sunday</td>
													<td><input type="text" name="dayFrom[]" placeholder="From" class="fld_small" value="<?=in_array('0', $newDayNameArray)?$newFromTimeArray[0]:''?>"/> -</td>
													<td><input type="text" name="dayTo[]" placeholder="To" class="fld_small" value="<?=(in_array('0', $newDayNameArray))?$newToTimeArray[0]:''?>"/></td>
												</tr>
												<tr>
													<td><input type="checkbox" name="dayCount[]" value="1" <? if(in_array('1', $newDayNameArray)){?> checked="checked"<?}?>/>Monday</td>
													<td><input type="text" name="dayFrom[]" placeholder="From" class="fld_small" value="<?=(in_array('1', $newDayNameArray))?$newFromTimeArray[1]:''?>"/> -</td>
													<td><input type="text" name="dayTo[]" placeholder="To" class="fld_small" value="<?=(in_array('1', $newDayNameArray))?$newToTimeArray[1]:''?>"/></td>
												</tr>
												<tr>
													<td><input type="checkbox" name="dayCount[]" value="2" <? if(in_array('2', $newDayNameArray)){?> checked="checked"<?}?>/>Tuesday</td>
													<td><input type="text" name="dayFrom[]" placeholder="From" class="fld_small" value="<?=(in_array('2', $newDayNameArray))?$newFromTimeArray[2]:''?>"/> -</td>
													<td><input type="text" name="dayTo[]" placeholder="To" class="fld_small" value="<?=(in_array('2', $newDayNameArray))?$newToTimeArray[2]:''?>"/></td>
												</tr>
												<tr>
													<td><input type="checkbox" name="dayCount[]" value="3" <? if(in_array('3', $newDayNameArray)){?> checked="checked"<?}?>/>Wednesday</td>
													<td><input type="text" name="dayFrom[]" placeholder="From" class="fld_small" value="<?=(in_array('3', $newDayNameArray))?$newFromTimeArray[3]:''?>"/> -</td>
													<td><input type="text" name="dayTo[]" placeholder="To" class="fld_small" value="<?=(in_array('3', $newDayNameArray))?$newToTimeArray[3]:''?>"/></td>
												</tr>
												<tr>
													<td><input type="checkbox" name="dayCount[]" value="4" <? if(in_array('4', $newDayNameArray)){?> checked="checked"<?}?>/>Thursday</td>
													<td><input type="text" name="dayFrom[]" placeholder="From" class="fld_small" value="<?=(in_array('4', $newDayNameArray))?$newFromTimeArray[4]:''?>"/> -</td>
													<td><input type="text" name="dayTo[]" placeholder="To" class="fld_small" value="<?=(in_array('4', $newDayNameArray))?$newToTimeArray[4]:''?>"/></td>
												</tr>
												<tr>
													<td><input type="checkbox" name="dayCount[]" value="5" <? if(in_array('5', $newDayNameArray)){?> checked="checked"<?}?>/>Friday</td>
													<td><input type="text" name="dayFrom[]" placeholder="From" class="fld_small" value="<?=(in_array('5', $newDayNameArray))?$newFromTimeArray[5]:''?>"/> -</td>
													<td><input type="text" name="dayTo[]" placeholder="To" class="fld_small" value="<?=(in_array('5', $newDayNameArray))?$newToTimeArray[5]:''?>"/></td>
												</tr>
												<tr>
													<td><input type="checkbox" name="dayCount[]" value="6" <? if(in_array('6', $newDayNameArray)){?> checked="checked"<?}?>/>Saturday</td>
													<td><input type="text" name="dayFrom[]" placeholder="From" class="fld_small" value="<?=(in_array('6', $newDayNameArray))?$newFromTimeArray[6]:''?>"/> -</td>
													<td><input type="text" name="dayTo[]" placeholder="To" class="fld_small" value="<?=(in_array('6', $newDayNameArray))?$newToTimeArray[6]:''?>"/></td>
												</tr>
											</table>
										</td>
									</tr>
									<tr class="row2">
										<td align="left">Keywords</td>
										<td align="left" id="keywordsColumn">
											<?
											$keywordArray	=	explode(',',$rowPost['keywords']);
											for($i=0;$i<count($keywordArray);$i++){
											?>
												<input type="text" name="keywords[]" id="keywords" class="fld"  placeholder="Enter Keyword" value="<?=$keywordArray[$i]?>"/>
											<?
											}
											?>
											<img src="<?=BASE_URL?>webmaster/images/plus.png" width="16"  id="addKeywords"/>
											<input type="hidden" id="keywordCount" value="<?=count($keywordArray)?>" />
										</td>
									</tr>

									
									
									<tr class="row1">
										<td align="left">Social Link</td>
										<td align="left">
											<?
											$sqlSocialLink	=	array();
											$sqlSocialLink['QUERY']	=	"SELECT * 
																			FROM ".DB_POST_SOCIAL_LINK."
																			WHERE `postId`	=	?
																			AND   `status`	=	?";
																			
											$sqlSocialLink['PARAM'][]=	array('FILD' => 'postId', 	'DATA' => $rowPost['id'], 	'TYP' => 's');
											$sqlSocialLink['PARAM'][]=	array('FILD' => 'status', 	'DATA' => 'A', 				'TYP' => 's');
											$resSocialLink			=	$mycms->sql_select($sqlSocialLink);
											
											$socialLinkNameArray	=	array('0'=>'Facebook',
																			  '1'=>'Twiter',
																			  '2'=>'Google-Plus',
																			  '3'=>'Pinterest',
																			  '4'=>'Likedin'
																			);
											$newsocialLinkNameArray	=	array();
											$newsocialLinkUrlArray	=	array();
											
											foreach($resSocialLink as $key=>$rowSocialLink){
												foreach($socialLinkNameArray as $socialKey=>$socialLinkName){
													if($rowSocialLink['socialLinkName']==$socialLinkName){
														$newsocialLinkNameArray[]	=	$socialKey;
														$newsocialLinkUrlArray[$socialKey]	=	$rowSocialLink['url'];
													}
												}
											}
											/*echo "<pre>";
											print_r($newsocialLinkUrlArray);*/
											?>
											<table>
												<tr>
													<td><input type="checkbox" name="socialIcon[]" value="0" <? if(in_array('0', $newsocialLinkNameArray)){?> checked="checked"<?}?> />Facebook</td>
													<td><input type="text" name="socialUrl[]" placeholder="https://" class="fld" value="<?=(in_array('0', $newsocialLinkNameArray))?$newsocialLinkUrlArray[0]:''?>"/></td>
												</tr>
												<tr>
													<td><input type="checkbox" name="socialIcon[]" value="1" <? if(in_array('1', $newsocialLinkNameArray)){?> checked="checked"<?}?>/>Twiter</td>
													<td><input type="text" name="socialUrl[]" placeholder="https://" class="fld" value="<?=(in_array('1', $newsocialLinkNameArray))?$newsocialLinkUrlArray[1]:''?>"/></td>
												</tr>
												<tr>
													<td><input type="checkbox" name="socialIcon[]" value="2" <? if(in_array('2', $newsocialLinkNameArray)){?> checked="checked"<?}?>/>Google Plus</td>
													<td><input type="text" name="socialUrl[]" placeholder="https://" class="fld" value="<?=(in_array('2', $newsocialLinkNameArray))?$newsocialLinkUrlArray[2]:''?>"/></td>
												</tr>
												<tr>
													<td><input type="checkbox" name="socialIcon[]" value="3" <? if(in_array('3', $newsocialLinkNameArray)){?> checked="checked"<?}?>/>Pinterest</td>
													<td><input type="text" name="socialUrl[]" placeholder="https://" class="fld" value="<?=(in_array('3', $newsocialLinkNameArray))?$newsocialLinkUrlArray[3]:''?>"/></td>
												</tr>
												<tr>
													<td><input type="checkbox" name="socialIcon[]" value="4" <? if(in_array('4', $newsocialLinkNameArray)){?> checked="checked"<?}?>/>Likedin</td>
													<td><input type="text" name="socialUrl[]" placeholder="https://" class="fld" value="<?=(in_array('4', $newsocialLinkNameArray))?$newsocialLinkUrlArray[4]:''?>"/></td>
												</tr>
											</table>
										</td>
									</tr>

										<tr>
										<?php if($rowPost['file'] || $rowPost['youtubeLink'] ){ ?>
											 <td>Uploaded File</td>
										<?php }else{ ?>
											<td>Upload Gallery Image</td>
										<?php } ?>
											<td align="left" valign="top">
											<?
											$fileType	= 	getFileType($rowPost['id']);
											if($fileType=='image'){
											?>
												<img src="<?=BASE_URL?>uploads/post/<?=$rowPost['file']?>" width="200px" height="100px">
											<?
											}
											else if($fileType=='video'){
											?>
												<video height="150px" width="300px" controls>
													 <source src="<?=BASE_URL?>uploads/post/<?=$rowPost['file']?>" type="<?=$rowPost['fileType']?>">
												</video>
											<?
											}else if($rowPost['embededLinkType']=='youtu.be') {?>
												<iframe width="300" height="175" src="<?php echo $rowPost['youtubeLink'];?>" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
												
										<?php }else{?>

											<table id="edit_galleryImg">
												<input type="hidden" name="BASE_URL" id="BASE_URL" value="<?php echo BASE_URL;?>" />
												<thead>
													<tbody>
														  <tr>
														    <th>Image</th>
														    <th>TagLine</th>
														    <th>Alt Tag</th>
														    <th>Action</th>
														  </tr>
														  <?php foreach ($resImg as $key => $value) {
														  	$key++;
														  	?>
														  	<tr>

														  		<input type="hidden" id="id<?= $key;?>"  class="id" value="<?php echo $value['id']; ?>">
														  		<input type="hidden" id="postId"  class="postId" value="<?php echo $value['postId']; ?>">
														  		<td style="width:2%"><img src="<?=BASE_URL?>webmaster/uploads/thumbs/<?=$value['thumbnalImgName']?>" height='50px' width='120px'/>
														  			<input type="file" id="curr<?= $key;?>" class="abc" name="imageFile[]" style="margin-top: 5px;width:186px;"></td>
														  		<td style="width:70%"><input type="text" name="tag[]" class="tag" id="tag<?= $key;?>" value="<?=$value['imageTagLine']?>" style="width:100%"></td>
														  		<td style="width:70%;"><input type="text" class="altTag" id="altTag<?= $key;?>" name="altTag[]" value="<?=$value['altTag']?>"></td>
														  	    <td><span class='delete' data-removeItem = "<?php echo $value['id']; ?>" id='del_<?php echo $value['id']; ?>'>
																	<img src="images/drop.gif" alt="Delete" title="Delete" width="16" height="16" border="0"  /></span> &nbsp;&nbsp;
																</td>
														  	</tr>
														  <?php  } ?>
														 <tr class="row2">
															<td align="center" valign="top" colspan="4" >
																<input type="button" name="Save" id="Save" value="UpdateGalleryImage" onclick="saveImg()" class="greenbutton">
															</td>
														</tr>
													</tbody>
												</thead>
											</table>
										 <?php } ?>
										</td>
										</tr>

									<tr class="row2">
										<td>Upload New File</td>
										<td align="left" valign="top">
											<input type="button" name="Save" id="Save" value="Upload" onclick="window.location.href='manage-post-upload.php?type=update&id=<?=$_GET['id']?>'" class="greenbutton" >
										</td>
									</tr>
									<tr class="row2">
										<td>&nbsp;</td>
										<td align="left" valign="top">
											<input type="reset" name="Reset" id="Reset" class="resetbttn">
											<input type="submit" name="Save" id="Save" value="Submit" class="loginbttn" >
										</td>
									</tr>
								</tbody>
							</table>
						</div>
						<div align="center" id="processArea" style="display:none">
							<p>&nbsp;</p>
							<p>&nbsp;</p>
							<p>&nbsp;</p>
							<p><img src="ajax-loader-xx.gif" border="0"></p>
						</div>
					</form>
					<script type="text/javascript">
					function HandleBrowseClick()
					{
					    var fileinput = document.getElementById("browse");
					    fileinput.click();
					}

					function Handlechange()
					{
					    var fileinput = document.getElementById("browse");
					    var textinput = document.getElementById("filename");
					    textinput.value = fileinput.value;
					}
			</script>
					<script>
						$(document).ready(function(){
							var clickCountMobile	=	$('#companyMobileCount').val();
							//var companyMobileCount	=	$('#companyMobileCount').val();
							$('#addCompanyMobile').click(function(){
								if(clickCountMobile<=2){
									$('#mobileColumn').append('<input type="text" name="companyMobile[]" id="companyMobile" class="fld"  placeholder="Enter Mobile Number"/>');
									clickCountMobile++;
								}
								else {
									alert('Limit Exceeded.');
								}
							});
							var clickCountService	=	$('#companyServiceCount').val();
							$('#addCompanyServices').click(function(){
								if(clickCountService<=4){
									$('#serviceColumn').append('<input type="text" name="companyServices[]" id="companyServices" class="fld" placeholder="Enter Service"/>');
									clickCountService++;
								}
								else {
									alert('Limit Exceeded.');
								}
							});
							var clickCountKeywords	=	$('#keywordCount').val();
							$('#addKeywords').click(function(){
								if(clickCountKeywords<=4){
									$('#keywordsColumn').append('<input type="text" name="keywords[]" id="keywords" class="fld"  placeholder="Enter Keyword"/>');
									clickCountKeywords++;
								}
								else {
									alert('Limit Exceeded.');
								}
							});
						});
						
						function validate(){
							var name			=	$('#name').val();
							var mobile			=	$('#mobile').val();
							var email			=	$('#email').val();
							var expr			= /^([a-zA-Z0-9_.-])+@([a-zA-Z0-9_.-])+\.([a-zA-Z])+([a-zA-Z])+/;
							
							if(name==''){
								alert('Please Enter Name');
								$('#name').focus();
								return false;
							}
							if(mobile==''){
								alert('Please Enter Mobile Number');
								$('#mobile').focus();
								return false;
							}
							if(mobile!=''){
								if(isNaN(mobile)){
									alert('Please Enter Valid Mobile Number');
									$('#mobile').focus();
									return false;
								}
								var mobileLen	=	mobile.length;
								if(mobileLen!=10){
									alert('Please Enter 10-digit Mobile Number');
									$('#mobile').focus();
									return false;
								}
							}
							if(email==''){
								alert('Please Enter E-mail');
								$('#email').focus();
								return false;
							}
							if(email!=''){
								if(expr.test(email)==false){
									alert('Please Enter Valid E-mail');
									$('#email').focus();
									return false;
								}
							}
						}
					</script>


		<script>
			function saveImg() {

		    var clickCountImgBox	    =	$(".abc").length;
			let BASE_URL	    	    =	$('#BASE_URL').val();
			var form_data = new FormData(); // Creating object of FormData class
			var file_data = '';
			let j = 0;
			for(let i=1;i<=clickCountImgBox;i++){
				let tagname = '#tag'+i;
				let altTag = '#altTag'+i;
				let id     = '#id'+i;
				file_data = $('#curr'+i).prop("files");
				file_data = file_data[0]; // Getting the properties of file from file field
			    console.log(file_data);
			    form_data.append('image['+i+']', file_data);
			    form_data.append('tag['+i+']', $(tagname).val());
			    form_data.append('altTag['+i+']', $(altTag).val());
			    form_data.append('imgId['+i+']', $(id).val());
			    form_data.append('postId', $(".postId").val());
			    form_data.append('total', clickCountImgBox);
			}

			$.ajax({
			  url:   BASE_URL+'webmaster/editGalleryImgProcess.php',
			  type: 'POST',
			  processData: false, // important
			  contentType: false, // important
			  dataType : 'html',
			  data: form_data,
			  success : function(res) {
			  	console.log(res);
			  	if(res){
			 		window.location.href="manage-post.php?m=1";  
			  	}
			  }
			});
			
		}
		</script>
				<?
				
				}
				
				if($show=='view'){
					$sqlPost				=	array();
				$sqlPost['QUERY']	        =	"SELECT `post`.*,
													`package`.`id` AS `packageId`,
													`package`.`name` AS `packageName`,
													`package`.`price` AS `packagePrice`,
													`package`.`validity` AS `packageValidity`,
													`category`.`name` AS `categoryName`,
													`user`.`name` AS `userName`,
													`imgGallery`.`id` AS imgId,
													`imgGallery`.`postId` AS imgPostId,
													`imgGallery`.`galleryImageFile`,
													`imgGallery`.`thumbnalImgName`,
													`imgGallery`.`fileType`,
													`imgGallery`.`imageTagLine`,
													`imgGallery`.`altTag`,
													`imgGallery`.`status`

												FROM ".DB_POST." AS `post`
											LEFT JOIN ".DB_PACKAGE." AS `package`
												ON `package`.`id`	=	`post`.`package`
											LEFT JOIN ".DB_CATEGORY." AS `category`
												ON `category`.`id`	=	`post`.`category`
											LEFT JOIN ".DB_IMAGE_GALLERY." AS `imgGallery`
												ON `imgGallery`.`postId`= `post`.`id`
											LEFT JOIN ".DB_USER." AS `user`
												ON `user`.`id`		=	`post`.`userId`
												WHERE `post`.`status` != 	?
												AND	  `post`.`id`	   =	?";
												
					$sqlPost['PARAM'][]	=	array('FILD' => '`post`.`status`', 	'DATA' => 'D', 			'TYP' => 's');
					$sqlPost['PARAM'][]	=	array('FILD' => '`post`.`id`', 		'DATA' => $_GET['id'], 	'TYP' => 's');
					$resPost			=	$mycms->sql_select($sqlPost);
					$rowPost			=	$resPost[0];

					/////////////////////////////For Fetch image Gallery////////////////////
					$sqlImg  			=  array();
					$sqlImg['QUERY']	=	"SELECT *
												FROM ".DB_IMAGE_GALLERY."
												WHERE `status` != ?
												AND   `postId` = ?
												ORDER BY `id` DESC";
					$sqlImg['PARAM'][]	=	array('FILD' => 'status', 	'DATA' => 'D', 	'TYP' => 's');		
					$sqlImg['PARAM'][]	=	array('FILD' => 'status', 	'DATA' => $_GET['id'], 	'TYP' => 's');		
					$resImg			    =	$mycms->sql_select($sqlImg);
					$numUser			=	$mycms->sql_numrows($resUser);
					/*echo '<pre>';
					print_r($resImg);
					exit;*/

				?>
					<form name="frm_changpass" onsubmit="" method="post" action="manage-post-process.php">
						<div id="formArea">
							<input type="hidden" name="act" value="update" />
							<input type="hidden" name="id" value="<?=$_GET['id']?>" />
							<table width="90%" align="center" cellPadding=6 cellSpacing=1 class="tborder">
								<thead>
									<tr>
										<td colspan="2" align="left" class="tbhdr">User Registration</td>
									</tr>
								</thead>
								<tbody>
									<?
									if($msg!='')
									{
									?>
									<tr class="row1">
										<td colspan="2" align="left" class="msg"><?=$msg?></td>
									</tr>
									<?
									}
									?>
									<tr class="row2">
										<?php if($rowPost['file'] || $rowPost['youtubeLink'] ){?>
										<td >Uploaded File</td>
									<?php }else{?>
										<td>Uploaded Gallery Image</td>
										<?php }?>
										<td align="left" valign="top">
											<?
											$fileType	= 	getFileType($rowPost['id']);
											if($fileType =='image'){
											?>
												<img src="<?=BASE_URL?>uploads/post/<?=$rowPost['file']?>" width="200px" height="100px">
											<?
											}
											else if($fileType=='video'){
											?>
												<video height="150px" width="300px" controls>
													 <source src="<?=BASE_URL?>uploads/post/<?=$rowPost['file']?>" type="<?=$rowPost['fileType']?>">
												</video>
											<?
											}else if($rowPost['embededLinkType']=='youtu.be'){?>
												<iframe width="300" height="175" src="<?php echo $rowPost['youtubeLink'];?>" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
										<?php }else{
											?>
											<table id="galleryImg">
												<thead>
													<tbody>
														  <tr>
														    <th>Image</th>
														    <th>TagLine</th>
														    <th>Alt Tag</th>
														  </tr>
														  <?php foreach ($resImg as $key => $value) {?>
														  	<tr>
														  		<td style="width:120px"><img src="<?=BASE_URL?>webmaster/uploads/thumbs/<?=$value['thumbnalImgName']?>" height='50px' width='120px'/></td>
														  		<td><?=$value['imageTagLine']?></td>
														  		<td style="width:112px;"><?=$value['altTag']?></td>
														  	</tr>
														  <?php  } ?>
													</tbody>
												</thead>
											</table>

										<?php } ?>
										</td>
									</tr>
									
									<tr class="row2">
										<td>Uploaded Thumnails</td>
										<td align="left" valign="top">
											<img src="<?=BASE_URL?>uploads/thumnails/<?=$rowPost['thumnails']?>" width="200px" height="100px">
										</td>
									</tr>
									<tr class="row2">
										<td width="42%" align="left">User</td>
										<td width="58%" align="left">
											<?=$rowPost['userName']?>
										</td>
									</tr>
									<tr class="row1">
										<td align="left">Category</td>
										<td align="left">
											<?=$rowPost['categoryName']?>
										</td>
									</tr>
									<tr class="row2">
										<td align="left">Package</td>
										<td align="left">
											<?=$rowPost['packageName'].' (Rs.'.$rowPost['packagePrice'].' - '.$rowPost['packageValidity'].' Days)'?>
										</td>
									</tr>
									<tr class="row1">
										<td align="left">City</td>
										<td align="left">
											<?php
												$sqlCity = array();
												$sqlCity['QUERY'] 		=	"SELECT `name` FROM ".DB_CITY." WHERE `status` = ? AND `id` = ?";
												$sqlCity['PARAM'][]	=	array('FILD' => 'status', 'DATA' =>'A', 'TYP' => 's');
												$sqlCity['PARAM'][]	=	array('FILD' => 'id', 'DATA' =>$rowPost['cityId'], 'TYP' => 's');
												$resCity			=	$mycms->sql_select($sqlCity);
												echo $resCity[0]['name'];
											?>
										</td>
									</tr>
									<tr class="row1">
										<td align="left">Company Name</td>
										<td align="left">
											<?=$rowPost['companyName']?>
										</td>
									</tr>
									<tr class="row1">
										<td align="left">Mobile</td>
										<td align="left" id="mobileColumn">
											<?=$rowPost['companyMobile']?>
										</td>
									</tr>
									<tr class="row1">
										<td align="left">Email</td>
										<td align="left" id="emailColumn">
											<?=$rowPost['companyEmail']?>
										</td>
									</tr>
									<tr class="row1">
										<td align="left">Address</td>
										<td align="left">
											<?=$rowPost['companyAddress']?>
										</td>
									</tr>
									<tr class="row1">
										<td align="left">Website</td>
										<td align="left">
											<?=$rowPost['companyWebsite']?>
										</td>
									</tr>
									<tr class="row1">
										<td align="left">Title</td>
										<td align="left">
											<?=$rowPost['title']?>
										</td>
									</tr>
									<tr class="row1">
										<td align="left">Description</td>
										<td align="left">
											<?=$rowPost['description']?>
										</td>
									</tr>
									<tr class="row1">
										<td align="left">Services</td>
										<td align="left" id="serviceColumn">
											<?=$rowPost['companyServices']?>
										</td>
									</tr>
									<tr class="row1">
										<td align="left">Year Of Establish</td>
										<td align="left">
											<?=$rowPost['yearEstablish']?>
										</td>
									</tr>
									<tr class="row1">
										<td align="left">Hours Of Operation</td>
										<td align="left">
											<table>
												<?
												$sqlhoursOperation 	=	array();
												$sqlhoursOperation['QUERY']	=	"SELECT * 
																					FROM ".DB_HOUR_OPERATION."
																					WHERE `postId`	=	?
																					AND   `status`	=	?";
																					
												$sqlhoursOperation['PARAM'][]=	array('FILD' => 'postId', 	'DATA' => $rowPost['id'], 	'TYP' => 's');
												$sqlhoursOperation['PARAM'][]=	array('FILD' => 'status', 	'DATA' => 'A', 				'TYP' => 's');	
												$reshoursOperation				=	$mycms->sql_select($sqlhoursOperation);		
												foreach($reshoursOperation as $key=>$rowhoursOperation){
												?>
													<tr>
														<td><?=$rowhoursOperation['dayName']?></td>
														<td><?=$rowhoursOperation['fromTime']?> -</td>
														<td><?=$rowhoursOperation['toTime']?></td>
													</tr>
												<?
												}		
												?>
											</table>
										</td>
									</tr>
									<tr class="row2">
										<td align="left">Keywords</td>
										<td align="left" id="keywordsColumn">
											<?=$rowPost['keywords']?>
										</td>
									</tr>

									<tr class="row1">
										<td align="left">Social Link</td>
										<td align="left">
											<?
											$sqlSocialLink	=	array();
											$sqlSocialLink['QUERY']	=	"SELECT * 
																			FROM ".DB_POST_SOCIAL_LINK."
																			WHERE `postId`	=	?
																			AND   `status`	=	?";
																			
											$sqlSocialLink['PARAM'][]=	array('FILD' => 'postId', 	'DATA' => $rowPost['id'], 	'TYP' => 's');
											$sqlSocialLink['PARAM'][]=	array('FILD' => 'status', 	'DATA' => 'A', 				'TYP' => 's');
											$resSocialLink			=	$mycms->sql_select($sqlSocialLink);
											?>
											<table>
												<?
												foreach($resSocialLink as $key=>$rowSocialLink){
												?>
													<tr>
														<td><?=$rowSocialLink['socialLinkName']?></td>
														<td><?=$rowSocialLink['url']?></td>
													</tr>
												<?
												}
												?>
											</table>
										</td>
									</tr>
									<tr class="row2">
										<td>&nbsp;</td>
										<td align="left" valign="top">
											<input type="button" name="back" id="back" class="resetbttn" value="Back" onclick="window.location.href='manage-post.php';">
										</td>
									</tr>
								</tbody>
							</table>
						</div>
						<div align="center" id="processArea" style="display:none">
							<p>&nbsp;</p>
							<p>&nbsp;</p>
							<p>&nbsp;</p>
							<p><img src="ajax-loader-xx.gif" border="0"></p>
						</div>
					</form>
				<?
				}
			
				if($show=='renew'){
					$sqlPost			=	array();
					$sqlPost['QUERY']	=	"SELECT `post`.*,
													`package`.`id` AS `packageId`,
													`package`.`name` AS `packageName`,
													`package`.`price` AS `packagePrice`,
													`package`.`validity` AS `packageValidity`,
													`category`.`name` AS `categoryName`,
													`user`.`name` AS `userName`
												FROM ".DB_POST." AS `post`
											LEFT JOIN ".DB_PACKAGE." AS `package`
												ON `package`.`id`	=	`post`.`package`
											LEFT JOIN ".DB_CATEGORY." AS `category`
												ON `category`.`id`	=	`post`.`category`
											LEFT JOIN ".DB_USER." AS `user`
												ON `user`.`id`		=	`post`.`userId`
												WHERE `post`.`status` != 	?
												AND	  `post`.`id`		=	?";
												
					$sqlPost['PARAM'][]	=	array('FILD' => '`post`.`status`', 	'DATA' => 'D', 			'TYP' => 's');
					$sqlPost['PARAM'][]	=	array('FILD' => '`post`.`id`', 		'DATA' => $_GET['id'], 	'TYP' => 's');
					
					$resPost			=	$mycms->sql_select($sqlPost);
					$rowPost			=	$resPost[0];
				
				?>
					<form name="frm_changpass" onsubmit="return confirmRenewal();" method="post" action="manage-post-process.php">
						<div id="formArea">
							<input type="hidden" name="act" value="renew" />
							<input type="hidden" name="id" value="<?=$_GET['id']?>" />
							<table width="90%" align="center" cellPadding=6 cellSpacing=1 class="tborder">
								<thead>
									<tr>
										<td colspan="2" align="left" class="tbhdr">User Registration</td>
									</tr>
								</thead>
								<tbody>
									<?
									if($msg!='')
									{
									?>
									<tr class="row1">
										<td colspan="2" align="left" class="msg"><?=$msg?></td>
									</tr>
									<?
									}
									?>
									<tr class="row1">
										<td align="left">Title</td>
										<td align="left">
											<?=$rowPost['title']?>
										</td>
									</tr>
									<tr class="row2">
										<td>Uploaded File</td>
										<td align="left" valign="top">
											<?
											$fileType	= 	getFileType($rowPost['id']);
											if($fileType=='image'){
											?>
												<img src="<?=BASE_URL?>uploads/post/<?=$rowPost['file']?>" width="200px" height="100px">
											<?
											}
											else if($fileType=='video'){
											?>
												<video height="150px" width="300px" controls>
													 <source src="<?=BASE_URL?>uploads/post/<?=$rowPost['file']?>" type="<?=$rowPost['fileType']?>">
												</video>
											<?
											}
											?>
										</td>
									</tr>
									<tr class="row2">
										<td>Uploaded Thumnails</td>
										<td align="left" valign="top">
											<img src="<?=BASE_URL?>uploads/thumnails/<?=$rowPost['thumnails']?>" width="200px" height="100px">
										</td>
									</tr>
									<tr class="row2">
										<td width="42%" align="left">User</td>
										<td width="58%" align="left">
											<?=$rowPost['userName']?>
										</td>
									</tr>
									<tr class="row1">
										<td align="left">Category</td>
										<td align="left">
											<?=$rowPost['categoryName']?>
										</td>
									</tr>
									<tr class="row2">
										<td align="left">Package</td>
										<td align="left">
											<?
											$sqlPackage	=	array();
											$sqlPackage['QUERY']	=	"SELECT `id`,
																			`name`,
																			`price`,
																			`validity`
																		FROM ".DB_PACKAGE."
																		WHERE `status` = 'A'
																		ORDER BY CAST(price AS UNSIGNED) ASC";
											$resPackage	=	$mycms->sql_select($sqlPackage);
											?>
											<select name="package"  id="package" class="fld">
												<option value="">--Select Package--</option>
												<?
												foreach($resPackage as $key=>$rowPackage){
												?>
													<option value="<?=$rowPackage['id']?>"<? if($rowPost['package']==$rowPackage['id']){?> selected="selected"<?}?>><?=$rowPackage['name'].' (Rs.'.$rowPackage['price'].' - '.$rowPackage['validity'].' Days)'?></option>
												<?	
												}
												?>
											</select>
										</td>
									</tr>
									<tr class="row1">
										<td align="left">Company Name</td>
										<td align="left">
											<?=$rowPost['companyName']?>
										</td>
									</tr>
									<tr class="row2">
										<td>&nbsp;</td>
										<td align="left" valign="top">
											<input type="submit" name="Save" id="Save" value="Renew" class="loginbttn" >
										</td>
									</tr>
								</tbody>
							</table>
						</div>
						<div align="center" id="processArea" style="display:none">
							<p>&nbsp;</p>
							<p>&nbsp;</p>
							<p>&nbsp;</p>
							<p><img src="ajax-loader-xx.gif" border="0"></p>
						</div>
					</form>
					<script>
						function confirmRenewal(){
							if(confirm('Are you sure To Renew This Listing with this package ?')){
								return true;
							}
							else {
								return false;
							}
						}
					</script>
				<?
				}
				?>
			</td>
		</tr>
	</tbody>
</table>

<? page_footer(); ?>