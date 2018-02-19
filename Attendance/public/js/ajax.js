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

    //-------------------------------------------------------------
    //Select module list
    $('#expandModules').click(function () {
        $('#moduleList').css("visibility", "visible");
    });

    //Close the module list
    $('#closeModuleList').click(function () {
        $('#moduleList').css("visibility", "hidden");
    })

    $('#selectModules').click(function () {
        //get the module ID
        var moduleID = $('#listOfModules').val();

        //Send the ajax request
        $.ajax({
            type: 'POST',
            url: '/selectModule',
            data: {'moduleID': moduleID},
            success: function (data) {
                //Reload the page
                if (data == "moduleAdded") {
                    location.reload();
                } else if (data == "requestAdded") {
                    $('#popUpModuleErrorMessage').empty();
                    $('#popUpModuleErrorMessage').append('<p>Your request to join this module has been notified by your module tutor.</p>');
                } else if (data == 'requestAlreadyMade') {
                    $('#popUpModuleErrorMessage').empty();
                    $('#popUpModuleErrorMessage').append('<p>You have already made the request to join this module</p>');
                } else {
                    $('#popUpModuleErrorMessage').empty();
                    $('#popUpModuleErrorMessage').append('<p>Module already exist in your library!</p>');
                }
            }
        });

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

    //--------------------------------------------------------------
    //Open live chat settings
    $('#changeLiveChat').click(function () {
        $('.hidden-popup').css("visibility", "visible");
    });

    //Close live chat settings
    $('#closeLiveChatSettings').click(function () {
        $('.hidden-popup').css("visibility", "hidden");
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


    //--------------------------------------------------------------
    //if send text has been click on it
    $('#SendText').click(function () {
        //get the text message
        var textMessage = $('#sendTextChat').val();
        var anonymous = 0;
        if ($('#anonymousTick').is(':checked')) {
            anonymous = 1;
        }

        //Then pass to the Ajax request
        var request = $.ajax({
            type: 'POST',
            url: '/sendLiveChatText',
            data: {
                'textMessage': textMessage,
                'anonymous': anonymous
            },
        });

        //Once the request is completed
        request.done(function (data) {
            //You don't do anything, as details are saved into the database
            //Clear text
            $('#sendTextChat').val('');
            //Send a confirmation of the message
            $('#messageConfirmation').text('Text ' + data + ': send completed');
        })
    });
    //--------------------------------------------------------------

    //--------------------------------------------------------------
    //Every 3 seconds update the chat
    window.setInterval(function () {
        //Call the ajax
        //Send no data and it GET request as we getting the data from the controller
        $.ajax({
            type: 'GET',
            url: '/getChatMessage',
            data: null,
            //If it success
            success: function (data) {
                if (data != "No Data") {
                    //Get the conversations
                    var conversations = data.conversations;
                    conversations.forEach(function (index) {
                        var listOfChats = "<ul class=\'list-inline setToZero\'>";
                        //Append the UL
                        listOfChats += "<li><p class='pull-left text-danger'>" + index.fullName + "</p></li>";
                        listOfChats += "<li><p class=\'pull-left text-success\'>" + index.message + "</p></li>";
                        listOfChats += "<li><p class=\'pull-left text-info\'>" + index.created_at + "</p></li>";
                        listOfChats += "</ul>";

                        $('#live-chat-messages').append(listOfChats);
                    });
                }
            }
        });

    }, 3000);


});