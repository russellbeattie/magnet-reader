<?php
	
	$folder = $page['folder'];

?>

<div class="content">

	<form method="post" action="<?=$site['url']?>/saveFolder" id="edit" >

		<label for="id">ID</label> 
		<input type="text" name="id" value="<?=$folder['id']?>" readonly="readonly"/>
		
		<label for="name">Name</label>
		<input type="text" name="name" value="<?=$folder['name']?>" />
        
        <label for="color">Color</label>
        <span id="picker"></span>
        <input type="text" type="color" id="color" name="color" style="display:none;" value="<?=$folder['color']?>" />
        
		<label for="position">Position</label>
		<input type="text" name="position" value="<?=$folder['position']?>" />
		
		<div class="buttons">
		
		<a class="btn btn-primary" href="#" onclick="document.forms[0].submit();return false;">Save</a>

<?if($folder['id'] != 'new'){ ?>
		<a class="btn btn-danger pull-right" href="<?=$site['url']?>/deleteFolder?id=<?=$folder['id']?>" onclick="return confirm('Do you want to delete this folder?');">Delete Folder</a>
<? } ?>

		</div>
	</form>	



</div>
	
	
	
	<script src="libs/colorpicker.js"></script>
    <script><!--
        $(document).ready(function(){

            var colors = new Array("#69D2E7","#FA6900","#C02942","#542437","#556270","#FF6B6B","#C44D58","#A8DBA8","#79BD9A","#3B8686","#0B486B","#490A3D","#BD1550","#E97F02","#F8CA00","#8A9B0F","#00A0B0","#6A4A3C","#CC333F","#EB6841","#3A111C","#83988E","#2A044A","#0B2E59","#0D6759","#CCCCCC","#AAAAAA","#FF7400","#CDEB8B","#6BBA70","#006E2E","#C3D9FF","#4096EE","#356AA0","#FF0096","#B02B2C","#000000");


            $('#picker').colorPicker(
            { color: colors,           
              defaultColor: colors.indexOf($('#color').val()), // index of the default color
              click:function(c){
                $('#color').val(c);
              }
            });
            
        }); //$(document).ready
 
</script>


