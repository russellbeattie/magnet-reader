<?php 

	$page['title'] = 'Click History';

?>
<script>
function clearLink(id){

    $.get("clearLink?id=" + id );

}

function restoreLink(id){

    $.get("restoreLink?id=" + id );

}
</script>
	
<div class="content">
	
    <div class="links">
<?

    
    $results = $db->query('SELECT *, date(created) as day FROM links where type = "history" and created > date_sub(now(), interval 14 day) order by date(created) desc, type, time(created) desc');

	$oldname = '';
	$oldday = '';
	$oldtype = '';

	foreach($results as $row){
	
	
	    $day = date('l F j, Y', strtotime($row['created']));
	    $type = $row['type'];
	    
	    if($day !== $oldday){
	        
	        echo '<h3 class="linkDay ' . $row['day'] . '">' . $day . '</h3>' . PHP_EOL;
	        $oldday = $day;   
	        
	    }

        if($row['title']){
            $title = $row['title'];
        } else {
            $title = $row['linkurl'];
        }
       
		if(substr($title,0,4) == 'Item'){
			$itemClass = 'innerLink';
		} else{
			$itemClass = 'itemLink';
		}
 

		echo '<div class="' . $row['id'] . ' linkItem linkType' . $row['type'] . ' ' . $itemClass . '">' . PHP_EOL;

        echo '<span class="linkDate">' . date('g:i a', strtotime($row['created'])) .'</span> '  . PHP_EOL;

        echo '<a href="' . $row['linkurl'] . '" class="link" target="_blank">' . substr( $title, 0, 80) . '</a> '  . PHP_EOL;

        echo '<span class="linkOptions">';

        echo '<a href="#" onclick="clearLink(' . $row['id'] . ');$(\'#' . $row['id'] . '\').fadeOut();return false;" class="clear" title="clear"><i class="icon-ok"></i></a> ';

        echo '<a href="' . $site['url'] . '/editLink?id=' . $row['id'] . '" title="edit"><i class="icon-pencil"></i></a> ';

        echo '<a href="' . $site['url'] . '/deleteLink?id=' . $row['id'] .'" onclick="return confirm(\'Do you want to delete this link?\');" title="delete" class="delete"><i class="icon-remove"></i></a> ';

        echo '</span> '  . PHP_EOL;
        
        echo '</div>' . PHP_EOL;
	
	}

?>
	</div>

</div>


