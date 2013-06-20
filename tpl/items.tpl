
	
<% 
  var newFolder = false;
  
  if(folderName !== $.trim($('.folderName').last().text()) ) { 

  newFolder = true;

%>
<div class="folderName" style="color:<%=folderColor%>; border-bottom: 2px solid <%=folderColor%>;">
	<%=folderName%>
</div>
<% } %>

<div class="sourceItem item <%=expandClass%> newFolder-<%=newFolder%>" data-id="<%=id%>" data-color="<%=folderColor%>" data-newFolder="<%=newFolder%>">

	<div class="sourceItemOptions">
	    
	    <span class="markItemUnread button" title="Mark Unread">
			<i class="icon-repeat"></i>	
	    </span>

	    <span class="starItem button" title="Star">
	    	<% if(starred) { %>
				<i class="icon-star"></i>
			<% } else { %>
				<i class="icon-star-empty"></i>
	    	<% } %>
	   	</span>

	    <span class="optionsItem button" title="Options">
			<i class="icon-align-justify"></i>
		</span>
	    
    
	    <span class="expandItem button" title="Expand">
	    	<% if(expand){ %>
				<i class="icon-chevron-down"></i>	
			<% } else { %>
				<i class="icon-chevron-up"></i>	
	    	<% } %>
	    
	    </span>
	
	</div>

	<div class="sourceTitle" data-sourceid="<%=sourceId%>"> 
	 
	    <%=sourceTitle%> 
	
	</div>
	
	<div class="sourceItemTitle">
	
	    <a class="sourceItemLink" href="<%=url%>" target="_blank" title="<%=id%>"><%=title%></a>
	
	</div>

	<div class="sourceItemSubtitle"> 
	
	    
	    <span class="sourceItemPubdate"><%=relativeTime%></span>
	    
	    <% if(author){ %>
	    <span class="sourceItemAuthor">by <%=author%></span> 
		<% } %>
	
	</div>
	
	<div class="sourceItemSummary"><%=summary%></div>
	
	<div class="sourceItemFull"><%=fullText%></div>

</div>
    

