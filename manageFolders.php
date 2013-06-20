<?php 

	$page['title'] = 'Folders';
	
	$results = $db->query('SELECT * from folders order by position');

?>

<div class="content">

	<div class="pull-right">
    <a class="btn btn-primary" href="addFolder">Add Folder</a>
	</div>

	<div class="folders">
	<ul class="folderList">
<?

	foreach($results as $row){
		
		$name = $row['name'];
		if(empty($name)){
		    $name = "Empty Folder Name";
		}
		
		echo '<li data-id="' . $row['id'] . '"><i class="icon-pencil"></i> <a style="color:' . $row['color'] .'" href="' . $site['url'] . '/editFolder?id=' . $row['id'] . '">' . $name . '</a><i class="icon-resize-vertical pull-right"></i></li>' . PHP_EOL;
	
	}
	
?>
	</ul>

	</div>

</div>
	
<script>


$(function() {
	$( ".folderList" ).sortable({
		placeholder: "ui-state-highlight",
		update: function(event, ui) {
			
			var ids = [];
            
            $('.folderList li').each(function(index){
                ids.push( $(this).data('id'));
            });
      
            $.post("folderPosition", {'listItem[]': ids});
		
		}
		
	});
	// $( ".folderList" ).disableSelection();
});

</script>


