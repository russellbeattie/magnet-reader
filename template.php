<!DOCTYPE html> 
<html lang="en"> 
<head> 
    <meta charset="utf-8"> 

    <title><?=$site['title']?> <?if($page['title']){ echo ' - ' . $page['title'];}?></title>

	<link href='http://fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
    <link href="libs/bootstrap/css/bootstrap.css" rel="stylesheet">
	<link href="libs/colorpicker.css" rel="stylesheet">
    <link href="css/main.css" rel="stylesheet">
    <link href="css/fix.css" rel="stylesheet">
    <link href="css/adblock.css" rel="stylesheet">

    <script src="libs/jquery.min.js"></script>
    <script src="libs/jquery-ui.min.js"></script>
    <script src="libs/jquery.hashchange.js"></script>
    <script src="libs/jquery.touchSwipe.js"></script>
    <script src="libs/jquery.waypoints.js"></script>
    <script src="libs/jquery.colorpicker.js"></script>
    <script src="libs/date.format.js"></script>
    <script src="libs/underscore-min.js"></script>
    <script src="libs/backbone-min.js"></script>
	<script src="libs/bootstrap/js/bootstrap.js"></script>
	<script src="libs/bootbox.min.js"></script>

    <script src="js/touchfix.js"></script>
    <script src="js/main.js"></script>

    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <meta name="apple-mobile-web-app-capable" content="yes">

    <meta name="msapplication-TileImage" content="/win8_tile.png"/>
    <meta name="msapplication-TileColor" content="#729fcf"/>

</head>

<body>

<div class="navbar navbar-fixed-top">
  <div class="navbar-inner">
    <div class="container-fluid">
	<a class="brand" href="<?=$site['url']?>"><img src="<?=$site['logo']?>" width="28" height="28"/></a>

<? if(checkToken()){ ?>
	 <ul class="nav pull-right">
	  <li class="dropdown">
		<a href="#" class="dropdown-toggle foldersToggle" data-toggle="dropdown">Folders<b class="caret"></b></a>
		<ul class="dropdown-menu folderMenu">

		</ul>
	  </li>
	  <li><a href="links">Links</a></li>
	  <li class="dropdown">
		<a href="#" class="dropdown-toggle" data-toggle="dropdown"><img class="settingsIcon" src="/images/cog.png"/><b class="caret"></b></a>
		<ul class="dropdown-menu">
			<li><a href="search">Search</a></li>
			<li><a href="addlink">Add Link</a></li>
			<li><a href="history">Click History</a></li>
		    <li><a href="manageSources">Sources</a></li>
		    <li><a href="manageFolders">Folders</a></li>                        
			<li><a href="settings">Settings</a></li>
		    <li><a href="logout">Logout</a></li>    
		</ul>
	  </li>
	</ul>	
<? } ?>
    </div>
  </div>
</div>



<div class="page container-fluid">
	<div class="pageTitle"></div>
	<div class="mainPage">
		<?=$page['content']?>
	</div>
	<div class="subPage">
	</div>
</div>
<script>
	$('.pageTitle').html('<?=$page['title']?>');
</script>
</body>
</html>
