<?php 

	$page['title'] = 'Search';

	$q = $_GET['search'];

?>
    
<div class="content">

<form method="get" action="<?=$site['url']?>/search" class="searchForm" >
<input type="text" x-webkit-speech type="search" value="<?=htmlentities(stripslashes($q))?>" name="search" class="q" />
<div class="buttons">
		<a class="btn  btn-primary btn-large" href="#" onclick="document.forms[0].submit();return false;">Search</a>
</div>
</form>
<br/>
<?php

if($q){		   
    
#         $results = $db->prepare("select sources.title as sourcetitle, items.title, items.linkurl, items.pubdate, items.content from items, sources where items.sourcesid = sources.id and sources.type is null and match(items.title, content) against(:query) order by items.pubdate desc limit 20");
         $results = $db->prepare("select sources.title as sourcetitle, items.title, items.linkurl, items.pubdate, items.content from items, sources where items.sourcesid = sources.id and sources.type is null and (items.title like :query or items.content like :query) order by items.pubdate desc limit 20");
			$results->execute(array(':query' => '%' . $q . '%'));	
			if($results){
                echo '<h3 class="searchSection">Sources</h3>';
				echo '<div class="search">' . "\n";
				foreach($results as $row){
                    $pubdate = getRelativeTime($row['pubdate']);
                    $summary = getSummary($row['content']);


                    echo '<div class="searchResult">' . PHP_EOL;
                    echo '<div class="searchTitle"><a href="' . $row['linkurl'] . '">' . $row['title'] . '</a></div>' . PHP_EOL;
                    echo '<div class="searchDate">Published ' . $pubdate . ' - ' . $row['sourcetitle'] . '</div>' . PHP_EOL; 
                    echo '<div class="searchSummary">' . $summary . '</div>' . PHP_EOL;
                    echo '</div>' . PHP_EOL;
				
				}
				echo '</div>';
				
			} 


            $results = $db->prepare("select * from links where title like :query order by created desc limit 20");
		    $results->execute(array(':query' => '%' . $q . '%'));	
			
			if($results){
				echo '<h3 class="searchSection">Links</h3>';
				echo '<div class="search">' . "\n";
				foreach($results as $row){
				    
				    $ltype = $row['type'];
				    $type = '';
				    if($ltype == 'inactive'){
				        $type = 'Archived';
				    } else if($ltype== 'active'){
				        $type = 'Saved Links';
				    } else if($ltype == 'history'){
				        $type = 'History';
				    }
			        echo '<div class="searchResult">' . PHP_EOL;	
					echo '<div class="searchTitle"><a href="' . $row['linkurl'] . '">' . $row['title'] . '</a></div>' . PHP_EOL;
                    echo '<div class="searchDate">Saved ' . getRelativeTime($row['created']) . ' - ' . $type . '</div>' . PHP_EOL;
                    echo '<div class="searchSummary">' . getSummary($row['description']) . '</div>' . PHP_EOL;
                    echo '</div>' . PHP_EOL;
				}
				echo '</div>' . PHP_EOL;
				
			} 
            

}		

?>
	</div>


