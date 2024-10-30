/*
 * Post Bulk Edit Script
 * Hooks into the inline post editor functionality to extend it to our custom metadata
 */

jQuery(document).ready(function($){

    //Prepopulating our quick-edit post info
    var $inline_editor = inlineEditPost.edit;
    inlineEditPost.edit = function(id){

        //call old copy
        $inline_editor.apply( this, arguments);

        //our custom functionality below
        var post_id = 0;
        if( typeof(id) == 'object'){
            post_id = parseInt(this.getId(id));
        }

        //if we have our post
        if( post_id != 0 ){

            //find our row
            $row = $('#edit-' + post_id);

            _post_authentication_custom_metabox_ce21 = $row.find(".be_group:checkbox:checked").map(function(){
                return $(this).val();
            }).get();


        }
        jQuery.ajax({
            url: ajaxurl,
            type: 'post',
            data: { action: 'data_fetch', post_id: post_id  },
            success: function(data) {
                $.each(data.result , function(index, val) {
                    $row.find("input.qbe_group.be_group").each(function(){
                        if($(this).val() == val){
                            $(this).attr('checked', true);
                        }
                    });
                });
            },
            error: function(errorThrown){
                alert(errorThrown);
            }
        });

    }

});
