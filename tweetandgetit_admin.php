<?php
if(isset($_GET['bid']))
{	
	$id=trim($_GET['bid']);
	$b_name=__("Update your button",TWEEGI_TRASNLATE);
	$editmode=true;
	$editmodestr="edit";
	global $wpdb,$tbl_tweetandgetit_buttons;
	$row = $wpdb->get_row(" SELECT  *  FROM ".$tbl_tweetandgetit_buttons." where id=$id");
	if(isset($row))
	{
		$db_btnname=$row->button_name;
		$db_usernames=$row->twitter_name;
		$db_tweet=$row->tweet;
		if(strpos($row->file_path,"TWEEGE_FILE:") == 1)
		{
			$row->file_path =  basename(str_replace(":TWEEGE_FILE:",'',$row->file_path));
		}
		$db_file=$row->file_path;
	}
	
}
else
{
	$b_name=__("Create your button",TWEEGI_TRASNLATE);
	$editmodestr="add";
}
	$destination_path = $_GET['path'];
	if(isset($destination_path))
	{

   $result = 0;
   
   $target_path = $destination_path . basename( $_FILES['File_sharing']['name']);
   if(@move_uploaded_file($_FILES['File_sharing']['tmp_name'], $target_path)) {
      $result = 1;
   }
   

?>


<script language="javascript" type="text/javascript">window.top.window.tweegi_stopUpload(<?php echo $result; ?>,'<?echo $target_path?>','<? echo basename( $_FILES['File_sharing']['name'])?>');</script>  <?
die();
}
if(!isset($_POST['promotional_tweet']))
	{
	if(isset($_GET['bid']))
		$TWEET=$db_tweet;
	else
		$TWEET= __("ex: Just downloaded my twitter background for free from http://viuu.co.uk @pointofviuu. Check it out!",TWEEGI_TRASNLATE);
	}
else
	$TWEET=$_POST['promotional_tweet'];
if(isset($_POST['twittername']))	
	$twtname=$_POST['twittername'];
	else if(isset($_GET['bid']))
		$twtname=$db_usernames;
if(isset($_POST['btn_name']))	
	$btnname=$_POST['btn_name'];
	else if(isset($_GET['bid']))
	$btnname=$db_btnname;
if(isset($_POST['urlfile']))
	$urlname=$_POST['urlfile'];
	else if(isset($_GET['bid']))
	{	
		$urlname = $db_file;

	}
 $destination_path = TWEEGI_UPLOAD_PATH.DIRECTORY_SEPARATOR;
 tweegi_make_upload_dir();

?>

<!-- Javascripts -->
<script type="text/javascript" src="<?echo TWEEGI_URLPATH."/"?>js/jquery.min.js"></script>
<script type="text/javascript" src="<?echo TWEEGI_URLPATH."/"?>js/jquery.superfish.min.js"></script>
<script type="text/javascript" src="<?echo TWEEGI_URLPATH."/"?>js/jquery.tools.min.js"></script>
<script type="text/javascript" src="<?echo TWEEGI_URLPATH."/"?>js/tweegi.js"></script>
<script type="text/javascript">
function tweegi_startUpload(){

      document.getElementById('f1_upload_process').style.display = 'block';
      document.getElementById('upload_result').style.display = 'none';
      return true;
}

function tweegi_stopUpload(success,file,filename){
      var result = '';
      if (success == 1){
         result = "<?php echo __('File ',TWEEGI_TRASNLATE)?>";
         result = result + '<b><font color="red">' +  filename + "</font></b>" + "<?php echo __(' was uploaded successfully!',TWEEGI_TRASNLATE)?>";
		 document.getElementById('submit').disabled=true;
		 document.getElementById('urlfile').disabled=true;
		 document.getElementById('urlfile').value="";
		 document.getElementById('uploaded').value=file;
		 
      }
      else {
         result = "<?php echo  __('There was an error during file upload',TWEEGI_TRASNLATE)?>";
      }
      document.getElementById('f1_upload_process').style.display = 'none';
      document.getElementById('upload_result').innerHTML = result;
      document.getElementById('upload_result').style.display = 'block';      
      return true;   
}


function tweegi_tweet_counter()
{
	
	var counter = document.getElementById('twtlbl');
	var myform = document.getElementById('contact_form');
	var tweet = myform.promotional_tweet.value;
	var count=140 - tweet.length;
	if ( count<0 ) 
	{
		counter.innerHTML = '<font color="red">'+count+'</font>';
	}
	else
		counter.innerHTML = '<font color="green">'+count+'</font>';
	
	
}

function tweegi_myalert()
{
	
	var nameReg = /^([a-zA-Z0-9 ]+[a-zA-Z0-9, _]*)$/;
	var nameReg2 = /^(@+.*)$/;
	var btnReg = /^([a-zA-Z]+[a-zA-Z0-9]*)$/;
	var myform = document.getElementById('contact_form');
	var iserror = 0;
	var error_msg = "";
	var formmode = myform.mode.value;
	var id = myform.bid.value;
	var btnname = myform.btn_name.value;
	var twittername = myform.twittername.value;
	var tweet = myform.promotional_tweet.value;
	
	var upload_path = "";
	var file_location = "";
	var span1="";
	var blkqut="";
	
	if((btnname.length <=0))
	{
		iserror =1;
		error_msg+="&quot;<?php echo __("Name of your button shouldn't be empty",TWEEGI_TRASNLATE);?>&quot; <br>";

	}
	
	if((twittername.length <= 0))
	{
		

		iserror =1;

		error_msg+="&quot;<?php echo __("Twitter username",TWEEGI_TRASNLATE)?>&quot;<br>";

	}
	else if(nameReg2.test(twittername))
	{
		
	
		iserror =1;
	
		error_msg+="&quot;<?php echo __("Type your Twitter username without the",TWEEGI_TRASNLATE)?> @ &quot;<br>";

	}
	else if(!nameReg.test(twittername))
	{
		
		
		iserror =1;
		
		error_msg+="&quot;<?php echo __("Twitter username can't contain @ # -",TWEEGI_TRASNLATE)?> &quot;<br>";

	}
	
	if(myform.urlfile.value.length > 0)
	{
		upload_path = myform.urlfile.value;
		file_location = "url";
	}
	
	if(myform.uploaded.value.length > 0)
	{
		
		upload_path = myform.uploaded.value;
		file_location = "local";
	}
	
	if((myform.urlfile.value.length <= 0)&&(myform.uploaded.value.length <= 0)&&(formmode=="add"))
	{
		iserror =1;
		error_msg+="&quot;<?php echo __("Location of your file",TWEEGI_TRASNLATE)?>&quot; <br>";
	}
	if(tweet.length > 140)
	{
		iserror =1;
		error_msg+="&quot;<?php echo __("Tweet",TWEEGI_TRASNLATE)?>&quot;<br>";
	}
	
	if(iserror ==1)
	{	
		span1 = document.getElementById('span1');
		span1.style.display = 'block';
		span1.innerHTML="<?php echo __("Following fields are required",TWEEGI_TRASNLATE)?> !<br>"+error_msg;
		//
	}else if(iserror ==0)
	
	{
		span1 = document.getElementById('span1');
		span1.style.display = 'none';
		
		blkqut = document.getElementById('blkqut');
		blkqut.style.display = 'block';
	
jQuery(document).ready(function($) {

	var data = {
		action: 'tweegi_createbutton_action',
		type: "POST",
		bname: btnname, 
		tname: twittername,
		t: tweet,
		f: upload_path,
		l: file_location,
		mode: formmode,
		bid: id,
	};

	jQuery.post(ajaxurl, data, function(response) {
		blkqut.innerHTML=response;
		
	});
});
		
	}
}
</script>
<div id="hld">
		<div id="header_container">
				<div class="hdrl"></div>
				<h1><a href="http://www.tweetandgetit.com"><img src="<?echo TWEEGI_URLPATH."/admin/"?>images/logo-twwetandgetit.png" width="137" height="153" alt="Tweet &amp; Get it" /></a></h1>
		  
          </div>
		<!-- end header (940px width) --> 
	
	<!-- end header container (100% width) -->
	
	<div id="body_content">
		<div id="content" class="has_sidebar left">
        <h1><?php echo $b_name ; ?></h1>
		
			<form method="post" id="contact_form" enctype="multipart/form-data" target="upload_target" onload="tweegi_tweet_counter()"  action="<?php echo "http://".$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"]."&path=$destination_path"; ?>"  onsubmit="tweegi_startUpload();" >
			
			<ol>
		  <li>
                    
                    <label for="twittername"><?php echo __("Your Twitter username (Required)",TWEEGI_TRASNLATE);?></label>
					<input type="text" name="twittername" class="text_field medium required" id="twittername" value="<?=$twtname?>"/>
                    		<ul class="unordered-arrow">
								<li><?php echo __("Enter the twitter account you want the user to follow in exchange of downloading your file. At the completion of the Tweet&Get it! process your visitor becomes follower of the Twitter account you defined.",TWEEGI_TRASNLATE)?>.</li>
							</ul>
					</li>
                    
                    
					<li>
                    <label  class="auto_clear"><?php echo __("Your Tweet (Required)",TWEEGI_TRASNLATE)?></label>
                    <li>
					<label id="twtlbl"><?$c = 140 - strlen ($TWEET); 
					if ( $c<0 ) 	
						echo '<font color="red">'.$c.'</font>';
					else
						echo '<font color="green">'.$c.'</font>';
						?>
						</label>
                    <textarea cols="30" rows="5" class="large" name="promotional_tweet" id="promotional_tweet" onchange="tweegi_tweet_counter();" onfocus="tweegi_tweet_counter();" onblur="tweegi_tweet_counter();" onmousemove="tweegi_tweet_counter();" onkeyup="tweegi_tweet_counter();" onload="tweegi_tweet_counter();" onkeypress="tweegi_tweet_counter();" onkeydown="tweegi_tweet_counter();"><?=$TWEET?></textarea>
                    </li>
                      	<ul class="unordered-arrow">
							<li><?php echo __("Write the tweet you want the user to send in exchange of downloading your file. This tweet will be sent automatically each time a visitor completes the Tweet&Get it! process.",TWEEGI_TRASNLATE)?>.</li>
						</ul>
                    </li>
					<?php if($editmode) echo '<ul class="unordered-arrow"><li><b>'.__("Old file",TWEEGI_TRASNLATE).': <font color="red">'.$urlname.' </font></b></li></ul>';?>
                    <li>
					<input type="hidden" name="mode" id="mode" value="<?=$editmodestr?>"/>
					 <input type="hidden" name="uploaded" id="uploaded" value="<?=$file?>"/>
					 <input type="hidden" name="bid" id="bid" value="<?=$id?>"/>
						<label for="File_sharing" class="small"><?php echo __("Your file",TWEEGI_TRASNLATE);?></label>
						<input id="File_sharing"  type="file" name="File_sharing" class="text_field medium required" <?if (isset($file)) echo "disabled=\"disabled\"";?>/>
                        <ul class="unordered-arrow">
					<li>.doc .pdf .xls .ppt .jpg .psd .ai .gif .png .tif .exe .dmg  etc.</li>
						</ul>
					</li>
					<li><input type="submit" class="submit" id="submit" value="<?php echo __("Upload your file",TWEEGI_TRASNLATE);?>" 
					<?//if (isset($file)) echo " disabled=\"disabled\" ";?>/>
					<p> <?=$filemsg?> </p>
					 <label id="upload_result" style="display:none" ></label>
                    <iframe id="upload_target" name="upload_target" src="#" style="width:0;height:0;border:0px solid #fff;"></iframe>
					<p id="f1_upload_process"style="display:none" ><?php echo __("Loading",TWEEGI_TRASNLATE);?>...<br/><img src="<?echo TWEEGI_URLPATH."/img/"?>ajax-loader.gif" /><br/></p>
                 
                    <?php echo __("Or",TWEEGI_TRASNLATE);?>
					</li>
                    
					
                    <li>
                    
                    <label for="urlfile"><?php echo __("The URL of your file (Required if you don't want to upload your file)",TWEEGI_TRASNLATE);?> </label>
					<input type="text"  name="url" class="text_field medium" id="urlfile" value="" <?//if (isset($file)) echo "disabled=\"disabled\"";?> />
                    		<ul class="unordered-arrow">
								<li><?php echo __("Enter the path of your file. ",TWEEGI_TRASNLATE)?>.</li>
							</ul>
					</li>
                    <li>
					<label for="btn_name"><?php echo __("Name of your button (Required)",TWEEGI_TRASNLATE);?> </label>
					<input type="text" <?php if($editmode) echo 'disabled="disabled"';?> name="btn_name" id="btn_name" class="text_field medium required" value="<?=$btnname?>" />
                    		<ul class="unordered-arrow">
								<li><!--(a-zA-Z0-9)--></li>
							</ul>
					</li>
					
				
				<li><input type="button" class="submit" id="submit2" value="<?php echo $b_name;?> !" onclick="tweegi_myalert()" /></li>
				
				</ol>
				
				<div id="blkqut" style="display:none">
				
			  </div>
				<p><span id="span1" class="error_notice" style="display: none;">
                </span></p>
            </form>
		</div>
		<!-- end content -->
		
		<div id="sidebar" class="right">
		  <!-- end widget -->
			
		  <div class="tabbed_widget">
				<ul class="widget_tabs">
					<li><a href="#tab1"><span><?php echo __("About",TWEEGI_TRASNLATE);?> Tweet&amp;Get it!</span></a></li>
					<li><a href="#tab2"><span><?php echo __("Help",TWEEGI_TRASNLATE);?></span></a></li>
				</ul>
				<div class="widget_tabs_content">
					<div class="tab_content"><p><?php echo __("Tweet&Get it! is an automatic process to get Twitter followers in exchange of a downloadable file. Get your shortcodes by setting up your Tweet&Get it! button.
Copy/paste the shortcode into posts, pages and widgets. 
Your Tweet&Get it! button will be immediately available to your visitors.",TWEEGI_TRASNLATE);?></p>
<p><?php echo __("Tweet&Get it! is fully recommended to share content such as: music, ebooks, photos, wallpapers, promotional codes, coupons, typography, CMS themes, videos, software, tutorials, web ressources, icons, PSD brushes...",TWEEGI_TRASNLATE);?>.</p>
<p><?php echo __("Get a new follower with each download !",TWEEGI_TRASNLATE);?>!</p></div>
					<div class="tab_content">
				    <h3><?php echo __("HELP",TWEEGI_TRASNLATE);?></h3>
						<ul>
							<li><a href="http://tweetandgetit.com/contactus" target="_blank" ><?php echo __("Contact us",TWEEGI_TRASNLATE);?></a></li>
							<li><a href="http://getsatisfaction.com/tweetandgetit" target="_blank" ><?php echo __("Support",TWEEGI_TRASNLATE);?></a></li>
							<li><a href="http://tweetandgetit.com/faq" target="_blank" ><?php echo __("FAQ",TWEEGI_TRASNLATE);?></a></li>
						</ul>
					</div>
				</div>
			</div>
			<!-- end widget --> 
			
		</div>
		<!-- end sidebar -->
	</div>
	</div>
	
	<!-- end body content -->
	<!-- end footer container -->

<!-- end wrapper -->