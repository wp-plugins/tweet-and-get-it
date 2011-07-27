<?
function tweegi_admin_styles() {
  
       wp_enqueue_style( 'tweegiStylesheet' );
       wp_enqueue_style( 'tweegiStylesheet1' );
      wp_enqueue_style( 'tweegiStylesheet2' );   
   }
function tweegi_make_upload_dir ()
{
	if(!is_dir(TWEEGI_UPLOAD_PATH))
	{
		
		 if (!is_dir(WP_UPLOAD_PATH))
		 {
			
			$dir1 = mkdir(WP_UPLOAD_PATH,0777,true);
			@chmod (WP_UPLOAD_PATH,0777);
			
		 }
		 else
		 {
			@chmod (WP_UPLOAD_PATH,0777);
		 }
		 $dir = mkdir(TWEEGI_UPLOAD_PATH,0777,true);
		
		if(!$dir)
		{
			
		}
		else
		{
			@chmod (TWEEGI_UPLOAD_PATH,0777);
			
		}
	}
	else
	{
		
		@chmod (TWEEGI_UPLOAD_PATH,0777);
		
		
	}
}   
function tweegi_admin()
{
	

	$parent = 'tweet-and-get-it/tweetandgetit_admin.php';
	$parent_list = 'tweet-and-get-it/tweetandgetit_admin_list.php';
	add_object_page('TweetAndGetIt', esc_attr(__('Tweet&Get it!', TWEEGI_TRASNLATE)), 0, $parent, '', TWEEGI_URLPATH.'/img/favicon.png');	
	add_submenu_page($parent, "Tweet & Get it Make your button", __("Make your button",TWEEGI_TRASNLATE), 0, $parent);
	add_submenu_page($parent, "Manage Tweet And Get It buttons", __("Manage your buttons",TWEEGI_TRASNLATE), 0, $parent_list);
	
	add_action( 'admin_print_styles-' . $parent, 'tweegi_admin_styles' );
	add_action( 'admin_print_styles-' . $parent_list, 'tweegi_admin_styles' );
	
}

function tweegi_admin_init() {
       
       wp_register_style( 'tweegiStylesheet', WP_PLUGIN_URL . '/tweet-and-get-it/css/skins/red.css' );
       wp_register_style( 'tweegiStylesheet1', WP_PLUGIN_URL . '/tweet-and-get-it/css/style.css' );
       wp_register_style( 'tweegiStylesheet2', WP_PLUGIN_URL . '/tweet-and-get-it/admin/css/style.css' );
	   
	   
   }
   

function tweegi_plugin_init () {

	add_action('admin_menu', 'tweegi_admin');
	add_action( 'admin_init', 'tweegi_admin_init' );
	
	wp_deregister_script( 'tweegi_script' );
	wp_register_script( 'tweegi_script', TWEEGI_URLPATH."/js/tweegi_redirect.js");
    wp_enqueue_script( 'tweegi_script' );
	
	
	
	
}
function tweegi_callback($ma)
{
	return "javascript:tweegiopenNewWindow('".$ma[1]."'";
	
}
function tweegi_content_hook($content)
{
	if ((strpos($content,"javascript:tweegiopenNewWindow")))
	{
		$hostPattern = "/javascript:tweegiopenNewWindow\(['\"]([^'\"]+)['\"]/";
		$cc = preg_replace_callback($hostPattern,'tweegi_callback',$content);
		return $cc;
	}
	return $content;
	
}

function tweegi_shortcode_handler( $atts ) {


	global $tbl_tweetandgetit_buttons,$wpdb;
	extract( shortcode_atts( array(
		'name' => 'something',
	), $atts ) );
	
		$sql = "select * from $tbl_tweetandgetit_buttons where button_name=\"".trim($name)."\"";
		$row = $wpdb->get_results($wpdb->prepare($sql));
		
		if($wpdb->num_rows == 0)
		{
			return '';
			
		}
		if(strpos($row[0]->file_path,"TWEEGE_FILE:") == 1)
		{
			
			$row[0]->file_path = str_replace(":TWEEGE_FILE:",'',$row[0]->file_path);
			
		}
		
		
		$lang = get_bloginfo( "language", "raw" );
		$data=array("tweet"=>$row[0]->tweet, "file"=>$row[0]->file_path, "blogger"=>$row[0]->twitter_name, "domain"=>$_SERVER['HTTP_HOST'], "btnname"=>$row[0]->button_name,"language"=>$lang);
		$encoded=urlencode(base64_encode(utf8_encode(serialize($data))));
		$tweet = urlencode(base64_encode($row[0]->tweet));
		$file = urlencode(base64_encode($row[0]->file_path));
		$blogger= urlencode(base64_encode($row[0]->twitter_name));
		$domain= urlencode(base64_encode($_SERVER['HTTP_HOST']));
		$btnname= urlencode(base64_encode($row[0]->button_name));
		$language= urlencode(base64_encode($lang));
		
		
		$url = "http://tweetandgetit.com/process.php?tweet=$tweet&file=$file&blogger=$blogger&domain=$domain&btnname=$btnname&language=$lang";
		$url = "http://tweetandgetit.com/process.php?data=$encoded";
		$content.= '<p><a class="tweegibutton" href="javascript:tweegiopenNewWindow(\''.$url.'\');"></a> <br />		
		   <span class="trademark"><a href="http://tweetandgetit.com" target="_blank">Tweet&getit</a> is powered by <a href="http://viuu.co.uk" target="_blank">Viuu</a></span> </p>';
		return $content;
}


function tweegi_btnslist($page,$link)
{
	global $wpdb,$tbl_tweetandgetit_buttons;
	$count_per_page = 10;
		
	if( !isset($page) || empty($page) || $page <= 0)
		$page = 1;
	$lim1 = ($page-1)*$count_per_page;		
	$lim2 = $count_per_page;		

$result = $wpdb->get_row(" SELECT count( * ) as count_all FROM ".$tbl_tweetandgetit_buttons);
$count_all = $result->count_all;

$last_page = ceil($count_all/$count_per_page);
$rows = $wpdb->get_results(" SELECT * FROM ".$tbl_tweetandgetit_buttons." Limit $lim1,$lim2;");
$previous_page = $page - 1;
if($previous_page < 1) 
	$previous_page = 1;
$next_page = $page + 1;
if($next_page > $last_page) 
	$next_page = $last_page;	

// loop
$i = $page - 3;
if( $i < 1)
	$i = 1;
$loop_count = 1;	

if($i >1)
{
$previous_page = $i-1;

}
else
{
$previous_page = 1;


}
	echo '	<form name="tweegibtnlist"	action="" method="post">
					
						<table cellpadding="0" cellspacing="0" width="100%" class="sortable">
						
							<thead>
								<tr>
									<th width="10"><input name="tweegichkbxall" type="checkbox" class="check_all" onClick="tweegi_CheckAll(document.tweegibtnlist.tweegichkbx,document.tweegibtnlist.tweegichkbxall)"/></th>
									<th>'.__("Button name",TWEEGI_TRASNLATE).'</th>
									<th>'.__("Shortcode",TWEEGI_TRASNLATE).'</th>
									<th>'.__("File",TWEEGI_TRASNLATE).'</th>
									<th>'.__("Edit",TWEEGI_TRASNLATE).' </th>
									<th>'.__("Delete",TWEEGI_TRASNLATE).' </th>
								</tr>
							</thead>
							
							<tbody>
							';
							
							$count = $wpdb->num_rows;
							$op=explode("?",$_SERVER["HTTP_REFERER"]);						
							
							
							foreach ($rows as $row)
							{
								
								if(strpos($row->file_path,":TWEEGE_FILE:") == 0)
								{
									$row->file_path = basename(str_replace(":TWEEGE_FILE:",'',$row->file_path));
								}
								
								echo '
								<tr>
									<td><input name="tweegichkbx" type="checkbox" value="'.$row->id.'" id="'.$row->id.'"/></td>
									<td>'.$row->button_name.'</td>
									<td>'.$row->shortcode.'</td>
									<td>'.$row->file_path.'</td>
									<td class="edit"><a href="'.$op[0].'?page=tweet-and-get-it/tweetandgetit_admin.php&bid='.$row->id.'">'.__("Edit",TWEEGI_TRASNLATE).'</a></td>
									<td class="delete"><a href="javascript:tweegi_delete_button('.$row->id.','.$page.',\''.$link.'\')">'.__("Delete",TWEEGI_TRASNLATE).'</a></td>
								</tr>';
									
							}
							$loop_count = 1;
							$i = $page -2;
							if($i < 1)
								$i = 1;
								echo '
								
							</tbody>
							
						</table>
						
						
						
						<div class="tableactions">
							<select id="selectactions">
								<option>'.__("Actions",TWEEGI_TRASNLATE).'</option>
								<option>'.__("Delete",TWEEGI_TRASNLATE).'</option>
 						    </select>
							
							<input class="submit tiny" value="'.__("Apply to selected",TWEEGI_TRASNLATE).'" onclick="tweegi_delete_buttons(document.tweegibtnlist.tweegichkbx,'.$page.',\''.$link.'\')"/>
					  </div>		<!-- .tableactions ends -->
						
						
						
						<div class="pagination right">';
							
							echo "<a href=\"javascript:tweegi_buttons_list(1,'$link');\" title=\"".__("Go to first page",TWEEGI_TRASNLATE)."[1]\"><<</a>";
							echo "<a href=\"javascript:tweegi_buttons_list($previous_page,'$link');\" title=\"".__("Go to previous page",TWEEGI_TRASNLATE)."[$previous_page]\"><</a>";
							while( $loop_count <= 5 && $i <= $last_page)
							{
								if($page != $i)
								{
									echo "<a href=\"javascript:tweegi_buttons_list($i,'$link');\" title=\"".__("Go to page",TWEEGI_TRASNLATE)."[$i]\">$i</a>";
								
								}
								else
									echo "<a title=\"".__("current page",TWEEGI_TRASNLATE)."[$i]\">$i</a>";
		
		
								$i++;
								$loop_count++;
							}
							echo "<a href=\"javascript:tweegi_buttons_list($next_page,'$link');\" title=\"".__("Go to next page",TWEEGI_TRASNLATE)."[$next_page]\">></a>";
							echo "<a href=\"javascript:tweegi_buttons_list($last_page,'$link');\" title=\"".__("Go to last page",TWEEGI_TRASNLATE)."[$last_page]\">>></a>";
							echo '
							
						</div>		<!-- .pagination ends -->
						
				  </form>
				
				';
}
function tweegi_delete_buttons()
{	
	
	if ( isset($_POST['bids']))
	{
	$url = $_POST['url'];
	$page = $_POST['page'];
	$bids = trim($_POST['bids'],",");
	global $wpdb,$tbl_tweetandgetit_buttons;
	$sql = "delete from ".$tbl_tweetandgetit_buttons." where id in (".$bids.")";
	if($wpdb->query($sql))
	{
		// show button list again
		tweegi_btnslist($page,$url);
		
	}
	}
	die();
}

function tweegi_buttons_list_callback($p='',$l='') {
	
	if(isset($_POST['page']))	
		$page = $_POST['page'];
	else
		$page = $p;
	if(isset($_POST['link']))	
		$link = $_POST['link'];
	else
		$link = $l;	

	
	tweegi_btnslist($page,$link);
	die();
}
function tweegi_action_callback() {
	global $wpdb,$tbl_tweetandgetit_buttons; // this is how you get access to the database
	
	$tweet = $_POST['t'];
	$btn = trim($_POST['bname']);
	$tname= $_POST['tname'];
	$file=$_POST['f'];
	$location=$_POST['l'];
	$mode=$_POST['mode'];
	$bid=$_POST['bid'];
	
	if ($location == "local")
	{
		$file = ":TWEEGE_FILE:".TWEEGI_UPLOAD_URL."/".basename($file);
	
	}
	
	$shortcode = '[tweegi-button name="'.$btn.'"]';
	if($mode=="add")
	{
	$sql_insert = "insert into ".$tbl_tweetandgetit_buttons." values('','$tname','$btn','$tweet','$file','$shortcode')";
	$out=$wpdb->query($sql_insert);
	}	
	else if($mode=="edit")
	{	
		if(strlen($_POST['f'])>0)
		$update_string=",file_path='$file'";
		
		$sql_update = "update ".$tbl_tweetandgetit_buttons." set twitter_name='$tname',tweet='$tweet' ".$update_string." where id=$bid;";
		$out=$wpdb->query($sql_update);
	}	
	if(is_bool($out) && $out == false)
	{
		echo "<blockquote>".__("Failed to $mode button, change button name and try again",TWEEGI_TRASNLATE)."!<br></blockquote>";
	}
	else if (!$out)
	{
		echo "<blockquote>".__("Failed to $mode button, change some button attributes and try again",TWEEGI_TRASNLATE)."!<br></blockquote>";
	}
	else
	{
		echo "<blockquote>
              <p>".__("Your button is ready",TWEEGI_TRASNLATE)." !<br>
				    ".__("copy / paste this code in your posts, pages and widgets",TWEEGI_TRASNLATE)." !				    <br>
				  </p>
				  <pre>
".$shortcode.'
</pre></blockquote>';
	}
	
	
	die(); 
}

?>