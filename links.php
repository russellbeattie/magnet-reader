<?php 

	$page['title'] = 'Links';
	
	$p = $_GET['p'];
	if($p){
	    $page['template'] = '';
	}
	
	$all = $_GET['all'];
	
	if($all !== 'true'){
		$whereQuery = 'where type = "active" and created > date_sub(now(), interval 7 day) ';
	} else {
		$whereQuery = 'where type in ("active","inactive") ';
	}

?>
<script>
function clearLink(id){

    $.get("clearlink?id=" + id );

}

function restoreLink(id){

    $.get("restorelink?id=" + id );

}
</script>


<div class="content">
	

    <div class="links">
<?

    if($p){
    
    $results = $db->query('SELECT *, date(created) as day FROM links where type = "active" and created > date_sub(now(), interval 30 day) order by date(created) desc, type, time(created)');
        
    
    } else {
    
    $results = $db->query('SELECT *, date(created) as day FROM links ' . $whereQuery . 'order by date(created) desc, type, time(created) desc limit 1000');

    }

	$oldname = '';
	$oldday = '';
	$oldtype = '';

	foreach($results as $row){
	

	    $day = date('l F j, Y', strtotime($row['created']));
	    $type = $row['type'];
	    
	    if($day !== $oldday){
	        
	        echo '<h3 class="linkDay" class="' . $row['day'] . '">' . $day . '</h3>' . PHP_EOL;
	        $oldday = $day;   
	        
	    }
	    


        if($row['title']){
            $title = $row['title'];
        } else {
            $title = $row['linkurl'];
        }
        
        

		
		echo '<div class="' . $row['id'] . ' linkItem linkType' . $row['type'] . '">' . PHP_EOL;

        echo '<span class="linkDate">' . date('g:i a', strtotime($row['created'])) .'</span> '  . PHP_EOL;

        echo '<a href="' . $row['linkurl'] . '" class="link" target="_blank">' . substr( $title, 0, 85) . '</a> '  . PHP_EOL;


        echo '<span class="linkOptions">';
        
        if($row['type'] == 'active'){

        echo '<a href="#" onclick="clearLink(' . $row['id'] . ');$(\'.' . $row['id'] . '\').fadeOut();return false;" class="clear" title="clear"><i class="icon-ok"></i></a> ';

		} else {

        echo '<a href="#" onclick="restoreLink(' . $row['id'] . ');$(\'.' . $row['id'] . '\');return false;" class="clear" title="clear"><i class="icon-pencil"></i></a> ';
		
		}

        echo '<a href="' . $site['url'] . '/editlink?id=' . $row['id'] . '" title="edit"><i class="icon-pencil"></i></a> ';


        echo '</span> '  . PHP_EOL;
/*
        echo '<div class="linkDetails">' . PHP_EOL;

        echo '<div class="linkDescription">' . formatContent($row['description']) . '</div> ' . PHP_EOL;

        echo '</div>' . PHP_EOL;
*/        
        echo '</div>' . PHP_EOL;
	
	}
	
	echo '</div>' . PHP_EOL;

?>

	<div class="buttons">
	<a class="btn" href="links?all=true">All links</a>
	</div>

	</div>

</div>

