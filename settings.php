<?

	$page['title'] = 'Settings';

	//$saveJS = "javascript:(function(){q=location.href;if(document.getSelection){d=document.getSelection();}else{d='';}p=document.title;open('" . $site['url'] . "/addlink?close=true&url='+encodeURIComponent(q)+'&description='+encodeURIComponent(d)+'&title='+encodeURIComponent(p),'Add Link','toolbar=no,width=600,height=500')})();";

	$saveJS = "javascript:(function(){q=location.href;sel=document.getSelection();ps=document.getElementsByTagName('p');if(sel.toString()){d=sel.toString();}else if(ps[0]){d=ps[0].innerHTML;}else{d='';}p=document.title;s=document.createElement('script');s.src='" . $site['url'] . "/saveLinkJS?linkurl='+encodeURIComponent(q)+'&description='+encodeURIComponent(d)+'&title='+encodeURIComponent(p);document.body.appendChild(s);})();";

?>

<div class="content">

	<div class="settingsPage">

	<div>
	<h3>Token</h3>
	Your security token: <?=getToken()?>
	</div>

	<div>
	<h3>Add Link Script</h3>
	<p>
	Drag this to bookmarks bar: <a class="btn" href="<?=$saveJS?>" title="Add Link" alt="Add Link">Add Link</a>
	</p>
	<textarea><?=$saveJS?></textarea>
	</div>

	<div>
	<h3><a href="#" onclick="navigator.registerContentHandler('application/vnd.mozilla.maybe.feed','<?=$site['url']?>/addSource?url=%s','My Sources');return false;">Click here to add <?=$site['title']?> to list of browser news readers</a></h3> 
	</div>



	</div>
	
</div>
