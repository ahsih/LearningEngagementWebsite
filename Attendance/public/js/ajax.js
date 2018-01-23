/**
 * Ajax call for adding a module
 */
$(document).ready(function(){

    //Attach the content
    $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });
  

      //Add module into the database
      // return the result to the module label
    $('#addModule').click(function(){
    
        var moduleName = $('#moduleName').val();
    
        $.ajax({
            type:'POST',
            url:'/addModule',
            data:{'moduleName':moduleName},
            success:function(data){
               $('#modules').append(data + " ");
            }

        });
    });

});
