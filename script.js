$(function() {
  // TODO: Seperate backend backend from frontend scripts

  // Manage field dependencies (Field edit form)
  // TODO: This definition is just a temporary solution. We should define it inside the editor.php (Attributes on bunch control items currently not working)
  //       https://github.com/juek/CustomSections/issues/19
  $(document).on("CustomSection:bunchControlLoaded", function(){
    var $formWrapper = $('#gp_admin_boxc:first');
    // Subvalues Field
    if($formWrapper.find("textarea[id*='options']")){
      $formWrapper.find("textarea[id*='options']").parents(".editor-ctl-box:first").hide();
    }
  });
});
