var menuOpen = false;
var md = false;

var templateNames = ['items']
var templates = {};


$(document).ready(function(){


	bootbox.animate(false);
    
    $('.foldersToggle').on('click.dropdown.data-api', function(e){
    	
    	$.getJSON('apiFolders', function(data){
    	
    		var folders = '';
    	
			for(var i = 0; i < data.length; i++){
			
				var folder = data[i];
			
				if(folder['name'] == 'All'){
					
					folders += '<li class="all"><a href="stream">All (' + folder['unread'] + ')</a></li>';
				
				} else {
				
					var li = '';
					
					li += '<li>';
					li += '<a style="color:' + folder['color'] + '" href="stream#' + folder['name'] + '">' + folder['name'] + '&nbsp;(' + folder['unread'] + ')</a>';
					li += '<span title="Mark folder read" class="markFolderRead" data-id="' + folder['id'] + '" data-name="' + folder ['name'] + '">x</span>';
					li += '</li>';
					
					folders += li;
			
				}
			}
			
			$('.folderMenu').html(folders);	
			
			$('.markFolderRead').on('click', markFolderRead);
    	
		});
    
    
    
    });
    

    
	function markFolderRead(e){
	
		var $folder = $(e.target);
	
		var id = $folder.data('id');

		bootbox.confirm('Do you want to mark the ' + $folder.data('name') + ' folder as read?', function(result){

			if(result){

				$.post('markFolderRead', {'folder': id}, function(data) { 
				
				    document.location.reload(true);
			  });
		
			} 
		
		});
	
	}
/*

	for(var i = 0; i < templateNames.length; i++){	
	
		var name = templateNames[i];

		$.get('tpl/' + name + '.tpl', function (data) {
			templates[name] = data;
		});
	
	}

*/

});



