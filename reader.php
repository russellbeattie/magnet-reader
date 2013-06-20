<?php 

	$page['title'] = 'Reader';

?>

	<script>
	$().ready(function(){
			
			$('.title').each(function(){

				var urlRegex = /(https?:\/\/[^\s]+)/g;
				urlRegex = /[A-Za-z]+:\/\/[A-Za-z0-9-_]+\.[A-Za-z0-9-_:%&~\?\/.=]+/g;
				var $title = $(this);
				var text = $title.text();

				var urls = text.match(urlRegex);

				if(urls){
					var url = urls[0];

					$.getJSON('http://api.longurl.org/v2/expand?format=json&url=' + encodeURIComponent(url) + '&callback=?', function(data) {
							if(data){
								//console.log(data['long-url']);
								var newurl = data['long-url'];

								var strippedUrl = newurl.replace(/\?([^#]*)/, function(_, search) {
									search = search.split('&').map(function(v) {
									  return !/^utm_/.test(v) && v;
									}).filter(Boolean).join('&'); // omg filter(Boolean) so dope.
									return search ? '?' + search : '';
								});
								
								if ( newurl != strippedUrl ) {
								    newurl = strippedUrl;
								}

							    var newText = text.replace(urlRegex, function(url) {
							        return '<a class="new" href="' + newurl + '">' + newurl + '</a>';
							    })

							    var exp = /(^|\s)#(\w+)/g;
							    newText = newText.replace(exp, "$1<a href='http://search.twitter.com/search?q=%23$2' target='_blank'>#$2</a>");
							    exp = /(^|\s)@(\w+)/g;
							    newText = newText.replace(exp, "$1<a href='http://www.twitter.com/$2' target='_blank'>@$2</a>");


							    console.log(url, newurl, newText);
							    $title.html(newText);
							}
					});
				}

			});
		});
	</script>
<style>
	.result{
		padding: 20px;
		border: 1px solid #ccc;
		margin: 10px;
	}
	.result a{
		margin: 10px;
	}
	.result .new{
		display: block;
		font-weight: bold;
		color: red;
	}
	</style>

	
<div class="content">
	
    <div class="links">
<?

    
    $results = $db->query('SELECT title, linkurl, pubdate from items where content like "%reader%" and pubdate > date_sub(now(), interval 6 hour) order by pubdate desc');


	foreach($results as $row){
	
	
	    $day = date('l F j, Y', strtotime($row['pubdate']));
	    
        if($row['title']){
            $title = $row['title'];
        } else {
            $title = $row['linkurl'];
        }
       
		echo '<p class="result"><a href="' . $row['linkurl'] . '"> ' . $row['pubdate'] . '</a><span class="title">' . $row['title'] . '</span></p>' . PHP_EOL;   

	}

?>
	</div>

</div>


