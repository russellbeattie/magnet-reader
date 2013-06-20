<?php


	require_once('common.php');

// -----------------------------------------------
// Controller section
	
	$page['template'] = 'template.php';
	$page['title'] = '';
	
	$uri = substr($_SERVER['REQUEST_URI'], strlen($site['base'])+1);

	$page['name'] = str_replace('/','_',substr($uri, 0, (strpos($uri, '?') !== false ? strpos($uri, '?') : strlen($uri))));

	if($page['name'] == ''){
		//$page['name'] = 'items';
	    header('Location: ' . $site['url'] . '/stream');
	    exit();		
	}


    if(checkToken() == false){

        if($page['name'] !== 'login' && $page['name'] !== 'source' && $page['name'] !== 'saveLinkJS'){
		    header('Location: ' . $site['url'] . '/login');
            exit();
        }
	
	}


	if(function_exists($page['name'] . 'Action')){
		$fn = $page['name']  . 'Action';
		$fn();
	}

	ob_start();
	
	if(file_exists(APP_PATH . '/' . $page['name'] . '.php')) {
	
		$page['filename'] = $page['name'] . '.php';
		
		include $page['filename'];
	
	} else {
	
	    do404();
	
	}


	
	$page['content'] = ob_get_clean();

    header('Cache-Control: no-cache, no-store, max-age=0, must-revalidate');
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header('Pragma: no-cache');

	if($page['template']){
	

        header('Content-Type: text/html; charset=UTF-8');
	
		include $page['template'];
		
	} else {
		
		echo $page['content'];

	}



function trackAction(){

    global $site, $db;

    $id = $_GET['id'];
    $xurl = $_GET['url'];

    if($id){
        $url = de_utm(saveItemAsLink($id,'history'));
        //$update = $db->prepare('update items set status = 2 where id in(' . $id . ')');
        //$update->execute();
    } else if($xurl){
       saveUrlToHistory($xurl);
    }
	
	header("Content-Type: image/png");
	header("Content-Length: 0");	
	
	exit();
}



function openAction(){

    global $site, $db;

    $id = $_GET['id'];
    $xurl = $_GET['url'];

    if($id){
        $url = de_utm(saveItemAsLink($id,'history'));
        $update = $db->prepare('update items set status = 2 where id in(' . $id . ')');
        $update->execute();
        header('Location: ' . $url);
    } else if($xurl){
       saveUrlToHistory($xurl);  
        header('Location: ' . $xurl);
    } else {
        header('Location: ' . $site['url']);
    }
	
	exit();
}


function starItemAction(){

    global $db, $site;
    $id = $_POST['id'];
    
	if($id){
        $update = $db->prepare('update items set starred = not starred where id = ?');
	    $update->execute(array($id));
    }

    saveItemAsLink($id,'active');

}



function saveItemLinkAction(){
	
    $id = $_POST['id'];
    
    saveItemAsLink($id,'active');
	
	exit();
	
}


function saveItemByTypeAction(){
	
    $id = $_POST['id'];
    $type = $_POST['type'];
    
    saveItemAsLink($id, $type);
	
	exit();
	
}


function getItemAction(){

    global $db, $site;

	$id = $_GET['id'];
	
	$sql = "SELECT content, sourcesid from items where id = ?";
	
	$results = $db->prepare($sql);
	$results->execute(array($id));

	$row = $results->fetch();

	$content = $row['content'];
	
	if($row['sourcesid'] == 648){
		$content = get_links($content);
	} 

	echo formatContent($content);
	
	exit();

}


function apiItemsAction(){

	global $db, $page;

	$items = $_POST['items'];
	$page['active'] = $_POST['active'];
	
	$vals = explode(',', $items);
	$ids = array();
	
	foreach($vals as $val){
		$ids[] = $db->quote($val);
	}
	
	$items = implode(',', $ids);	
    
	if($items){
        $update = $db->prepare('update items set status = 2 where id in (' . $items . ')');
	    $update->execute();
    }

}


function trainItemAction(){
       
    $id = $_GET['id'];
    $action = $_GET['action'];
    
    if($id && $action){
        trainItem($id, $action);
        echo '$id - $action';
        exit();
    } 
}


function getUnreadAction(){

    
    global $db, $site;

	$startTime = $_GET['starttime'];
	
	if(empty($startTime)){
	    $startTime = time();
	}
	
	$sql = "SELECT count(*) as ct from items where status = 1 and created >= from_unixtime('" . $startTime . "')";
	
    $results = $db->query($sql);

	$row = $results->fetch();   

    $unread = array();
    $unread['count'] = $row['ct'];

    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    header('Content-type: application/json');


    echo json_encode($unread);

    exit();
}



function markUnreadAction(){

	global $db;

	$id = $_GET['id'];
	    
    $update = $db->prepare('update items set status = 1 where id in(' . $id . ')');

	$update->execute();
    
    exit();    
    
}


function markReadAction(){

	global $db;

	$id = $_GET['id'];
	
	if($id){
	
		$update = $db->prepare('update items set status = 2 where id in(' . $id . ')');

		$update->execute();
		
		echo $id;
   
   	}
    
    exit();
    
}

function markFolderReadAction(){

	global $db;

	$folder = $_POST['folder'];
	
	if($folder){

        $update = $db->prepare('update items set status = 2 where status = 1 and foldersid = :folder');
        $params = array(':folder' => $folder);
        $update->execute($params);
        
        debug('marking folder ' . $folder . ' read');
    
    
    } else {
        $update = $db->prepare('update items set status = 2');    
    }
    
    echo 'OK';

    exit();
}


function editSourceAction(){
	
	global $db, $site, $page;	
	
	$id = $_GET['id'];
	
	$results = $db->prepare("select * from sources where id = ?");
	$results->execute(array($id));

	$page['source'] = $results->fetch();
	$page['title'] = 'Edit Source';

	$page['name']= 'editSource';

}

function apiEditSourceAction(){

	global $db, $site, $page;	
	
	$id = $_GET['id'];
	
	$results = $db->prepare("select * from sources where id = ?");
	$results->execute(array($id));

	$sources = $results->fetchAll(PDO::FETCH_OBJ);

	$results = $db->query('SELECT id, name from folders order by position');

	$folders = $results->fetchAll(PDO::FETCH_OBJ);
	
	$data = array('sources' => $sources, 'folders' => $folders);
	
	header('Content-type: application/json');
	
	echo json_encode($data);

	exit();

}



function addSourceAction(){
	
	global $site, $page;	
	
	$newsource = array();
	$newsource['id'] = 'new';
	
	$sourceurl = $_GET['url'];
	$newsource['active'] = true;
	
	if($sourceurl){
		
		require_once('simplepie.php');
		
		$source = new SimplePie();
		$source->set_feed_url($sourceurl);
		$source->enable_cache(false);
		$source->init();
		
		$newsource['title'] = $source->get_title();
		$newsource['description'] = $source->get_description();
		$newsource['iconurl'] = '';
		$newsource['siteurl'] = $source->get_permalink();
		$newsource['sourceurl'] = $source->subscribe_url();

		
	}
	
	$page['source'] = $newsource;
	$page['title'] = 'Add Source';

	$page['name']= 'editSource';
	
	
}


function deleteSourceAction(){
	
	global $db, $site;	
	
	//need to confirm here then delete
	
	$id = $_GET['id'];
	
	$delete = $db->prepare('delete from sources where id = ?');
	$delete->execute(array($id));
	
	$delete = $db->prepare('delete from items where sourcesid = ?');
	$delete->execute(array($id));
	
	header('Location: ' . $site['url'] . '/manageSources');
	exit();
	
	
}


function saveSourceAction(){
	
	global $db, $site, $page;	
	
	$id = $_POST['id'];
	$title = $_POST['title'];
	$sourceurl = $_POST['sourceurl'];
	$siteurl = $_POST['siteurl'];
	$iconurl = $_POST['iconurl'];
	$description = $_POST['description'];
	$position = $_POST['position'];
	$private = isset($_POST['private']);
	$active = isset($_POST['active']);
  $type = $_POST['type'];
	$foldersid = $_POST['foldersid'];
	$created = $_POST['created'];
	$lastupdate = $_POST['lastupdate'];
	
	// validation would probably be good here

	if($type == ''){
		$type = null;
	}


	$params = array(
        ':title' => $title,
        ':sourceurl' => $sourceurl,
        ':siteurl' => $siteurl, 
        ':iconurl' => $iconurl,
        ':description' => $description,
        ':position' => $position,
        ':private' => $private,
        ':active' => $active,
        ':foldersid' => $foldersid,
        ':type' => $type
	);
	
	if($id == 'new'){

		$results = $db->prepare("insert into sources values(null, :title, :sourceurl, :siteurl, :iconurl, :description,  now(), null, :position, :private, :active, :foldersid, :type)");
		$results->execute($params);
	
		header('Location: ' . $site['url'] . '/manageSources');
		exit();		
	
	
	} else {
		$params['id'] = $id;
	
		$results = $db->prepare("update sources set title=:title, sourceurl=:sourceurl, siteurl=:siteurl, iconurl=:iconurl, description=:description, position=:position, private=:private, active=:active, foldersid=:foldersid, type=:type where id=:id");
		$results->execute($params);
		
		header('Location: ' . $site['url'] . '/manageSources');
		exit();		
	
	}
	
}


function editFolderAction(){
	
	global $db, $site, $page;	
	
	$id = $_GET['id'];
	
	$results = $db->prepare("select * from folders where id = ?");
	$results->execute(array($id));

	$page['folder'] = $results->fetch();
	$page['title'] = 'Edit Folder';

	$page['name']= 'editFolder';

}


function addFolderAction(){
	
	global $site, $page;	
	
	$newfolder = array();
	$newfolder['id'] = 'new';
	
	$page['folder'] = $newfolder;
	$page['title'] = 'Add Folder';

	$page['name']= 'editFolder';
	
	
}


function deleteFolderAction(){
	
	global $db, $site;	
	
	//need to confirm here then delete
	
	$id = $_GET['id'];
	
	$delete = $db->prepare('delete from folders where id = ?');
	$delete->execute(array($id));
	
	header('Location: ' . $site['url'] . '/manageFolders');
	exit();
	
	
}


function folderPositionAction(){

    global $db, $site, $page;
    
    $items = $_POST['listItem'];
    
    if($items !== null){
        
        $results = $db->prepare("update folders set position=:position where id=:id");
        
        foreach($items as $position => $id){
            $params = array(
				':position' => $position, 
				':id' => $id
			);
		    $results->execute($params);
		    //echo $position . ' ' . $id . PHP_EOL;
        }
    }

    print_r($items);
    //header('Location: ' . $site['url'] . '/managefolders');
    
    exit();

}


function saveFolderAction(){
	
	global $db, $site, $page;	
	
	$id = $_POST['id'];
    $name = $_POST['name'];
    $color = $_POST['color'];
	$position = $_POST['position'];

	$params = array(':name' => $name, ':color' => $color, ':position' => $position);
	
	if($id == 'new'){
	
		$results = $db->prepare("insert into folders values(null, :name, :color, :position)");
		$results->execute($params);
	
		header('Location: ' . $site['url'] . '/manageFolders');
		exit();		
	
	
	} else {

		$params['id'] = $id;
		
		$results = $db->prepare("update folders set name=:name, color=:color, position=:position where id=:id");
		$results->execute($params);
		
		header('Location: ' . $site['url'] . '/manageFolders');
		exit();		
	
	}
	
}


function clearLinkAction(){
	
	global $db, $site;	
	
	$id = $_GET['id'];
	
	$results = $db->prepare('update links set type="inactive" where id = ?');
	$results->execute(array($id));
	
	header('Location: ' . $site['url'] . '/links');
	exit();
	
}


function restoreLinkAction(){
	
	global $db, $site;	
	
	$id = $_GET['id'];
	
	$results = $db->prepare('update links set type="active" where id = ?');
    $results->execute(array($id));
	
	header('Location: ' . $site['url'] . '/links');
	exit();
	
}


function addLinkAction(){
	
	global $site, $page;	
	
	$newlink = array();
	$newlink['id'] = 'new';
	$newlink['type'] = 'active';

	$newlink['linkurl'] = $_GET['url'];
	$newlink['title'] = $_GET['title'];
	$newlink['description']= $_GET['description'];
	
	$page['link'] = $newlink;
	$page['title'] = 'Add Link';

	$page['name']= 'editLink';
	
	
}


function editLinkAction(){
	
	global $db, $site, $page;	
	
	$id = $_GET['id'];
	
	$results = $db->prepare("select * from links where id = ?");
    $results->execute(array($id));

	$page['link'] = $results->fetch();
	$page['title'] = 'Edit link';

	$page['name']= 'editLink';

}


function deleteLinkAction(){
	
	global $db, $site;	
	
	//need to confirm here then delete
	
	$id = $_GET['id'];
	
	$delete = $db->prepare('delete from links where id = ?');
	$delete->execute(array($id));
	
	if($_GET['return'] == 'history'){
		header('Location: ' . $site['url'] . '/history');
	} else {
		header('Location: ' . $site['url'] . '/links');
	}
	

	exit();
	
	
}


function savelinkAction(){
	
	global $db, $site, $page;	
	
	$id = $_POST['id'];
	$title = $_POST['title'];
	$linkurl = $_POST['linkurl'];
	$description = $_POST['description'];
	$type = $_POST['type'];
	$close = $_POST['close'];

	$params = array(
        ':title' => $title,
        ':linkurl' => $linkurl,
        ':description' => $description,
        ':type' => $type
	);
	
	// validation would probably be good here
	if($linkurl == ''){
		header('Location: ' . $site['url'] . '/links');
		exit();		
	}
	
	if($id == 'new'){

        $type = 'active';
	
        $delete = $db->prepare('delete from links where linkurl = ?');
        $delete->execute(array($linkurl));
	
	
		$results = $db->prepare("insert into links values(null, :title, :linkurl, null, :description,  null, now(), :type )");
        try{
		    $results->execute($params);
        }catch(Exception $e){
            
        }           
		
        if($close){
            //header('Location: ' . $site['url'] . '/closeme');
        
		    header('Location: ' . $linkurl);
        
        } else {
            header('Location: ' . $site['url'] . '/links');
        }
		
		//header('Location: ' . $linkurl);
		
		exit();		

	
	} else {

		$params['id'] = $id;
	
		$results = $db->prepare("update links set title=:title, linkurl=:linkurl, description=:description, type=:type where id=:id");
		$results->execute($params);
		
		header('Location: ' . $site['url'] . '/links');
		exit();		
	
	}
	
}


function saveLinkJSAction(){

	global $db, $site, $page;	

    $page['template'] = '';

	//probably should check login here or something...
	
	$title = $_GET['title'];
	$linkurl = $_GET['linkurl'];
	$description = $_GET['description'];
    $tags = "";

    if($linkurl == ''){

        exit();

    }

    if(checkToken() == false){

        $script = 'document.location="' . $site['url'] . '/addLink?url=' . urlencode($linkurl) . '&title=' . urlencode($title) . '&description=' . urlencode($description) . '&close=true";';
        
    } else {

        $type = 'active';

        $delete = $db->prepare('delete from links where linkurl = ?');
        $delete->execute(array($linkurl));


        $results = $db->prepare("insert into links values(null, :title, :linkurl, null, :description, null,  now(), :type )");
        $params = array(
			':title' => $title,
			':linkurl' => $linkurl,
			':description' => $description,
			':type' => $type
		);
        try{
            $results->execute($params);		
        }catch(Exception $e){
            
        }     


        $script = "alert('Link Saved');";

    }    
	
	header("Content-type: application/x-javascript");
    
    echo $script;
	
	exit();


}


function listFoldersAction(){

	global $page;
	
	$page['template']= '';
	$page['name'] = 'folders';
}



function clearTabAction(){
	
	global $db, $site;	
	
	$id = $_GET['id'];
	
	$results = $db->prepare('update links set type="inactive" where id = ?');
	$results->execute(array($id));
	
	header('Location: ' . $site['url'] . '/tabs');
	exit();
	
}



function deleteTabAction(){
	
	global $db, $site;	
	
	//need to confirm here then delete
	
	$id = $_GET['id'];
	
	$delete = $db->prepare('delete from links where id = ?');
	$delete->execute(array($id));
	

	header('Location: ' . $site['url'] . '/tabs');
	

	exit();
	
	
}


function saveTabsAction(){
	
	global $db, $site, $page;	
	
    $jsontxt = $_POST['links'];

    $json = json_decode($jsontxt, true);
    
    
    $delete = $db->prepare('delete from links where type="tab"');
    $delete->execute();        
     
    foreach($json as $title => $links){
    
        $linkurl = $links['linkurl'];
        $faviconurl = $links['faviconurl'];
        
        
	    $results = $db->prepare("insert into links values(null, :title, :linkurl, :faviconurl, '', null,  now(), 'tab' )");
	    $params = array(
			':title' => $title, 
			':linkurl' => $linkurl,
			':faviconurl' => $faviconurl
		);

        try{
	        $results->execute($params);
        }catch(Exception $e){

        }           
    }
	
	
	echo 'Tabs were saved';
	
	exit();		

}

function loginAction(){

	global $site, $page;

	if($_POST){
	
		$username = $_POST['username'];
		$password = $_POST['password'];
	
		$check = doLogin($username,$password);
		
		if($check){
			
			header('Location: ' . $site['url']);
        	exit();
		
		} else {
			$page['username'] = $username;
			$page['password'] = $password;
			$page['error'] = '<p class="error">Try again</p>';
		}
		
		
	}

	if($_GET['action'] == 'logout'){
        logoutAction();
	}


}


function logoutAction(){
	
	global $site;

    doLogout();

    header('Location: ' . $site['url'] . '/login');
    exit();
    
}


function getLongUrlAction(){

    global $site, $page;
    
    $page['template'] = '';
    
    header('Content-type: text/plain');
    
    echo long_url($_GET['url']);
    
    exit();
        
        
}

// ------------------------------------------------------
// Helper functions below
// ------------------------------------------------------


function saveItemAsLink($id, $type){
	
	global $db, $site;	
	  
    if(empty($id)){
        return;
    }

	$sql = "SELECT title, linkurl, content from items where id = ?";
	
	$results = $db->prepare($sql);
	$results->execute(array($id));
    $row = $results->fetch();
    
    $description = getSummary($row['content']);
//    $description = $row['content'];

    $delete = $db->prepare('delete from links where linkurl = ?');
    $delete->execute(array($linkurl));

	$results = $db->prepare("insert into links select null, :title, :linkurl, null, :description, null, now(), :type from items where items.id = :id");
	$params = array(
		':title' => $row['title'], 
		':linkurl' => $row['linkurl'], 
		':description' => $description, 
		':type' => $type, 
		':id' => $id
	);
	
	try{
	    $results->execute($params);
	} catch(Exception $e){
	
	}
	
	return $row['linkurl'];
	
}



function trainItem($id, $action){

	global $db, $site, $nb;

    if(empty($id)){
        return;
    }

	$sql = "SELECT title, linkurl, content from items where id = ?";
	
	$results = $db->prepare($sql);
	$results->execute(array($id));
    $row = $results->fetch();
    
    $docid =  $row['linkurl'] ;
    $doc = $row['title'] . ' ' . getSummary($row['content']);

    if($action == 'like' || $action == 'dislike'){
    
        $nb->train($docid, $action, $doc); 
        $nb->updateProbabilities();
    
    } else if($action == 'unlike' || $action == 'undislike'){
    
        $nb->untrain($docid, $action, $doc); 
        $nb->updateProbabilities();
    
    }
    

}




function saveUrlToHistory($url){

   global $db, $site;  
      
    if(empty($url)){
        return;
    }

    $results = $db->prepare("insert into links values(null, :title, :linkurl, null, :description, null, now(), :type) ");
    $params = array(
		':title' => 'Item Link: ' . $url,
		':linkurl' => $url,
		':description' => '',
		':type' => 'history'
	);
    
	try{
        $results->execute($params);
    } catch(Exception $e){
    
    }

}





function do404(){
	
	global $site;
	
	header("HTTP/1.0 404 Not Found");
	
	echo '<div id="content">';
	echo '<div class="admin">';
	echo '<h1>Not Found - Sorry!</h1>';
	echo '<p>We couldn\'t find that page, please try another URL</p>';
	echo '</div></div>';
	
}




