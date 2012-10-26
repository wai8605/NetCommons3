<?php
$nc_mode = $this->Session->read(NC_SYSTEM_KEY.'.mode');
$content = $this->fetch('content');
$setting_class_name = '';
if($block['Block']['controller_action'] == 'group' && $nc_mode == NC_BLOCK_MODE && $hierarchy >= NC_AUTH_MIN_CHIEF) {
	// 内部ブロックと重なるため上下paddingをとる
	$setting_class_name = ' nc_content_group_setting nc_columns';
}
?>
<?php if($content != '' || ($hierarchy >= NC_AUTH_MIN_CHIEF && $nc_mode == NC_BLOCK_MODE)): ?>
	<div id="<?php echo($id); ?>_content" class="<?php if(isset($parent_class_name)): ?><?php echo($parent_class_name.'_content '); ?><?php endif; ?><?php echo($block['Block']['theme_name']); ?>_content nc_content<?php echo($setting_class_name); ?>">
		<?php echo($content);?>
	</div>
<?php endif; ?>