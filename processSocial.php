<?php
#	ini_set("memory_limit","64M");

#	require_once('common.php');

   echo 'Social Group Update starting. ' . date("D M j Y G:i:s") . PHP_EOL;

	$insertItem = $db->prepare('insert into items values(null, :sourcesid, 39, :hash, :guid, :linkurl, :imageurl, :enclosureurl, :title, :content, :status, 0, :author, now(), :pubdate, :flags, null)');

	$social = $db->query('select * from items where foldersid = 33 and status != 2 order by author, pubdate limit 1000');

	$socialItems = $social->fetchAll();
	$social->closeCursor();
	
	$total = count($socialItems);
	
	$content = '';

	for($i = 0; $i < $total; $i++){

		$si = $socialItems[$i];
	
		//echo $i . ' - ' . ($total - 1) . $si['author'] .  PHP_EOL;

		$content = $content . '<p class="social-status"><a class="social-timestamp" href="' . $si['linkurl'] . '">' . date('h:i a', strtotime($si['pubdate'])) . '</a> ' . $si['content'] . '</p>' . PHP_EOL;	

		if($i == $total - 1 || $socialItems[$i]['author'] !== $socialItems[$i+1]['author']){
		
			$content = '<p><img src="' . $si['imageurl'] .'" class="social-avatar"/></p>' . PHP_EOL . $content;
			
			$author = $si['author'];
			$linkurl = $si['linkurl'];
			$sourcesid = $si['sourcesid'];
			$guid = $si['guid'];
			$pubdate = $si['pubdate'];
			$imageurl = $si['imageurl'];
			$authoruri = $si['enclosureurl'];

			$enclosureurl = '';
			$status = 1;
			$flags = '{expanded: true}';
			
			$params = array(
				':sourcesid' => $sourcesid , 
				':hash' => sha1($content), 
				':guid' => $guid, 
				':linkurl' => $authoruri, 
				':imageurl' => $imageurl, 
				':enclosureurl' => $enclosureurl, 
				':title' => $author, 
				':content' => $content, 
				':status' => $status, 
				':author' => $author, 
				':pubdate' => $pubdate, 
				':flags' => $flags
			);

			echo 'Adding: ' . $guid . PHP_EOL;

			$insertItem->execute($params);
			$insertItem->closeCursor();	

			$content = '';
			
		}
	
	
	}



   $count = $db->exec('update items set status = 2 where foldersid = 33 and status !=2 ');
   
   
   echo 'Social Group Update finished. ' . date("D M j Y G:i:s") . PHP_EOL;
   

