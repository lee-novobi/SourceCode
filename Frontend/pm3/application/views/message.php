<?php 
if ($msg !='' && $type_msg !=''){
?>
	<div class="notification <?php echo $type_msg?> png_bg">
				<a class="close" href="#"><img alt="close" title="Close this notification" src="<?php echo $base_url?>asset/images/icons/cross_grey_small.png"></a>
				<div>
					<?php echo $msg?>
				</div>
	</div>    
<?php 
}
?>


