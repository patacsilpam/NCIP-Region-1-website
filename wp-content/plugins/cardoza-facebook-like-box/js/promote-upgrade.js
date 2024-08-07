jQuery(document).ready(function(){

		
	var utm_source = window.location.href;
	
	jQuery.ajax({
		url:'https://johnn.info/plugin/affiliate-links.json',
		method: 'GET',
		headers:
		{
			'utm_source': utm_source  
		},
		success: function(data) {
			
			let affiliate_promotes=data.affiliate_promote;
			let html="";
				
			for(let promote of affiliate_promotes)
			{
				html+='&nbsp;&nbsp'+promote.title+':<a target="_blank" href="'+promote.link+'">'+ promote.label+'</a>&nbsp;&nbsp';
			}
				
			if(jQuery("#footer-thankyou").length)
			{
				jQuery('#footer-thankyou').append(html);
			}
			else
			{
				jQuery('body').append(html);
			}
		},
		error: function(xhr, status, error) {
			console.error('Request failed:', status, error);
		}
		
		
});

});