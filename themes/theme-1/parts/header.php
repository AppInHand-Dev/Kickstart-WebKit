<?php
/**
 * v1.7.0a
 * 26/05/2026
 * 
 */

/*
	IMPORTED
	(index.php)

*/

?>
<?php // ↓ NO EMPTY LINES FOR A CORRECT HTML OUTPUT ?>
<!doctype html>
<html lang="<?php echo htmlspecialchars($lang); ?>">
<head>

	<base href="<?php echo BASE_URL; ?>/">
	<meta name="robots" content="noindex, nofollow">
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<?php echo $LangMetaLinks; ?>
	<link rel="alternate" hreflang="x-default" href="<?php echo BASE_URL;?>/">
	<link rel="canonical" href="<?php echo $uriAbs;?>"/>

	<title><?php echo ($PageContents != null) ? trim($PageContents->meta->title) : "Page | Lorem ipsum dolor sit amet, consectetur";?></title>
	<meta name="description" content="<?php echo ($PageContents != null) ? $PageContents->meta->description : 'Mauris malesuada mi et risus scelerisque, vel viverra sapien cursus. Proin et mi euismod, vehicula quam nec, tristique nibh. Nunc euismod maximus lacus, eu tincidunt arcu.';?>">

	<meta property="og:locale" content="it_IT">
	<meta property="og:type" content="website">
	<meta property="og:title" content="Titolo - Lorem ipsum dolor sit amet, consectetur">
	<meta property="og:description" content="Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce dolor nisl, mattis in nibh ac, sollicitudin commodo dui. Sed euismod, metus consequat mole">
	<meta property="og:url" content="<?php echo BASE_URL;?>/">
	<meta property="og:site_name" content="NEW WEBSITE">
	<meta property="og:image" content="<?php echo APP_IMAGES_URL;?>/hp-2.jpg">
	<meta property="og:image:width" content="1455">
	<meta property="og:image:height" content="840">
	<meta property="og:image:type" content="image/jpeg">

	<link rel="stylesheet" href="<?php echo ($_SERVER["SERVER_NAME"]!='localhost')?THEME_CSS_PATH . '/min/reset-min.css':THEME_CSS_PATH.'/reset.css';?>">
	<link rel="stylesheet" href="<?php echo ($_SERVER["SERVER_NAME"]!='localhost')?THEME_CSS_PATH . '/min/style-min.css':THEME_CSS_PATH.'/style.css';?>">

</head>

<body id="<?php echo $_ARG1;?>" class="<?php echo $bodyClasses;?>">

	<header>

		<nav>
			<?php echo $LangLinks; ?>
		</nav>
	
		<nav>
			<?php foreach($MainMenuContents->items->item as $item):
				$link  = rtrim(BASE_URL, '/') . '/' . $lang . '/';
				$link .= ($item->slug!="")?$item->slug. '/':'';
				?>
				<a href="<?php echo $link; ?>"><?php echo strtoupper($item->text);?></a>
			<?php endforeach;?>
		</nav>

	</header>

	<main>