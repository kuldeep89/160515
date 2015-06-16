jQuery(document).ready(function($) {


  /* Get list of all files needing upload and push to CDN individually via AJAX */
  /*
$("a#cfcdn_manual_upload").click( function(e){
    e.preventDefault();

    blog_url = $(this).data("blogurl");
    single_url = blog_url + "/wp-admin/?cfcdn_routing=upload_file&path=";
    ajax_url =  blog_url + "/wp-admin/?cfcdn_routing=needing_upload.json";

    $.get( ajax_url, function( data ){
      $("#cfcdn_info").show();
      files = $.parseJSON( data );
      $("#cfcdn_info").html("<p>Found " + Object.keys(files).length.toString() + " files needing upload.");

      $.each( files, function(key, file_path){
         
        $("#cfcdn_info img").show();
        $.get( single_url + file_path, function( file_data ){
          $("#cfcdn_info").append( "<p>" + file_data + "</p>" );
          $("#cfcdn_info img").hide();
        });

      });
      
    });
  });
*/


});
