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
    //Close decline module pop up
    $('#deleteDeclineModule').click(function () {

        //Delete all the declined request from the user
        $.ajax({
            type: 'GET',
            url: '/deleteDeclineRequest',
            data: null,
            success: function () {
                //do nothing
            }
        });
        $('#declineModule').css("visibility", "hidden");
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
                } else if (data.result == "requestAdded") {
                    $('#popUpModuleErrorMessage').empty();
                    $('#popUpModuleErrorMessage').append('<p>Your request to join this module <b>' + data.moduleName + '</b> has been notified by your module tutor.</p>');
                } else if (data.result == 'requestAlreadyMade') {
                    $('#popUpModuleErrorMessage').empty();
                    $('#popUpModuleErrorMessage').append('<p>You have already made the request to join this module</p><b>' + data.moduleName + '</b>');
                } else {
                    $('#popUpModuleErrorMessage').empty();
                    $('#popUpModuleErrorMessage').append('<p>Module <b>' + data.moduleName + '</b> already exist in your library!</p>');
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
                $('#modules').append("'"+ moduleName + "'");
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

            if (data == "inappropriate") {
                //Inappropriate word is not allowed
                    $('#sendTextChat').val('');
                $('#messageConfirmation').text('Inappropriate word is not permitted');
            } else {

                //You don't do anything, as details are saved into the database
                //Clear text
                $('#sendTextChat').val('');
                //Send a confirmation of the message
                $('#messageConfirmation').text(' Send completed');
            }
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
                    if (window.location.pathname == '/') {
                        location.reload();
                    }
                }
                /*
              if (data != "No Data") {
                  //Get the conversations
                  var conversations = data.conversations;
                  var csrf_token = $('meta[name="csrf-token"]').attr('content');

                  conversations.forEach(function (index) {
                      var ChatMessage = "<form method=\'POST\' action=\'http://attendances.local:8008/deleteMessage\' accept-charset=\'UTF-8\'>";
                      ChatMessage += "<input type='hidden' value=" + index.id + " name='deleteValue'/>";
                      ChatMessage += "<input type='hidden' value='"+ csrf_token + "' name='_token'>";
                      ChatMessage += "<ul class=\'list-inline setToZero\'>";
                      //Append the UL
                      ChatMessage += "<li><p class='pull-left text-danger'>" + index.fullName + "</p></li>";
                      ChatMessage += "<li><p class=\'pull-left text-success\'>" + index.message + "</p></li>";
                      ChatMessage += "<li><p class=\'pull-left text-info\'>" + index.created_at + "</p></li>";
                      ChatMessage += "<li class='invisibleDeleteMessage pull-left text-info'><button type='submit' class='glyphicon glyphicon-minus-sign set-red buttonWithoutButtonlayout'></button></li>";
                      ChatMessage += "</ul>";
                      ChatMessage += "</form>";

                      $('#live-chat-messages').append(ChatMessage);

                  });
                  */
            }
        });
    }, 2000);
    //--------------------------------------------------------------

    //--------------------------------------------------------------
    //Make the delete message invisible/visible
    $('.conversationMessage').mouseover(function () {
        $('.invisibleDeleteMessage').css("visibility", "visible");
    });

    $('.conversationMessage').mouseout(function () {
        $('.invisibleDeleteMessage').css("visibility", "hidden");
    });

    //Student delete their own message
    $('.studentOwnMessage').mouseover(function () {
        $('.studentDeleteMessage').css("visibility", "visible");
    });

    $('.studentOwnMessage').mouseout(function () {
        $('.studentDeleteMessage').css("visibility", "hidden");
    });

    //Direct to polling
    $('#directToPolling').click(function (){
        window.location.href = "/polling";
    });

    //--------------------------------------------------------------
    //--------------------------------------------------------------

    //Add optional answer/append optional answer
    $('#addMoreAnswer').click(function (){

        //Get the value
        var countValue = $('#optionalAnswersCount').val();
        countValue++;

        //Change the value
        $('#optionalAnswersCount').val(countValue);

        var optionalAnswer =  "<label>Optional Answer " + countValue + "</label>";
        optionalAnswer += "<input type='text' class='form-control' name='optionalAnswers"+ countValue +"' placeholder='optional answer "+countValue+"'/>";

        $('#optionalAnswerBox').append(optionalAnswer);
    });

    //--------------------------------------------------------------
    //--------------------------------------------------------------


});
