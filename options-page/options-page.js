jQuery(function($){

  // Add the line highlighting
  jQuery('div.capability-selection:odd').addClass('highlight1');
  jQuery('div.capability-selection:odd:odd').addClass('highlight2');

});


jQuery(window).load(function(){

  // Hide options boxes
  jQuery('div.should-be-closed').removeClass('should-be-closed').find('.hndle').click();
  jQuery('div.should-be-opened').removeClass('should-be-opened');

});
