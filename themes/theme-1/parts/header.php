<?php
/**
 * v1.2.1
 * 21/06/2026
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
	<meta name="robots" content="<?php echo ROBOTS_CONTENT;?>">
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<?php echo $LangMetaLinks; ?>
	<link rel="alternate" hreflang="x-default" href="<?php echo BASE_URL;?>/">
	<link rel="canonical" href="<?php echo $uriAbs;?>"/>

	<title><?php echo ($PageContents != null) ? trim($PageContents["meta"]["title"]) : "Page | Lorem ipsum dolor sit amet, consectetur";?></title>
	<meta name="description" content="<?php echo ($PageContents != null) ? $PageContents["meta"]["description"] : 'Mauris malesuada mi et risus scelerisque, vel viverra sapien cursus. Proin et mi euismod, vehicula quam nec, tristique nibh. Nunc euismod maximus lacus, eu tincidunt arcu.';?>">

	<link rel="stylesheet" href="<?php echo ($_SERVER["SERVER_NAME"]!='localhost')?THEME_CSS_PATH . '/min/reset-min.css':THEME_CSS_PATH.'/reset.css';?>">
	<link rel="stylesheet" href="<?php echo ($_SERVER["SERVER_NAME"]!='localhost')?THEME_CSS_PATH . '/min/style-min.css':THEME_CSS_PATH.'/style.css';?>">

</head>

<body id="<?php echo $bodyId;?>" class="<?php echo $bodyClasses;?>">

	<header>
		<div class="header-inner">

			<nav class="nav nav-2">
				<?php echo $LangLinks; ?>
			</nav>

			<button id="nav-toggle" class="nav-toggle" aria-controls="main-nav" aria-expanded="false" aria-label="<?php echo htmlspecialchars($HeaderContents["navToggleAriaLabel"]);?>">
				<span class="hamburger" aria-hidden="true"></span>
			</button>
	
			<nav id="main-nav" class="nav nav-1">
				<?php foreach($MainMenuContents["items"]["item"] as $item):
					$link  = rtrim(BASE_URL, '/') . '/' . $lang . '/';
					$link .= (!is_array($item["slug"]))?(($item["slug"]!="")?$item["slug"]. '/':''):'';
					?>
					<a href="<?php echo $link; ?>"><?php echo strtoupper($item["text"]);?></a>
				<?php endforeach;?>
			</nav>

		</div>
	</header>

	<main>