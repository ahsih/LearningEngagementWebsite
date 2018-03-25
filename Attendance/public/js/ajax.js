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
    //Close the module alert
    $('#closeModuleAlert').click(function () {
        $('#moduleError').css("visibility", "hidden");
    });
    $('#closeModuleSuccess').click(function () {
        $('#moduleSuccess').css("visibility", "hidden");
    })
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
                $('#modules').append("'" + moduleName + "'");
                $('#moduleSuccess').css("visibility", "visible");
            } else if (data == "false") {
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
            } else if (data == "empty") {
                //Empty text not permitted
                $('#sendTextChat').val('');
                $('#messageConfirmation').text('Text message cannot be empty');
            }
            else {

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
    //Every 2 seconds update the chat
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
                    if (window.location.pathname == '/' || window.location.pathname == '/home') {
                        location.reload();
                    }
                }
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
    $('#directToPolling').click(function () {
        window.location.href = "/polling";
    });

    //--------------------------------------------------------------
    //--------------------------------------------------------------

    //Add optional answer/append optional answer
    $('#addMoreAnswer').click(function () {

        //Get the value
        var countValue = $('#optionalAnswersCount').val();
        countValue++;

        //Change the value
        $('#optionalAnswersCount').val(countValue);

        //add to the optional answer part
        var optionalAnswer = "<label>Optional Answer " + countValue + "</label>";
        optionalAnswer += "<input type='text' class='form-control' name='optionalAnswers" + countValue + "' placeholder='optional answer " + countValue + "'/>";

        //Add to the select list
        var optionalAnswerSelect = "<option value='" + countValue + "'>Optional Answer " + countValue + "</option>";

        //Append to the both optional select and option question
        $('#correctAnswerOption').append(optionalAnswerSelect);
        $('#optionalAnswerBox').append(optionalAnswer);
    });

    //--------------------------------------------------------------
    //--------------------------------------------------------------

    //Ajax for saving the student response
    $('.optionSelected').click(function () {

        var optionalAnswerValue = $(this).find('input:hidden').val();
        var questionValue = $(this).find('input.questionID').val();

        //Call the Ajax to save the response
        var request = $.ajax({
            type: 'POST',
            url: '/saveResponse',
            data: {
                'optionalAnswerValue': optionalAnswerValue,
                'questionValue': questionValue,
            }
        });

        //If the ajax request is completed.
        request.done(function (data) {
            if (data == 'optionalNotExist') {
                $('#studentPollingNotifications').append("<p class='text-danger'>Oh no, look like somebody change the questionID or optional ID!</p>");
            } else if (data == 'responseExist') {
                $('#studentPollingNotifications').append("<p class='text-danger'>This user has already respond to this poll!</p>")
            } else {
                $('div#question' + data).remove();
            }
        });

    });

    //--------------------------------------------------------------
    //--------------------------------------------------------------

    //Every 10 seconds check if there are new classroom polling for student to fill in
    window.setInterval(function () {
        //Get the classroom polling data
        $.ajax({
            type: 'GET',
            url: '/getClassroomPolling',
            data: null,
            //If it success
            success: function (data) {

                if (data != "No Data") {
                    if (window.location.pathname == '/' || window.location.pathname == '/home') {
                        location.reload();
                    }
                }
            }
        });
    }, 10000);

    //--------------------------------------------------------------
    //--------------------------------------------------------------

    //Get the total amount of lesson
    $('#moduleListLesson').change(function () {
        //Get the module ID and find out how many lesson are there in this module
        var moduleID = $('#moduleListLesson').val();
        //Get the total amount of the lesson
        //call ajax
        $.ajax({
            type: 'GET',
            url: '/getTotalAmountLesson',
            data: {
                'moduleID': moduleID,
            },

            //if it successful, then we need to find
            success: function (data) {
                $('#hiddenAmountOfLesson').val(data);
                $('#amountOfLesson').empty();
                $('#amountOfLesson').append(data);
            }
        });
    });

    //Get all the lesson belong to this module
    $('#moduleListLesson').change(function () {
        //Get the module ID and find out how many lesson are there in this module
        var moduleID = $('#moduleListLesson').val();
        //Get the total amount of the lesson
        //call ajax
        $.ajax({
            type: 'GET',
            url: '/getAllLessonsFromModule',
            data: {
                'moduleID': moduleID,
            },
            //if it successful, then we need to find
            success: function (data) {
                $('#listOfLessons').find('h5').remove();
                //If data length is more than 1
                if (data.length > 0) {
                    for (var i = 0; i < data.length; i++) {
                        var headingFive = "<h5 class='margin-zero-top noMarginBottom font-navy'>" + data[i].lesson_name + "</h5>";
                        $('#listOfLessons').append(headingFive);
                    }
                }
            }
        });
    });

    //--------------------------------------------------------------
    //--------------------------------------------------------------

    //Does the same thing as above except it getting all the lesson from this module
    $('#moduleListPolling').change(function () {
        //Get the module ID and find out how many lesson are there in this module
        var moduleID = $('#moduleListPolling').val();
        //Get the total amount of the lesson
        //call ajax
        $.ajax({
            type: 'GET',
            url: '/getLessonsFromModule',
            data: {
                'moduleID': moduleID,
            },

            //if it successful, then we need to find
            success: function (data) {
                //Remove all the option
                $('#lessonList').find('option').remove();
                if (data.length > 0) {
                    for (var i = 0; i < data.length; i++) {
                        var lessonOption = "<option value=" + data[i].id + ">" + data[i].lesson_name + "</option>"
                        $('#lessonList').append(lessonOption);
                    }
                }
            }
        });
    });


});
