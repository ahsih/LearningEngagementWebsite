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

        //Check if text message contains inappropriate word
        $fileName = resource_path() . '\InappropriateWord';
        $contents = File::get($fileName);

        //line by line stored as array
        $wordArray = explode("\n", $contents);

        // by default inappropriate word is false;
        $inappropriateWord = false;

        //check if the string/text message contains swearing word
        foreach ($wordArray as $word) {
            if (stripos($textMessage,"fuck") !== false) {
                $inappropriateWord = true;
            }
        }

        //if it contains inappropriate word, then chat should not allow to send this.
        if ($inappropriateWord) {
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
            $conversation->module_id = $firstChoiceModule;
            $conversation->created_at = Carbon::now()->addSeconds(1);
            //save the conversation
            $conversation->save();

            return $textMessage;
        }
    }

    /**
     * Delete the message according to the tutor chosen one
     */
    public function deleteMessage()
    {
        $deleteId = Input::get('deleteValue');
        Conversation::find($deleteId)->delete();

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

        if ($module != null) {
            //count list of chats
            $totalModuleConversations = Conversation::orderBy('created_at')
                ->where('module_id', '=', $module->module_id)
                ->count();

            //Store in session
            //If session has the key, if not then create a new one
            if (!session()->has('totalModuleChats')) {
                session(['totalModuleChats' => $totalModuleConversations]);

                return "No Data";
            } else {
                //Get the session value
                $value = session('totalModuleChats');
                //compare
                //If it different, then redirect the page and save a new session value
                if ($value != $totalModuleConversations) {
                    session(['totalModuleChats' => $totalModuleConversations]);
                    return "redirect";
                }
            }
        }
        return "No Data";
    }


}