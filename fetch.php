#!/usr/bin/php -q
<?php
	ini_set("memory_limit","64M");

if (isset($_SERVER['argc'])) {
    define('CLI', true);
} else {
    define('CLI', false);
}

	require_once('common.php');

  require_once('simplepie.php');

if(CLI == false){

$page['template'] = '';

// header('Content-type: application/octet-stream');

// Turn off output buffering
ini_set('output_buffering', 'off');
// Turn off PHP output compression
ini_set('zlib.output_compression', false);
// Implicitly flush the buffer(s)
ini_set('implicit_flush', true);
ob_implicit_flush(true);
// Clear, and turn off output buffering
while (ob_get_level() > 0) {
    // Get the curent level
    $level = ob_get_level();
    // End the buffering
    ob_end_clean();
    // If the current level has not changed, abort
    if (ob_get_level() == $level) break;
}
// Disable apache output buffering/compression
if (function_exists('apache_setenv')) {
    apache_setenv('no-gzip', '1');
    apache_setenv('dont-vary', '1');
}


	$br = '<br>';

}

	class SimplePie_Custom_Sort extends SimplePie {
	 
		function sort_items($a, $b)
		{
			 return $a->get_date('U') >= $b->get_date('U');
		}
	}

echo 'Fetch Started: ' . date("D M j Y G:i:s") . $br . PHP_EOL;
	
	$getitem = $db->prepare('select * from items where sourcesid = :sourcesid and guid = :guid');	
	$insert = $db->prepare('insert into items values(null, :sourcesid, :foldersid, :hash, :guid, :linkurl, :imageurl, :enclosureurl, :title, :content, :status, 0, :author, :created, :pubdate, :flags, :raw)');

	$updatesource = $db->prepare('update sources set lastupdate = now(), sourceurl = :sourceurl, siteurl = :siteurl, iconurl = :iconurl where id = :id');


	// ---------------------------

    if($argc > 1){

	    $dbsourceset = $db->query('SELECT * from sources where sources.id = ' . $argv[1] . ' and active = 1 and type is null order by position');

	} else {

	    $dbsourceset = $db->query('SELECT sources.* from sources, folders where sources.foldersid = folders.id and active = 1 and type is null order by folders.position, sources.position');

	}

	$dbsources = $dbsourceset->fetchAll();
	$dbsourceset->closeCursor();

	foreach($dbsources as $dbsource){
	
		echo 'Checking: ' . $dbsource['id'] . ' - ' . $dbsource['sourceurl']  . $br . PHP_EOL;
    
   		$source = new SimplePie_Custom_Sort();
   		$source->set_useragent($site['title'] . ' bot. ' . $site['url']);
        $source->enable_cache(false);
        $source->enable_order_by_date(true);
		$source->set_feed_url($dbsource['sourceurl']);
		
		$strip_htmltags = $source->strip_htmltags;
        unset($strip_htmltags[array_search('object', $strip_htmltags)]);
        unset($strip_htmltags[array_search('param', $strip_htmltags)]);
        unset($strip_htmltags[array_search('embed', $strip_htmltags)]);
        $strip_htmltags = array_values($strip_htmltags);
         
        $source->strip_htmltags($strip_htmltags);
		
		$source->init();
		$source->handle_content_type();
		
		if ($source->error()){
			$errormsg =  $source->error();
			echo "	Error in Source ID: " . $dbsource['id'] . " - " . $errormsg . $br . PHP_EOL;
		
			continue;
		}

        $items = $source->get_items();
	
		
	    foreach($items as $item){
	    
	    	// echo json_encode($item->data, true) . PHP_EOL;
	    	
	    	$guid = $item->get_id();

			$params = array(
				':sourcesid' => $dbsource['id'], 
				':guid' => $guid
			); 
		
		    $getitem->execute($params);
		
		    $dbitem = $getitem->fetch();
		
		    if(!$dbitem){

			    echo '	New Item found: ' . $item->get_id()  . $br . PHP_EOL;
			
			    if ($author = $item->get_author()){
				    $authorname = $author->get_email();
				    if($authorname == ''){
				        $authorname = $author->get_name();
				    }
			    } else {
				    $authorname = null;
			    }

			    if ($enclosure = $item->get_enclosure()){
				    $enclosureurl = $enclosure->get_link();
			    } else {
				    $enclosureurl = null;
			    }

				$images = $item->get_links('image');
				
				if($images){
					$imageurl = $images[0];
				} else {
					$imageurl = '';
				}			
                
				$created = date('Y-m-d H:i:s');

                if ($item->get_date('Y-m-d H:i:s')){
                    $pubdate = $item->get_date('Y-m-d H:i:s');
                } else {
                    $pubdate = $created;
                }

				if ($pubdate > $created){
					$pubdate = $created;
				}


			    $title = mb_convert_encoding($item->get_title(), 'HTML-ENTITIES', 'UTF-8');

				$title = preg_replace('/\s+/', ' ', $title);

			    $content = mb_convert_encoding($item->get_content(), 'HTML-ENTITIES', 'UTF-8');
			    //$raw = json_encode($item->data, true);
			    $raw = null;
			    $status = 0;
			    $flags = '';

			    $params = array(
			    	':sourcesid' =>  $dbsource['id'], 
			    	':foldersid' => $dbsource['foldersid'],
					':hash' => sha1($item->get_content()), 
					':guid' => $item->get_id(), 
					':linkurl' => $item->get_permalink(), 
				    ':imageurl' => $imageurl, 
		            ':enclosureurl' => $enclosureurl, 
					':title' => $title, 
					':content' => $content, 
					':status' => $status, 
					':author' => $authorname, 
					':created' => $created, 
					':pubdate' => $pubdate, 
					':flags' => $flags, 
					':raw' => $raw
				);
			
			    $insert->execute($params);
			    $insert->closeCursor();
			
				$params = array(
					':id' => $dbsource['id'], 
					':sourceurl' => $source->feed_url, 
		            ':siteurl' => $source->get_link(), 
		            ':iconurl' => $source->get_favicon()			
				);

                $updatesource->execute($params);
			    $updatesource->closeCursor();
			
		    }
		    
		    $getitem->closeCursor();
		
	    }

		$source->__destruct();

	}
	

	$dups = $db->query('select max(id) from items where pubdate > date_sub(now(), interval 48 hour) and status = 0 group by title having count(*) > 1');

	$ids = $dups->fetchAll(PDO::FETCH_COLUMN, 0);		
	
	if($ids){
		$count = $db->exec('update items set status = 2, flags="dup" where status=1 and id in (' . implode(',', $ids) . ')');
        if($count > 0){		
    		echo $count . ' duplicates marked read' . $br . PHP_EOL;
        }
    }

    $count = $db->exec('update items set status = 2 where pubdate < date_sub(now(), interval 24 hour) and status = 1');

    if($count > 0){
        echo 'Marked ' . $count . ' older entries as read.' . $br . PHP_EOL;
    }


  echo 'Running Filters' . $br . PHP_EOL;

    $count = $db->exec('update items set status = 1 where status = 0');

    if($count > 0){
        echo 'Updated ' . $count . ' items.' . $br . PHP_EOL;
    }

  //todo 
  // expand based on sourcesid
  // include social feeds
  // group social feeds using processSocial
  // auto-mark read based on text string... 


	require_once('processSocial.php');



echo 'Fetch finished. ' . date("D M j Y G:i:s") . $br . PHP_EOL;

?>
