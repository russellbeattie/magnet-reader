<?php 

	$page['title'] = 'Sources';

?>

<div class="content">

	<div class="pull-right">
    	<a class="btn btn-primary" href="addSource">Add Source</a>
    </div>

<div class="sourceList">
<?

    
    $results = $db->query('SELECT sources.id, iconurl, name, title, siteurl, active, sourceurl FROM sources, folders where sources.foldersid=folders.id order by folders.position, folders.name, title');

	$oldname = '';

	foreach($results as $row){
		
		$name = $row['name'];
		
		if($name != $oldname){
			
			if($oldname){
				echo '</ul>' . "\n";
			}
			echo '<h3>' . $name . '</h3>' . "\n";
			echo '<ul>' . "\n";
			
			$oldname = $name;
			
		}
		
		if(!$row['active']){
			$itemclass='class="inactive"';
		} else {
			$itemclass = '';
		}
		
		if(!$row['title']){
		    $title = $row['sourceurl'];
		} else {
		    $title = $row['title'];
		}
		
		echo '<li ' . $itemclass . '>' . $title . ' <span><a href="' . $site['url'] . '/editSource?id=' . $row['id'] . '"><i class="icon-pencil"></i></a> <a href="' . $row['siteurl'] . '"><i class="icon-share"></i></a></span></li>' . "\n";
	
	}
	
	echo '</ul>' . "\n";

?>
	<h3>OPML</h3>
	<ul>
	<li><a href="<?=$site['url']?>/opml">Public Only List</a></li>
	<li><a href="<?=$site['url']?>/opml?all=true">Public and Private List</a></li>
	</ul>
	</div>

