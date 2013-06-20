<?php 

	$page['template'] = '';

	header('Content-type: text/xml; charset=UTF-8', true);

	echo '<?xml version="1.0" encoding="utf-8"?' . '>' . "\n";

	$dateResults = $db->query('select max(lastupdate) as latestupdate from sources');
	$pubdaterow = $dateResults->fetchAll();
	$pubdate = $pubdaterow[0]['latestupdate'];

	$all = $_GET['all'];
	
	if(!$all){
		$private_query = 'and private=0 ';
	}

?>

<opml version="2.0">
<head>
	<title>Feeds</title>
	<ownerName><?=$site['owner']?></ownerName>
	<ownerId><?=$site['url']?></ownerId>
	<dateModified><?=date(DATE_RSS, strtotime($pubdate)) ?></dateModified>
</head>
<body>
<?

    
    $results = $db->query('SELECT name, title, siteurl, sourceurl FROM sources, folders where sources.foldersid=folders.id ' . $private_query . 'order by folders.position, title');

	$oldname = '';

	foreach($results as $row){
		
		$name = $row['name'];
		
		if($name != $oldname){
			
			if($oldname){
				echo '</outline>' . "\n";
			}
			echo '<outline text ="' . $name . '">' . "\n";
			
			$oldname = $name;
			
		}
		echo '<outline type="rss" text="' . htmlentities($row['title']) . '" xmlUrl="' . htmlentities($row['sourceurl'])  . '" htmlUrl="' . htmlentities($row['siteurl']) . '"/>' . "\n";
	
	}
	
	echo '</outline>' . "\n";

?>
</body>
</opml>

