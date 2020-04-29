jQuery(document).ready(function($) {
    var settingsForm = $("form.kaltura-env-form");
    var environmentsContainer = $("#kaltura-environments table td:first");
        
    settingsForm.find(":button[name='Add Another Environment']").on("click",function(e){
        var lastEnv = settingsForm.find(".env:last");
        
        var maxIndex = -1;
        
        if(lastEnv.length > 0){
            maxIndex = lastEnv.data("index");
        }
        
        wp.ajax.post( "get_new_environment", {"max_index":maxIndex} )
          .done(function(response) {
            environmentsContainer.append(response);
          });
        });
    
    settingsForm.on("click",":button[name='Remove']",function(e){
        var envronmentRow = $(this).parent("div");
        if(confirm("Are you sure?")){
            envronmentRow.fadeOut(300,function(){$(this).remove();});
        }
    });
});
