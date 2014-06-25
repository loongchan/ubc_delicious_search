jQuery(document).ready(function() {
	var default_user = encodeURIComponent(jQuery('.resource_listings').data('user')); 
	var default_limit = encodeURIComponent(jQuery('.resource_listings').data('limit')); 
	var default_tag = encodeURIComponent(jQuery('.resource_listings').data('defaulttag'));
	var default_useor = encodeURIComponent(jQuery('.resource_listings').data('useor'));
	var reset_button =jQuery('#ubc-delicious-reset');
	var feed_url = 'http://feeds.delicious.com/v2/json/'+default_user; //left it here so that if search is destroyed, we can still use filters
	var search_url = 'https://avosapi.delicious.com/api/v1/posts/public/'+default_user+'/time?limit='+default_limit+'&has_all=true&tagsor='+default_useor;
	
	//if reset exists, then reset form
	if (reset_button.length > 0) {
		reset_button.click(function(e) {
			//don't submit
			e.preventDefault();
			
			//reset all non select inputs
			jQuery('.ubc-delicious-input:not(select)').val('');	
			
			
			//reset select boxes
			jQuery('select.ubc-delicious-input').prop('selectedIndex', 0);
			
			//properly set selects with default
			jQuery('select.ubc-delicious-input option').prop('selected', function() {
				if (this.defaultSelected) {
					return this.defaultSelected;
				}
			});
			
			//re-query again.  Since we are in reset, if it exists, then submit MUST exist
			jQuery('#ubc-delicious-submit').click();
		});
	} 

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
			query_url = query_url + '&keywords='+search_term + (tags.length > 0 ? '&tags='+tags : '');
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
				var view_type = encodeURIComponent(jQuery('.resource_listings').data('view'));
				var return_string = '';

				//delete everything
	    		write_area.empty().children().remove().empty();

	    		if (jQuery(jsonp.pkg).length == 0) {
	        		write_area.append('sorry, no results, please broaden search parameters');
	        	} else {
	        		switch (view_type) {
	        			case 'links':
							jQuery.each(jsonp.pkg, function(index, client) {
				        		var title= client.title;
				        		var linkURL = client.url;
								return_string += '<a target="_blank" href="'+linkURL+'">'+title+'</a><br>';
							});
							break;
	        			case 'list':
	        			case 'list_unordered':
	        			case 'list_ordered':
						default:
	        				if (view_type != 'list_ordered') {
	        					return_string += '<ul>';
	        				} else {
	        					return_string += '<ol>';
							}
							
							jQuery.each(jsonp.pkg, function(index, client) {
				        		var title= client.title;
				        		var linkURL = client.url;
								return_string += '<li><a target="_blank" href="'+linkURL+'">'+title+'</a></li>';
							});
							
							if (view_type != 'list_ordered') {
	        					return_string += '</ul>';
	        				} else {
	        					return_string += '</ol>';
							}
	        				break;
					}	
	        		write_area.append(return_string);
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
		if (use_default) {
			tags.push(default_tag);
		}

		return tags.join(',');  		
	}
});
