<?php
/**
 * v1.1.0a
 * 11/06/2025
 * 
 */

$filePathBreadcrumbsRouting = APP_DATA_PATH . "/{$lang}/breadcrumbs-routing.xml";
if(file_exists($filePathBreadcrumbsRouting)){
	$BreadcrumbsRouting = simplexml_load_file($filePathBreadcrumbsRouting);
}

?>

<?php if(isset($_ARGS) && is_array($_ARGS) && count($_ARGS)>0):?>
	<div id="breadcrumb" class="col-12 order-1">
		<nav>
			<a href="<?php echo BASE_URL;?>/">
				<span>Home</span>
			</a>
			 » 
			<?php foreach($_ARGS as $i=>$_ARG):?>
				<?php if($i<count($_ARGS)-1):?>
					<?php 
					$url = BASE_URL . "/";
					for($j=0;$j<=$i;$j++){
						$url.="{$_ARGS[$j]}/";
					}
					?>
					<a href="<?php echo $url;?>">
				<?php endif;?>
						<?php $text = (isset($BreadcrumbsRouting)&&property_exists($BreadcrumbsRouting, $_ARG))?$BreadcrumbsRouting->{$_ARG}:ucfirst($_ARG);?>
						<span><?php echo $text;?></span>
				<?php if($i<count($_ARGS)-1):?>
					</a> » 
				<?php endif;?>
			<?php endforeach;?>
		</nav>
	</div>
<?php endif;?>