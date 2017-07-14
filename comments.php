<?php
	global $post;
	if($post->comment_status == 'closed'){
		return;
	}	
	if( !function_exists('diskusijam_getCommentCount') ){
		require 'api.php';
	}	
	
	$diskusijam_profile_id = get_option('diskusijam_profile_id'); 
	$diskusijam_page_id = get_option('diskusijam_page_id');
	$diskusijam_comment_sync = get_option('diskusijam_comment_sync');
	$lang = get_option('diskusijam_lang');
	if(!$lang){
		$lang = 'lv';
	}	
	/*
	*	Syncronizing diskusijam.lv comments with WP database
	*/
	if($diskusijam_comment_sync == 1){
		diskusijam_syncComments($post->ID);
	}
	
	$comment_counts = diskusijam_getCommentCount(array($post->ID));
?>
<div id="uc_comment_content"></div>
<script type="text/javascript">
      var thread_id = '<?php echo $post->ID;?>'; // Jūsu raksta unikālais identifikators (skaitlis)
      var thread_title = '<?php echo htmlspecialchars(the_Title());?>'; // Jūsu raksta unikālais nosaukums (simbolu virkne). Vērtība nav obligāti jānorāda
      var comment_language = '<?php echo $lang; ?>'; // Valoda. Noklusētais uzstādījums - latviešu valoda. Pieļaujamās vērtības - "lv" un "ru".
      var uc_callback = ''; // Callback funkcijas nosaukums. Šī funkcija tiks izsaukta, kad komentāri būs pilnībā ielādēti. Neobligāts parametrs.
      
      /** NEMAINIET NEKO ZEM ŠĪS RINDIŅAS! **/
      (function() {
            var uc = document.createElement('script');
            uc.type = 'text/javascript';
            uc.async = true;
            uc.src = 'http://diskusijam.lv/comments/<?php echo $diskusijam_profile_id; ?>/<?php echo $diskusijam_page_id; ?>/' + thread_id + '/' + thread_title + '/' + comment_language + '/' + uc_callback + '/';
            (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(uc);
		})();				
</script>
<?
if(!empty($comment_counts)){
?>
<script type="text/javascript">
function diskusijam_ccounts(){ 
	<?php
		foreach($comment_counts as $thread => $data){
			if((int)$data['thread_id'] > 0){
				?>
					var diskusijam_post_data = document.getElementById('post-<?php echo $data['thread_id']; ?>');
					if(diskusijam_post_data){
						var diskusijam_post_title = diskusijam_post_data.getElementsByTagName('a')[0];
						diskusijam_post_title.innerHTML = diskusijam_post_title.innerHTML + ' (<?php echo $data['count']; ?>)';
					}
					
					var diskusijam_comment_url = document.getElementById('diskusijam_comment_count_<?php echo $data['thread_id']; ?>');
					if(diskusijam_comment_url){
						diskusijam_comment_url.innerHTML = '<?php echo $data['count']; ?> komentāri';
					}
				<?php
			}
		}
	?>
}
if(document.loaded) {
    diskusijam_ccounts();
} else {
    if (window.addEventListener) {  
        window.addEventListener('load', diskusijam_ccounts, false);
    } else {
        window.attachEvent('onload', diskusijam_ccounts);
    }
}
</script>
<?
}
?>