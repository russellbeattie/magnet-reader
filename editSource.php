<?php
	
	$source = $page['source'];

	$folders = $db->query('SELECT id, name from folders order by position');
?>
	
	<div class="content">
	<form method="post" action="<?=$site['url']?>/saveSource" class="edit" >
		<label for="id">ID</label> 
		<input type="text" name="id" value="<?=$source['id']?>" readonly="readonly"/>
		
		<label for="title">Title</label> 
		<input type="text" name="title" value="<?=htmlentities($source['title'], ENT_COMPAT | ENT_HTML401, 'UTF-8')?>" />
		
		<label for="sourceurl">Source URL</label> 
		<input type="url" name="sourceurl" value="<?=$source['sourceurl']?>" />
		
		<label for="siteurl">Site URL</label> 
		<input type="url" name="siteurl" value="<?=$source['siteurl']?>" />
		
		<label for="iconurl">Icon URL</label> 
		<input type="url" name="iconurl" value="<?=$source['iconurl']?>" />
		
		<label for="description">Description</label> 
		<input type="text" name="description" value="<?=htmlentities($source['description'], ENT_COMPAT | ENT_HTML401, 'UTF-8')?>" />
		
		<label for="position">Position</label> 
		<input type="text" name="position" value="<?=$source['position']?>" />
		
		<div>
		<label for="private">Private</label> 
		<input type="checkbox" name="private" <?=$source['private']?'checked="checked"':'';?> />
		
		<label for="active">Active</label> 
		<input type="checkbox" name="active" <?=$source['active']?'checked="checked"':'';?> />
		</div>


		<label for="type">Type</label>
			<select name="type">
				<option value="" <?=$source['type'] == ''?'selected="selected"':'';?>>Normal</option>
				<option value="social" <?=$source['type'] == 'social'?'selected="selected"':'';?>>Social</option>
			</select>

		
		<label for="foldersid">Folder</label>  
			<select name="foldersid">
<?	foreach($folders as $folder){ ?>
			<option value="<?=$folder['id']?>" <?=$folder['id'] == $source['foldersid']? 'selected="selected"' : '';?>><?=$folder['name']?></option>
<?	} ?>
			</select>
		
		<label for="created">Created</label> 
		<input type="text" name="created" value="<?=$source['created']?>" readonly="readonly"/>
		
		<label for="lastupdate">Source Updated</label> 
		<input type="text" name="lastupdate" value="<?=$source['lastupdate']?>" readonly="readonly"/>
		
		<div class="buttons">
		
		<a class="btn btn-primary" href="#" onclick="document.forms[0].submit();return false;">Save</a>

<?if($source['id'] != 'new'){ ?>
		<a class="btn btn-danger pull-right" href="<?=$site['url']?>/deleteSource?id=<?=$source['id']?>" onclick="return confirm('Do you want to delete this source?');">Delete Source</a>
<? } ?>
		
		</div>

	</form>
	
	</div>


