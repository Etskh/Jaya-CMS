
// namespace Site
//
var Site = (function(document){

	// This is the Init area for the site
	//
	$(document).ready(function(){
		// Init
		//

		// When you click a tag,
		// the posts with that tag are summarized
		//
		$('#tag-cloud a').click(function() {

			// Add the tag to the active filters
			$(this).toggleClass('active');

			// Build list of filters and check against all of them
			var filters = [];
			$('#tag-cloud a.active').each(function(){
				filters.push( $(this).text().split("(")[0] );
			});

			console.log(filters);

			// Special case: no filters: show all posts
			if( filters.length == 0 ) {
				$('.post').show('fast');
				return;
			}

			// Now filter all the other
			//
			$('.post .tags').each(function(){
				/// TODO: Summarize the ones you find, not hide the ones you don't.

				var filtersPassed = 0;
				for( var f=0; f<filters.length; f+=1 ) {
					if( $(this).text().indexOf(filters[f]) >= 0 ) {

						if( filters[f] == "content") {
							console.log($(this).text());
						}
						filtersPassed ++;
					}
				}

				if( filtersPassed == filters.length -1 ) {
					$(this).parent().hide('fast');
				}
				else {
					$(this).parent().show('fast');
				}

				// To show we've processed this class
				$(this).parent().addClass('processed');
			});

			// Now for every class, we'll go through them,
			// and if they've been processed, we'll unset our processed class
			// and if they haven't, then hide them!
			//
			$('.post').each(function() {
				if($(this).hasClass("processed")) {
					$(this).removeClass("processed");
				}
				else {
					$(this).hide('fast');
				}
			});
		});
	});

	// This is the Site's API
	//
	return {



	};
})(document);
