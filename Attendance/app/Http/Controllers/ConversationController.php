<?php

namespace attendance\Http\Controllers;

use attendance\Conversation;
use attendance\FirstChoiceUserModule;
use attendance\User;
use Auth;
use Carbon\Carbon;

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
    public function changeLiveChatModule()
    {
        //Get the ID the user wish to change
        $moduleID = request()->moduleID;

        //Change in the first choice live chat system
        $firstChoiceModule = FirstChoiceUserModule::where('user_id', '=', Auth::user()->id)->first();
        //If first choice module id is not same as the module id that has been given, then change
        if ($firstChoiceModule->module_id != $moduleID) {
            $firstChoiceModule->module_id = $moduleID;
            $firstChoiceModule->timestamps = false;
            $firstChoiceModule->save();
        }
    }

    /**
     * Store the message to the conversation, which can then be display
     * to the chat.
     */
    public function sendLiveChatMessage()
    {
        //Get user ID
        $user = User::find(Auth::user()->id);
        //Get the message
        $textMessage = request()->textMessage;
        //Create a new conversation model that store the new message
        $conversation = new Conversation();
        //set message
        $conversation->message = $textMessage;
        //Set the ID
        $conversation->fullName = $user->name;
        //Set the module
        //Get the first choice module from the current user
        $firstChoiceModule = FirstChoiceUserModule::where('user_id', '=', $user->id)->first()->module_id;
        $conversation->module_id = $firstChoiceModule;
        //save the conversation
        $conversation->save();

        return $textMessage;
    }

    /**
     * Get the message from the conversation
     */
    public function getMessage()
    {
        //Get the user ID
        $user = User::find(Auth::user()->id);

        //Get the user first choice live chat module
        $module = FirstChoiceUserModule::where('user_id', '=', $user->id)->first();

        if($module != null) {
            //List of chats from 3 seconds
            $conversations = Conversation::orderBy('created_at')
                ->where('fullName', '=', $user->name)
                ->where('module_id', '=', $module->module_id)
                ->where('created_at', '>', Carbon::now()->subSeconds(3))->get();

            //Store both username and their conversation
            $data = array(
                'conversations' => $conversations
            );


            return $data;
        }
            return "No Data";
    }


}