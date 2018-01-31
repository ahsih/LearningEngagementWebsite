<?php

namespace attendance\Http\Controllers;

use attendance\Conversation;
use attendance\FirstChoiceUserModule;
use Auth;

class ConversationController extends Controller
{
    /**
     * Create a new controller instance.
     * Return to login screen , if it is not auth
     *
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Change the default live chat module
     */
    public function changeLiveChatModule(){
        //Get the ID the user wish to change
        $moduleID = request()->moduleID;

        //Change in the first choice live chat system
        $firstChoiceModule = FirstChoiceUserModule::where('user_id','=',Auth::user()->id)->first();
        //If first choice module id is not same as the module id that has been given, then change
        if($firstChoiceModule->module_id != $moduleID){
            $firstChoiceModule->module_id = $moduleID;
            $firstChoiceModule->timestamps = false;
            $firstChoiceModule->save();
        }
    }

    /**
     * Store the message to the conversation, which can then be display
     * to the chat.
     */
    public function sendLiveChatMessage(){
        //Get the message
        $textMessage = request()->textMessage;
        //Create a new conversation model that store the new message
        $conversation = new Conversation();
        //set message
        $conversation->message = $textMessage;
        //Set the ID
        $conversation->user_id = Auth::user()->id;
        //Set the module
        //Get the first choice module from the current user
        $firstChoiceModule = FirstChoiceUserModule::where('user_id','=',$conversation->user_id)->first()->module_id;
        $conversation->module_id = $firstChoiceModule;
        //save the conversation
        $conversation->save();

        return $textMessage;
    }


}