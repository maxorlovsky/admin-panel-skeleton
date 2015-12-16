<!DOCTYPE html>
<html lang="<?=_cfg('language')?>">
<head>
    <meta charset="UTF-8" />
    <meta name="description" content="<?=$this->data->settings['site_description']?>" />
    <meta name="keywords" content="<?=$this->data->settings['site_keywords']?>" />
    
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        
    <title><?=$this->title?><?=$this->data->settings['site_name']?></title>
    
    <script src="<?=_cfg('static')?>/js/scripts.js"></script>
    
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,600&amp;subset=latin' rel='stylesheet' type='text/css'>

    <link rel="stylesheet" type="text/css" href="<?=_cfg('static')?>/css/style.css" />
</head>

<section id="full-site-wrapper" class="default">

<header class="container">
    <h1>Congratulations on newly installed website</h1>
</header>

<nav class="navbar container">
    <div class="navbar-inner">
        <ul class="nav">
        	<?
        	if ($this->data->links) {
				foreach($this->data->links as $v) {
                    if ($v->logged_in == 1 && $this->logged_in || $v->logged_in == 0) {
                    ?>
                    <li class="" id="<?=$v->link?>">
                        <a href="<?=_cfg('href')?>/<?=$v->link?>"><?=t($v->value)?></a>
                    </li>
                    <?
                    }
				}
            }
            ?>
        </ul>
    </div>
</nav>