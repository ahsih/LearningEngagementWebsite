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
                } else if(data.result == 'NoModuleID'){
                    $('#popUpModuleErrorMessage').empty();
                    $('#popUpModuleErrorMessage').append('<p>No module has been chosen.</p>');
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
                //Reload the page
                location.reload();
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

    //Every 2 seconds check if there are new classroom polling for student to fill in
    window.setInterval(function () {
        //Get the classroom polling data
        $.ajax({
            type: 'GET',
            url: '/getUpdatePolling',
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
            if (data.result == 'optionalNotExist') {
                $('#studentPollingNotifications').append("<p class='text-danger'>Oh no, look like somebody change the questionID or optional ID!</p>");
            } else if (data.result == 'responseExist') {
                $('#studentPollingNotifications').append("<p class='text-danger'>This user has already respond to this poll!</p>")
            } else {
                $('div#question' + data.result).remove();

                //Update the reward point:
                var rewardPoint = " <p class='noMarginBottom'>Your current reward point on this module is: <b class='text-danger circleNumber'>"+ data.rewardPoint +"</b></p>";
                $('#RewardPoint').find('p').remove();
                $('#RewardPoint').append(rewardPoint);

            }
        });

    });

    //--------------------------------------------------------------
    //--------------------------------------------------------------

    //Ajax for claiming the reward
    $('.rewardClaim').click(function () {

        //Find the reward ID
        var rewardID = $(this).find('#reward').val();

        //Call the Ajax to save the response
        var request = $.ajax({
            type: 'POST',
            url: '/claimReward',
            data: {
                'rewardID': rewardID,
            }
        });

        //If the ajax request is completed.
        request.done(function (data) {
            // If the result did not fail, then we should change the claim now to already claim
            if (data != 'fail') {
                location.reload();
            }
        });

    });

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
                $('#listOfLessons').find('button').remove();
                //Change the module name
                $('#listOfLessonTitle').empty();
                $('#listOfLessonTitle').append("List of lesson in this module: " + data.moduleName);
                //If data length is more than 1
                if (data.lessons.length > 0) {
                    for (var i = 0; i < data.lessons.length; i++) {
                        var headingFive = "<h5 class='margin-zero-top noMarginBottom font-navy'>" + data.lessons[i].lesson_name + "</h5>";
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
                //Remove all previous questions
                $('#listOfQuestions').find('h5').remove();
                //Remove all the option
                $('#lessonList').find('option').remove();

                //DEFAULT QUESTION TITLE
                //Empty the question title
                $('#questionTitle').empty();
                //Set the lesson title
                $('#questionTitle').append("There is no lesson in this module at the moment!");
                //Set the questions
                if (data.questions.length > 0) {
                    for (var i = 0; i < data.questions.length; i++) {
                        var headingFive = "<h5 class='margin-zero-top noMarginBottom font-navy'>" + data.questions[i].question + "</h5>";
                        $('#listOfQuestions').append(headingFive);
                    }
                }
                //Append all the lesson
                if (data.lessons.length > 0) {
                    //Empty the question title
                    $('#questionTitle').empty();
                    //Set the lesson title
                    $('#questionTitle').append('List of questions in this lesson: ' + data.lessons[0].lesson_name);
                    for (var i = 0; i < data.lessons.length; i++) {
                        var lessonOption = "<option value=" + data.lessons[i].id + ">" + data.lessons[i].lesson_name + "</option>"
                        $('#lessonList').append(lessonOption);
                    }
                }
            }
        });
    });

    //Once the lesson has change, we should update the list on the list of question
    $('#lessonList').change(function () {
        //Get the module ID and find out how many lesson are there in this module
        var lessonID = $('#lessonList').val();
        //Get the total amount of the lesson
        //call ajax
        $.ajax({
            type: 'GET',
            url: '/getQuestionsFromLesson',
            data: {
                'lessonID': lessonID,
            },
            //if it successful, then we need to find
            success: function (data) {
                //Remove all previous questions
                $('#listOfQuestions').find('h5').remove();
                //Set the lesson title
                $('#questionTitle').empty();
                $('#questionTitle').append('List of questions in this lesson: ' + data.lessonName);
                //Set the questions
                if (data.questions.length > 0) {
                    for (var i = 0; i < data.questions.length; i++) {
                        var headingFive = "<h5 class='margin-zero-top noMarginBottom font-navy'>" + data.questions[i].question + "</h5>";
                        $('#listOfQuestions').append(headingFive);
                    }
                }

            }
        });
    });

    //Click next question to activate the next lesson
    $('#nextQuestion').click(function () {
        //Get the total amount of the lesson
        //call ajax
        $.ajax({
            type: 'GET',
            url: '/nextLessonQuestion',
            data: null,
            success: function () {
                location.reload();
            }
        });
    });

    //Click stop lesson to reset the
    $('#stopLesson').click(function () {
        //Get the total amount of the lesson
        //call ajax
        $.ajax({
            type: 'GET',
            url: '/stopLesson',
            data: null,
            success: function () {
                location.reload();
            }
        });
    });

    //Click stop lesson to reset the
    $('.tutorQuestionPolling').mouseover(function () {
        //Get the question ID
        var questionID = $(this).find('input:hidden').val();
        //Get the total amount of the lesson
        //call ajax
        $.ajax({
            type: 'POST',
            url: '/createGraph',
            data: {
                'questionID': questionID
            },
            success: function (data) {
                //Create the chart
                createChart(questionID, data.questionName, data.answersArray, data.amountsArray);
            }
        });
    });

    //Click to delete the lesson
    $('#nextQuestion').click(function () {
        //Get the total amount of the lesson
        //call ajax
        $.ajax({
            type: 'GET',
            url: '/nextLessonQuestion',
            data: null,
            success: function () {
                location.reload();
            }
        });
    });

    //Record attendance
    $('#recordAttendance').click(function () {
        //Get the total amount of the lesson
        //call ajax
        $.ajax({
            type: 'GET',
            url: '/recordAttendance',
            data: null,
            success: function (data) {
                //make the attendance notification visible
                $('#attendanceNotification').css("visibility", "visible");
                if (data == 'true') {
                    //Remove any content
                    $('#attendanceNotificationContent').find('p').remove();
                    //Append new content
                    var paragraph = "<p class='text-success'>Start recording user attendance in this module</p>"
                    $('#attendanceNotificationContent').append(paragraph);
                } else if(data == 'false'){
                    //Remove any content
                    $('#attendanceNotificationContent').find('p').remove();
                    //Append new content
                    var paragraph = "<p class='text-danger'>Lesson has already started within 1 hour period of time</p>"
                    $('#attendanceNotificationContent').append(paragraph);
                }else{
                    //Remove any content
                    $('#attendanceNotificationContent').find('p').remove();
                    //Append new content
                    var paragraph = "<p class='text-danger'>No Module has been chosen to record the attendance</p>"
                    $('#attendanceNotificationContent').append(paragraph);
                }
            }
        });
    });

    //Make the attendance notification hidden
    $('#deleteAttendanceNotification').click(function () {
        $('#attendanceNotification').css("visibility", "hidden");
    });

    //Every 1 minute update the live user
    window.setInterval(function () {
        //Get the classroom polling data
        $.ajax({
            type: 'GET',
            url: '/getLiveUsers',
            data: null,
            //If it success
            success: function (data) {
                if (data != null && data.length > 0) {
                    $('#listOfOnlineUsers').find('li').remove();
                    $('#liveUser').find('#NoUsersOnline').remove();
                    for (var i = 0; i < data.length; i++) {
                        var li = "<li class='noMarginBottom text-primary'>" + data[i].name + "</li>"
                        $('#listOfOnlineUsers').append(li);
                    }

                    $('#liveUser').find('#totalOnlineUsers').remove();
                    var totalUsersH3 = "<h5 class='text-success' id='totalOnlineUsers'>Total Online:" + data.length + " users</h5>"
                    $('#liveUser').append(totalUsersH3)
                } else {
                    //remove all the information inside the live chat users
                    $('#listOfOnlineUsers').find('li').remove();
                    $('#liveUser').find('#totalOnlineUsers').remove();
                    $('#liveUser').find('#NoUsersOnline').remove();
                    var noUsersOnline = "<h4 class='font-navy' id='NoUsersOnline'>None of the users is online at the moment</h4>"
                    $('#liveUser').append(noUsersOnline);
                }
            }
        });
    }, 60000);



});
