jQuery(function(){
  jQuery('.hippy-archive-list .year').on('click', function(){
    jQuery(this).parents('.hippy-archive-list').find('li > ul').hide();
    jQuery(this).find('+ ul').show();
  })
});
