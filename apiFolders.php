<?php

	$page['template'] = '';
	
	header('Cache-Control: no-cache, must-revalidate');
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Content-type: application/json');    

    $timequery = ' items.created > date_sub(now(), interval 24 hour) and ';                                                
    
    //$timequery = '';

    $sql = "select folders.id as id, folders.name, folders.color, count(items.id) as unread, folders.position from items, sources, folders where status=1 and items.sourcesid = sources.id and " . $timequery . "sources.foldersid = folders.id group by folders.name order by folders.position";

    
    debug($sql);

    $results = $db->query($sql);
   
    $total = 0;
    $folders = array();
    
    foreach($results as $row){                                                                                              
    
        $total = $total + $row['unread'];
        
        $folder = array(
        	'id' => $row['id'],
        	'name' => $row['name'],
        	'color' => $row['color'],
        	'unread' => intval($row['unread']),
        	'position' => intval($row['position'])
        );
        
        $folders[] = $folder;
        
    }
    
		$folder = array(
        	'id' => null,
        	'name' => 'All',
        	'color' => null,
        	'unread' => $total,
        	'position' => 0
        );
        
        array_unshift($folders, $folder);
        
        echo json_encode($folders);

