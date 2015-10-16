
// namespace Site
//
var Site = (function(document){

	// This is the Init area for the site
	//
	$(document).ready(function(){
		// Init
		//


		//
		//
		$('#post-list a').click(function(){
			$('#header-posts').click();
		});
		/*$('#header-updates').click(function() {
			// Show the post-list
			$(this).toggleClass('active');
			if( $(this).hasClass('active') ) {
				$('#post-list').fadeIn(300);
			}
			else {
				$('#post-list').fadeOut(300);
			}
		});*/





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


		$('#header .link').click(function(){
			Site.showTab( this.id.split('-').pop() );
		});


		// Show the main posts
		//
		Site.showTab('updates');
	});

	// This is the Site's API
	//
	return {

		showTab : function ( name ) {
			// Remove all active header links
			$('.link').removeClass('active');

			// Make name active
			$('#header-'+name).addClass('active');

			// Hide all other tabs
			$('.tab').fadeOut(100, function(){
				// show `name` tab
				$('#'+name+'-tab').fadeIn(300);
			});
		},

	};
})(document);
