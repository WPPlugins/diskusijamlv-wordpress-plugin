<?php 
	echo '<link rel="stylesheet" href="'.DISKUSIJAM_PLUGIN_URL . 'styles.css" type="text/css" />';
	
	include 'api.php';
	$api_page_key = get_option('diskusijam_api_page_key'); 
	$api_user_key = get_option('diskusijam_api_user_key');
?>
<div class="wrap">
	<?php
		$action = isset($_GET['action'])?$_GET['action']:'comments';
	?>
	<div class="diskusijam_header">
		<img src="http://diskusijam.lv/img/logo.png" alt="Diskusijam.lv" />
		<div class="diskusijam_navigation">			
			<a href="edit-comments.php?page=diskusijam&action=comments"<?php if($action == 'comments'){echo ' class="active"';} ?>><div><span>Comments</span></div></a>
			<a href="edit-comments.php?page=diskusijam&action=settings"<?php if($action == 'settings'){echo ' class="active"';} ?>><div><span>Settings</span></div></a>
			<a href="edit-comments.php?page=diskusijam&action=blockedusers"<?php if($action == 'blockedusers'){echo ' class="active"';} ?>><div><span>Blocked users</span></div></a>
		</div>		
	</div>
	
	<?php		
		if(isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'diskusijam_api')){
			delete_option('diskusijam_api_page_key');
			delete_option('diskusijam_api_user_key');
			delete_option('diskusijam_lang');
			add_option('diskusijam_api_page_key', $_POST['api_page_key']);
			add_option('diskusijam_api_user_key', $_POST['api_user_key']);
			add_option('diskusijam_lang', $_POST['lang']);
			
			if(isset($_POST['comment_sync']) && ($_POST['comment_sync'] == 'on')){
				add_option('diskusijam_comment_sync', 1);
			}else{
				delete_option('diskusijam_comment_sync', 1);
			}
			
			$api_page_key 	= $_POST['api_page_key']; 
			$api_user_key 	= $_POST['api_user_key'];						
			$result 		= diskusijam_apiCall('checkApiKeys');
			$result 		= diskusijam_checkApiKeys($result);
			
			if(!$result){
				?>
				<div id="message" class="error fade"> 
					Nekorekti dati!
				</div>			
				<?php
			}else{
				delete_option('diskusijam_profile_id');
				delete_option('diskusijam_page_id');
				add_option('diskusijam_profile_id', $result['profile_id']);
				add_option('diskusijam_page_id', $result['page_id']);			
				?>
				<div id="message" class="updated fade"> 
					Dati veiksmīgi saglabāti!
				</div>			
				<?php				
			}			
		}	
	
		if($action == 'settings'){
			$lang = get_option('diskusijam_lang');
			if(!$lang){
				$lang = 'lv';
			}
	?>
		<form action="" method="post">
			<h1>Iestatījumi</h1>
			<table class="form-table">
				<tr>
					<th>Lapas API atslēga:</th>
					<td>
						<input class="regular-text" type="text" name="api_page_key" value="<?php echo $api_page_key ?>" />
					</td>
				</tr>
				<tr>
					<th>Lietotāja API atslēga:</th>
					<td>
						<input class="regular-text" type="text" name="api_user_key" value="<?php echo $api_user_key ?>" />
					</td>
				</tr>
				<tr>
					<th>Valoda:</th>
					<td>
						<select name="lang">
							<option value="lv"<?php if($lang == 'lv'){echo ' selected';}?>>Latviešu</option>
							<option value="ru"<?php if($lang == 'ru'){echo ' selected';}?>>Krievu</option>
						</select>
					</td>
				</tr>				
				<tr>
					<th>Komentāru sinhronizēšana:</th>
					<td>
						<input type="checkbox" name="comment_sync" id="comment_sync"<?php if(get_option('diskusijam_comment_sync') == 1){echo ' checked="checked"';}?> /> <label for="comment_sync">Sinhronizēt Wordpress komentārus ar diskusijam.lv komentāriem</label>
					</td>
				</tr>				
			</table>
			<p class="submit">
				<?php wp_nonce_field('diskusijam_api') ?> 
				<input type="submit" value="Saglabāt" name="submit" class="button-primary" />
			</p>	
		</form>	

		<?php
			}else{
				if(!$api_page_key || !$api_user_key){
					echo '<script type="text/javascript">window.location = "edit-comments.php?page=diskusijam&action=settings";</script>';
				}			
				?>
				<iframe src="http://diskusijam.lv/remote/<?php echo $action?$action.'/':'';?>?api_page_key=<?php echo $api_page_key; ?>&api_user_key=<?php echo $api_user_key; ?>" style="width:100%;height:800px;" id="diskusijam_iframe"></iframe>
				<?php
			}
		?>
</div>

