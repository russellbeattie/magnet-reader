<?php

   
    $page['template'] = '';
    
	header('Cache-Control: no-cache, must-revalidate');
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Content-type: application/json');    
    
    $sourcesid = $_REQUEST['source'];
    
    $folder = $_REQUEST['folder'];
    
    $startTime = $_REQUEST['starttime'];
    
    if($startTime){
    	$startQuery = ' and items.created < "' . $startTime . '"';
    } else {
    	$startQuery = '';
    }

    if(strtolower($folder) == "starred"){
        $folder = null;
        $itemtypes = 'items.starred = 1';
    } else {
        $itemtypes = 'items.status = 1';
    }
      
 
    $timequery = ' items.created > date_sub(now(), interval 24 hour) and ';
    
    if($sourcesid){
        $sourcequery = 'sources.id = ' . $sourcesid . ' and ';
    } else {
        $sourcequery = '';
    }
    
    if($folder){
        $folderquery = 'lcase(folders.name) = "' . strtolower($folder) . '" and ';
    } else {
        $folderquery = '';
    }
    
    if($page['active']){
        $activequery = ' and items.id not in (' . $page['active'] . ') ';
    } else {
        $activequery = '';
    }
    


    $sql = 'SELECT items.id, items.title as itemtitle, linkurl, sources.id as sourcesid, sources.siteurl, sources.title as sourcetitle, folders.name as foldername, folders.color as foldercolor, items.starred, items.author, items.pubdate, items.created, items.content, items.enclosureurl, items.imageurl from items, sources, folders where ' . $sourcequery . $folderquery . $timequery . ' items.sourcesid = sources.id and sources.foldersid = folders.id and ' . $itemtypes . ' ' . $activequery . ' ' . $startQuery . ' ORDER BY  folders.position, items.pubdate desc  limit 20';

  //debug($sql);

	//echo $sql;	
	
    $results = $db->query($sql);
	
	$items = array();
	
	foreach($results as $row){ 
	
		$item = array();
		
	    
		$item['id'] = $row['id'];
		$item['folderName'] = $row['foldername'];		
		$item['folderColor'] = $row['foldercolor'];
		$item['sourcesId'] = $row['sourcesid'];
		$item['sourceTitle'] = $row['sourcetitle'];
		$item['sourceUrl'] = $row['siteurl'];
		$item['url'] = de_utm($row['linkurl']);
		$item['title'] = cleanTitle($row['itemtitle']);
		$item['summary'] = getSummary($row['content']);
		$item['author'] = $row['author'];
		$item['pubDate'] = $row['pubdate'];
		$item['created'] = $row['created'];
		$item['relativeTime'] = getRelativeTime($row['pubdate']);
		$item['relDate'] = getRelativeTime($row['pubdate']);
		$item['starred'] = $row['starred']?true:false;	    
		$item['imageUrl'] = $row['imageurl'];
    $item['pubDay'] = date('F j, Y', strtotime($row['pubdate']));
	  $item['fullText'] = '';
    $item['expandClass'] = '';
    $item['expand'] = false;

	if($row['sourcesid'] == 648){
		$item['expandClass'] = 'open';
		$item['expand'] = true;
		$item['fullText'] = get_links($row['content']);
		
	}

		$items[] = $item;
	
	}
	
echo json_encode($items);
