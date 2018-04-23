<?php

namespace attendance\Http\Controllers;

use attendance\Conversation;
use attendance\FirstChoiceUserModule;
use attendance\User;
use Auth;
use File;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;

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
     * Store the message to the conversation, which can then be display
     * to the chat.
     */
    public function sendLiveChatMessage()
    {
        //Get user ID
        $user = User::find(Auth::user()->id);
        //Get the message
        $textMessage = request()->textMessage;

        // by default inappropriate word is false;
        $inappropriateWord = $this->checkInappropriateWord($textMessage);

        $emptyTextMessage = $this->checkEmptyWord($textMessage);

        //if it contains inappropriate word, then chat should not allow to send this.
        if ($emptyTextMessage) {
            return "empty";
        } else if ($inappropriateWord) {
            return "inappropriate";
        } else {

            //Create a new conversation model that store the new message
            $conversation = new Conversation();
            //set message
            $conversation->message = $textMessage;
            //Set the name
            if (request()->anonymous == 0) {
                $conversation->fullName = $user->name;
            } else {
                $conversation->fullName = "Anonymous";
            }
            //Set the module
            //Get the first choice module from the current user
            $firstChoiceModule = FirstChoiceUserModule::where('user_id', '=', $user->id)->first()->module_id;
            $conversation->user_id = $user->id;
            $conversation->module_id = $firstChoiceModule;
            $conversation->created_at = Carbon::now()->addSeconds(1);
            //save the conversation
            $conversation->save();

            return $textMessage;
        }
    }

    /**
     * @param $textMessage
     * @return bool
     */
    private function checkEmptyWord($textMessage)
    {
        $emptyText = false;
        if ($textMessage == "" || $textMessage == " ") {
            $emptyText = true;
        }
        return $emptyText;
    }

    /**
     * Check if the text message contains inappropriate word
     * @param $textMessage
     * @return bool
     */
    private function checkInappropriateWord($textMessage)
    {
        //Check if text message contains inappropriate word
        $fileName = resource_path() . '\InappropriateWord';
        $contents = File::get($fileName);

        //line by line stored as array
        $wordArray = explode("\n", $contents);

        //Inappropriate boolean = false
        $inappropriateWord = false;

        //check if the string/text message contains swearing word
        foreach ($wordArray as $word) {
            if (stripos($textMessage, $word) !== false) {
                $inappropriateWord = true;
            }
        }
        return $inappropriateWord;
    }

    /**
     * Delete the message according to the tutor chosen one
     */
    public function deleteMessage()
    {
        //Get the user ID
        $user = User::find(Auth::user()->id);
        //Get the delete ID
        $deleteId = Input::get('deleteValue');
        //Find the conversation message
        $conversation = Conversation::find($deleteId);

        if ($conversation != null) {
            if ($user->hasRole('student')) {
                if ($conversation->user_id == $user->id) {
                    $conversation->delete();
                } else {
                    session(['noPermissionToDelete' => 'You do not have the permission to delete this message']);
                }
            } else {
                //Delete the conversation
                Conversation::find($deleteId)->delete();
            }
        } else {
            session(['noPermissionToDelete' => 'You do not have the permission to delete this message']);
        }


        //Redirect the page
        return redirect('/');
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

        $result = "No Data";

        if ($module != null) {

            //count list of chats
            $totalModuleConversations = Conversation::where('module_id', '=', $module->module_id)->count();

            //Store in session
            //If session has the key, if not then create a new one
            if (!session()->has('totalModuleChats')) {
                session(['totalModuleChats' => $totalModuleConversations]);

            } else {
                //Get the session value
                $value = session()->get('totalModuleChats');
                //compare
                //If it different, then redirect the page and save a new session value
                if ($value != $totalModuleConversations) {
                    session(['totalModuleChats' => $totalModuleConversations]);
                    $result = "redirect";
                }
            }
        }

        return $result;
    }


}