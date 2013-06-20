

var startTime = '';

var folder = '';

var headerHeight = 50;

var getMoreHtml = '<i class="icon-refresh"></i>';

var loadingHtml = '<img src="/images/loading.gif" width="24" height="24"/>';

var optionsMenuHtml = '<div class="optionsMenu popup"><div><span class="optionsEditSource">Edit Source</span><span class="optionsSaveLink">Save Link</span></div></div>';

var render;


$(document).ready(function() {

	headerHeight = $('.navbar').outerHeight()-1;
	
	$('body').append(optionsMenuHtml);
	
	$('.page').click(function(e){
		$('.optionsMenu').fadeOut();
	});
	
	$(window).hashchange(function(e) {
	    $('.sourceItems').html('');
	    window.scrollTo(0,0);
		$.waypoints('refresh');
	    getItems();
	    e.stopPropagation();
	});
	
	
	$('.more').click(function(e) {
	    
	    markPageRead();
	
	});
	
	
	$('.more').waypoint(function(event, direction) {
	    
	    if (direction == 'down') {
	        getItems();
	    }
	
	}, {offset: '120%', onlyOnScroll: true});


	$('.markItemUnread').live('click',function(e){
	
		var $target = $(e.currentTarget);
		
		var $item = $target.parents('.sourceItem');
	
		$item.addClass('item');
    
		$item.removeClass('readItem');
    
		$.get('markUnread?id=' + $item.data('id'));
	
	});
	
	$('.starItem').live('click',function(e){

		var $target = $(e.currentTarget);
		
		if ($target.data('starred') !== true) {
		    
		    var $i = $target.find('i');
		    
		    $i.fadeOut(function() {
		        $i.attr('class','icon-star');
		        $i.fadeIn();
		        $target.off('click');
		    });
		
			var $item = $target.parents('.sourceItem');
		
			$.post('saveItemlink', {
				'id': $item.data('id')
				}, function(data) {
			
					//console.log(data);
			
				}
			);
	   
		}

	
	});
	
	$('.expandItem').live('click',function(e){

		var $target = $(e.currentTarget);
		
		var $item = $target.parents('.sourceItem');

		
		expand($item);
	
	});
	
	
	$('.optionsItem').live('click',function(e){

		var $target = $(e.currentTarget);
		
		var $item = $target.parents('.sourceItem');

		options($item);
	
	});
	
	
	$('.sourceItemSummary').live('click',function(e){

		var $target = $(e.currentTarget);
		
		var $item = $target.parents('.sourceItem');
		
		expand($item);

	});
	

	$('.sourceItemLink').live('click',function(e){

		var $target = $(e.currentTarget);
		
		var $item = $target.parents('.sourceItem');

		new Image().src = 'track?id=' + $item.data('id');
		
	});


	getItems();

});


function updateLinks() {

    $('.open .sourceItemFull').each(function(index, item) {
        
        $(item).find('a').each(function(index, a) {
            
            var url = $(a).attr('href');
            
            $(a).attr('target', '_blank');

            $(a).click(function(e) {
				new Image().src = 'track?url=' + encodeURIComponent(url);
            });

        });
    
    });

}

function markPageRead() {
    
    var ids = [];
    
    $('.item').each(function(index) {
        ids.push($(this).data('id'));
    });
    
    $.get('markRead', {
        'id': ids.join(',')
    }, function() {
        reloadFolder();
    });
}


function reloadFolder() {

    startTime = '';

	$('.sourceItems').html('');

	window.scrollTo(0,0);

    getItems();

}

function expand_($item){

	var	$sub = $('.subPage');

    if ($sub.hasClass('open')) {
    
		$sub.removeClass('open');
		$sub.removeClass('active');
        
        $sub.hide();
        $sub.html('');
        
		//$('.mainPage').show();

    } else {

    	$sub.addClass('open');
    	$sub.addClass('active');

    	$sub.html($item.html());
    	
    	$sub.addClass('open');
    	
		$sub.find('.sourceItemFull').load('getitem?id=' + $item.data('id'), function() {
            
            
            $sub.find('.sourceItemFull a').each(function() {
             
                
                var url = $(this).attr('href');
                
                $(this).attr('target', '_blank');
		        
		        $(this).click(function(e) {
					new Image().src = 'track?url=' + encodeURIComponent(url);
		        });
            
            });
            
            //$('.mainPage').hide();
            $sub.css('top',$(window).scrollTop() + 40);
            $sub.show();
            
            $.waypoints('refresh');
            
            cleanUtm();
        
        });    	
    
    	
    
    }

}



function expand($item) {
        
    if ($item.hasClass('open')) {
        
        $item.find('.sourceItemFull').fadeOut('fast', 'linear', function() {
            $item.removeClass('open');
            $item.removeClass('active');
            $item.find('.sourceItemFull').empty();
			$item.find('.expandItem i').attr('class','icon-chevron-up');            

            $.waypoints('refresh');
        });
        
    } else {
        
        var item_top = $item.offset().top - headerHeight;
        
        $item.find('.sourceItemFull').load('getItem?id=' + $item.data('id'), function() {

            $('.active').removeClass('active');
            $item.addClass('active');
            $item.addClass('open');
			$item.find('.expandItem i').attr('class','icon-chevron-down'); 


            $item.find('.sourceItemFull').fadeIn('fast', 'linear');
            
            
            $item.find('.sourceItemFull a').each(function() {
             
                
                var url = $(this).attr('href');
                
                $(this).attr('target', '_blank');
		        
		        $(this).click(function(e) {
					new Image().src = 'track?url=' + encodeURIComponent(url);
		        });
            
            });
            

            $('html,body').animate({
                'scrollTop': item_top
            }, 400);
            
            $.waypoints('refresh');
            
            cleanUtm();
        
        });
    
    }

}

function options($item){

	var id = $item.data('id');
	var fid = $item.find('.sourceTitle').data('sourceId');
    
    $('.optionsEditSource').unbind('click').bind('click', function(e) {
    	$('.optionsMenu').fadeOut();
        window.open('editsource?id=' + fid);
    });
    
    var target = $item.find('.optionsItem');
    
    var top = target.offset().top + (target.outerHeight());
    var left = target.offset().left + (target.outerWidth() / 2) - ($('.optionsMenu').outerWidth() / 2);
    
    $('.optionsMenu').show().offset({
        'left': left,
        'top': top
    }).hide();

    $('.optionsMenu').fadeIn('fast', 'linear');

}



function getItems(){

	var ids = [];
	var active = [];
    
    $('.item').each(function(index) {
        
        stp = $(window).scrollTop();
        
        tp = $(this).offset().top;
        
        var id = $(this).data('id');

        if (tp < (stp - headerHeight)) {
            
            ids.push(id);
            
            $(this).removeClass('item');
            
            $(this).addClass('readItem');
        
        } else {
            
            active.push(id);
        
        }
    
    });



    $('.more').html(loadingHtml);
    
    folder = window.location.hash;
    folder = folder.replace('#', '');
    
    if (folder !== '') {
   
        $('.pageTitle').html(folder);
        
   
    } else {
   
        $('.pageTitle').html('');
   
    }
    
    //console.log('posting', ids, active);
	
    $.post('apiItems', {
        'folder': folder,
        'items': ids.join(','),
        'active': active.join(','),
        'starttime': startTime
    
    }, function(data){
 		
       
        if (data == '') {
            
            if ($('.item').length == 0) {
               
                if (folder == '') {
                
                    document.location = 'links';
                    return;
                
                } else {
                	
                    document.location = 'stream';
                    return;
                }
          
           } else {
                
                $('.more').html(getMoreHtml);
                
                $.waypoints('refresh');
                
                updateLinks();
            
            }
        
        
        } else {
        
        	if(startTime == ''){
        		startTime = data[0]['created'];
        	}
 		
	 		if(render == null){
	 			var source = $('#itemsTemplate').text();
				render = _.template(source);
			}
			
			for(var i = 0; i < data.length; i++){
			
				var html = render(data[i]);
	 	
	 			$('.sourceItems').append(html);
	 		
	 		}
	 		
			cleanUtm();
            
            $('.more').html(getMoreHtml);
			
			/*
			if($('.sourceItem').length > 20){
				$('.sourceItem').slice(0,10).remove();
				$('.folderName').remove();
			}
			*/
            
            if(folder !== ''){
            	$('.folderName').hide();
            }
            
            $.waypoints('refresh');
            
            updateLinks();
        
        }

 	
 	});



}


function cleanUtm(){

	anchors = document.getElementsByTagName('a');
	for(i=0; i < anchors.length; i++){
		item = anchors[i];
		loc = item.getAttribute('href');
		if(loc.indexOf('utm_') != -1){
			item.setAttribute('href', loc.substr(0, loc.search(/[?&#]utm_/)));
		}
	}

}

