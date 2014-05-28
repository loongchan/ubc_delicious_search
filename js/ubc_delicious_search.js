jQuery(document).ready(function() {
	var default_user = encodeURIComponent(jQuery('.resource_listings').data('user')); 
	var default_limit = encodeURIComponent(jQuery('.resource_listings').data('limit')); 
	var default_tag = encodeURIComponent(jQuery('.resource_listings').data('defaulttag'));
	var feed_url = 'http://feeds.delicious.com/v2/json/'+default_user;
	var search_url = 'https://avosapi.delicious.com/api/v1/posts/public/'+default_user+'/time?&limit='+default_limit+'&has_all=true';

	//initial submission of query.
	submit_delicious_query(search_url+'&tags='+default_tag);

  	/**
  	 * search submit function 
  	 * 
  	 * @param void
  	 * @return void - if results return, fill in result area with list.
  	 */
  	jQuery('#ubc-delicious-submit').click(function(e) {
		var search_term = encodeURIComponent(jQuery('#ubc-delicious-search-term').val());
		var tags = get_all_current_tags(true);
		var query_url = search_url;
		
		//figure out search_url
		if (search_term.length) {
			query_url = query_url + '&keyword='+search_term + (tags.length > 0 ? '&tags='+tags : '');
		} else {
			query_url = query_url + (tags.length > 0 ? '&tags='+tags.replace('+',',') : '');
		}
		
		submit_delicious_query(query_url);
  	});
  	  	
	/**
  	 * Makes it so that clicking on "enter" key also submits search request
  	 * 
  	 * @param void
  	 * @return void - if results return, fill in result area with list.
  	 */
  	//also take into consideration clicking on enter key
  	jQuery('#ubc-delicious-search-term').keyup(function(e) {
  		if (e.keyCode == 13) {
  			jQuery('#ubc-delicious-submit').click();
  		}
  	});
  	
  	/**
  	 * detects changes in dropdown values and requeries
  	 * 
  	 * @param void
  	 * @return void - if results return, fill in result area with list.
  	 */
  	jQuery('.ubc-delicious-dropdown').change(function(e) {
  		if (jQuery('#ubc-delicious-submit').length) {
  			jQuery('#ubc-delicious-submit').click();
  		} else {
  			var tags = get_all_current_tags(true);
  			submit_delicious_query(search_url+'&tags='+ (tags.length > 0 ? '&tags='+tags : ''));
		}
  	});

	/**
	 * submits based on undocumented search json api
	 * 
	 * @param String query_url - absolute url of query string
	 * @return void - if results return, fill in result area with list.
	 */
	function submit_delicious_query(query_url) {
		//include tags
		jQuery.ajax({
	        type: 'GET',
	        url: query_url,
	        dataType: 'jsonp',
	        success: function (jsonp) {
	        	var write_area = jQuery('.resource_listings');
	        	
				//delete everything
	    		write_area.empty().children().remove().empty();
	    		
	    		if (jQuery(jsonp.pkg).length == 0) {
	        		write_area.append('sorry, no results, please broaden search parameters');
	        	} else {
		        	jQuery.each(jsonp.pkg, function(index, client) {
		        		var title= client.title;
		        		var linkURL = client.url;
						write_area.append('<a target="_blank" href="'+linkURL+'">'+title+'</a><br>');			        	
					});
		        }
	        }
	    });
    }

	/**
	 * Pulls all tags from every dropdown and combine them into a string
	 *
	 * @param boolean use_default - determines whether to use default tag or not
	 * @return string - comma separated string of tags
	 *
	 */
	function get_all_current_tags(use_default) {
		use_default = typeof use_default !== 'undefined' ? use_default : true;
	
		var tags = [];
		var selectz = jQuery('.ubc-delicious-dropdown');
		var count = 1;
		jQuery.each(selectz, function(index, client) {
			var select_val = jQuery(client).val().trim();
			if (select_val != 'Show All') {
				tags.push(encodeURIComponent(select_val));
			}
		});
		
		//if no options are selected, then it will default to the page's default tag (aka unit tag)
		if (tags.length == 0 && use_default) {
			tags.push(default_tag);
		}

		return tags.join(',');  		
	}
});