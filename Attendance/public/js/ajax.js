/**
 * Ajax call for adding a module
 */
$(document).ready(function () {

    //Attach the content
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    //-------------------------------------------------------------
    //Close the alert
    $('#closeModuleAlert').click(function () {
        $('#moduleError').css("visibility", "hidden");
    });
    //-------------------------------------------------------------

    //--------------------------------------------------------------
    //Add module into the database
    // return the result to the module label
    $('#addModule').click(function () {

        var moduleName = $('#moduleName').val();

        var request = $.ajax({
            type: 'POST',
            url: '/addModule',
            data: {'moduleName': moduleName},
        });

        request.done(function (data) {
            if (data == "true") {
                $('#modules').append(moduleName);
            } else {
                $('#moduleError').css("visibility", "visible");
            }
        });
    });

    //--------------------------------------------------------------

    //Open live chat settings
    $('#changeLiveChat').click(function () {
        $('.liveChat-popup').css("visibility", "visible");
    });

    //Close live chat settings
    $('#closeLiveChatSettings').click(function () {
        $('.liveChat-popup').css("visibility", "hidden");
    });

    //If change module button has been click then close the live chat setting first
    $('#changeModule').click(function () {
        //get the module ID
        var moduleID = $('#liveChatModuleID').val();
        //Make it invisible
        $('#liveChatSettings').css("visibility", "hidden");

        //Send the jax request
        var request = $.ajax({
            type: 'POST',
            url: '/changeLiveChatModule',
            data: {'moduleID': moduleID},
        });

        //Refresh the page if the request is completed.
        //So we get a new live chat
        request.done(function () {
            location.reload();
        });
    });

    //--------------------------------------------------------------
    //if send text has been click on it
    $('#SendText').click(function () {
        //get the text message
        var textMessage = $('#sendTextChat').val();

        //Then pass to the Ajax request
        var request = $.ajax({
            type: 'POST',
            url: '/sendLiveChatText',
            data: {'textMessage': textMessage},
        });

        //Once the request is completed
        request.done(function (data) {
            //You don't do anything, as details are saved into the database
            //Clear text
            $('#sendTextChat').val('');
            //Send a confirmation of the message
            $('#messageConfirmation').text('Text' + data + ': send completed');
        })
    });

    //--------------------------------------------------------------
    //Every 3 seconds update the chat
    window.setInterval(function () {
        //Call the ajax
       var request = $.ajax({

        });

        //chat store in a request and loop and display


    }, 3000);


});