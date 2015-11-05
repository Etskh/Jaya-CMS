
$(document).ready( function() {

    function showTab ( name, callback ) {

        $('#header-'+name).click( function() {
            // Remove all active header links
            $('.link').removeClass('active');

            // Make name active
            $('#header-'+name).addClass('active');

            if( callback ) {
                callback();
            }
        });
    }

    showTab('updates', function(){
        Posts.showAll();
    });

    $('#header-updates').click();

    /*showTab('projects', function() {
        Posts.filterOr(['project']);
    });*/
});
