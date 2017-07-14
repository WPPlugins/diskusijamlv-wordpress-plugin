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