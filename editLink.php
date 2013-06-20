<?php
	
	$link = $page['link'];
	
	
	if($link['id'] != 'new'){
	    $link['type'] = 'active';
	}

?>

    <div class="content">

	<form method="post" action="<?=$site['url']?>/saveLink" class="edit" >
		
		<label for="linkurl">URL</label> 
		<input type="url" name="linkurl" value="<?=$link['linkurl']?>" />
		
		<label for="title">Title</label> 
		<input type="text" name="title" value="<?=$link['title']?>" />

		<label for="description">Description</label>
		<textarea name="description"><?=$link['description']?></textarea>		

        <input type="hidden" name="type" value="<?=$link['type']?>"/>
        <input type="hidden" name="id" value="<?=$link['id']?>"/>

<?
    if($_GET['close'] == 'true'){
?>
        <input type="hidden" name="close" value="true"/>
<?
    }
?>
		<div class="buttons">
		
		<a class="btn btn-primary" href="#" onclick="document.forms[0].submit();return false;">Save</a>

<?if($link['id'] != 'new'){ ?>
		<a class="btn btn-danger pull-right" href="<?=$site['url']?>/delete:ink?id=<?=$link['id']?>" onclick="return confirm('Do you want to delete this link?');">Delete Link</a>
<? } ?>		
		
		</div>
	</form>
		


	
	</div>


