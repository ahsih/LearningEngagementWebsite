<?php

namespace attendance\Http\Controllers;

use attendance\Award;
use attendance\FirstChoiceUserModule;
use attendance\Module;
use attendance\Reward;
use attendance\RewardAchieve;
use attendance\User;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\Input;

class RewardController extends Controller
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
     * Show the application management homepage
     * @param $request - request from the user
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //Get the user detail
        $user = User::find($request->user()->id);

        //Get the path
        $path = $request->path();

        //Get this user modules
        $userModules = $user->modules()->get();

        //Get list of reward which this tutor has setup
        $reward = Reward::where('user_id', '=', $user->id)->get();

        $data = array(
            'path' => $path,
            'title' => 'Reward',
            'userModules' => $userModules,
            'listOfRewards' => $reward,
        );

        if ($user->hasRole('tutor')) {

            //Get a list of awards which associate with them
            $tutorAwards = $this->getListOfAwards($user);

            //Push to the array
            $data['tutorAwards'] = $tutorAwards;

            return view('pages.rewardPage-tutor')->with($data);
        }else{
            return view('pages.rewardPage-student')->with($data);
        }
    }

    /**
     * Remove the award from the list
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function removeAward(){
        //List of awards
        $listAwards = Input::all();
        //if the size of array is more than 1, then there is something to be deleted
        if(sizeof($listAwards) > 1){
            foreach($listAwards as $key => $value){
                //if the value is on ( mean the checkbox is ticked)
                //Then we need to delete it from the award list
                if($value == 'on'){
                    $awardID = str_replace("award","",$key);
                    $award = Award::find($awardID);
                    //update the award, that the prize has been taken
                    $award->prize_taken = true;
                    $award->save();
                    session(['awardSuccess' => 'Award updated successfully']);
                }
            }
        }else{
            session(['awardError' => 'Award updated failed, maybe nothing to update?']);
        }

        //Redirect back to the award page
        return redirect('/reward');

    }

    /**
     * Get a list of awards which are associate with this tutor
     * @param $user
     * @return array
     */
    private function getListOfAwards($user)
    {
        $thisTutorAwards = array();
        $thisTutorRewards = $user->rewards;
        //get list of awards in module ID order
        $listAwards = Award::orderBy('module_id')->get();

        //For each award, check against the tutor reward
        //Check whether this tutor has the responsible to take care of this award
        foreach ($listAwards as $award) {
            foreach ($thisTutorRewards as $reward) {
                if ($award->reward_id == $reward->id) {
                    array_push($thisTutorAwards, $award);
                }
            }
        }

        //return the list tutor awards array
        return $thisTutorAwards;
    }

    /**
     * Claim this specific reward from this user
     * @return mixed|string
     */
    public function claimReward()
    {
        //Get the user
        $user = User::find(Auth::user()->id);
        //Get the ID
        $rewardID = request()->rewardID;
        //Get the reward model
        $reward = Reward::find($rewardID);
        //Get the first choice of the module the student/tutor pick
        $firstChoiceModule = FirstChoiceUserModule::where('user_id', '=', $user->id)->first();

        //If the reward is not empty, then we can add to award table
        if ($reward != null) {
            $rewardAchieve = RewardAchieve::where('user_id', '=', $user->id)
                ->where('module_id', '=', $firstChoiceModule->module_id)->first();
            if ($rewardAchieve->amount >= $reward->amount_to_achieve) {
                //Save the award
                $this->saveAward($firstChoiceModule->module_id, $user->id, $rewardID);
                //minus the user reward point by the reward amount to achieve
                //And save
                $rewardAchieve->amount = $rewardAchieve->amount - $reward->amount_to_achieve;
                $rewardAchieve->save();

                //Result link to the reward ID, so we know which reward the user has claimed
                $result = $rewardID;

            } else {
                $result = 'fail';
            }
        }
        return $result;
    }

    /**
     * Save the award
     * @param $moduleID
     * @param $userID
     * @param $rewardID
     */
    private function saveAward($moduleID, $userID, $rewardID)
    {
        //Save award
        $award = new Award();
        $award->module_id = $moduleID;
        $award->user_id = $userID;
        $award->reward_id = $rewardID;
        $award->prize_taken = false;
        $award->save();
    }

    /**
     * Delete the reward from the module
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function deleteReward()
    {
        //get the reward ID
        $rewardID = request()->deleteRewardID;

        //Find the reward
        $reward = Reward::find($rewardID);

        //Check if the reward is empty or not
        if ($reward != null) {
            //Delete the reward
            $reward->delete();
            session(['rewardSuccess' => 'Delete Successfully']);
        } else {
            //Create an array to store error
            $error = array();
            array_push($error, 'Reward fail to delete, maybe the reward was invalid?');
            session(['rewardError' => $error]);
        }

        //Return back to the reward page
        return redirect('/reward');

    }

    /**
     * Create reward for the module
     */
    public function createReward()
    {
        //Get reward name
        $rewardName = request()->rewardName;
        //Get module list ID
        $moduleID = request()->moduleList;
        //Get the amount number
        $amountAchieve = request()->amountAchieve;

        //Get module from module ID
        $module = Module::find($moduleID);

        //Check errors
        $error = $this->checkCreateRewardError($module, $rewardName, $amountAchieve);
        if (sizeof($error) == 0) {
            $this->saveReward($rewardName, $amountAchieve, $moduleID);
            session(['rewardSuccess' => 'Reward has created']);
        } else {
            session(['rewardError' => $error]);
        }

        //Redirect to the reward page
        return redirect('/reward');

    }

    /**
     * Check if there is any error during the creation of the reward
     * @param $module
     * @param $rewardName
     * @param $amountAchieve
     * @return array
     */
    private function checkCreateRewardError($module, $rewardName, $amountAchieve)
    {
        $error = array();
        //Check module
        if ($module == null) {
            array_push($error, 'ModuleID is invalid');
        }
        //Check reward name
        if ($rewardName == null || $rewardName == "" || ctype_space($rewardName)) {
            array_push($error, 'Reward name is empty');
        }
        //Check amount to achieve
        if (!ctype_digit($amountAchieve)) {
            array_push($error, 'Amount is not a numeric');
        }

        return $error;
    }

    /**
     * Save the reward
     * @param $rewardName
     * @param $amountAchieve
     * @param $moduleID
     */
    private function saveReward($rewardName, $amountAchieve, $moduleID)
    {
        //Add them into the reward
        $reward = new Reward();
        $reward->user_id = Auth::user()->id;
        $reward->reward_name = $rewardName;
        $reward->amount_to_achieve = $amountAchieve;
        $reward->module_id = $moduleID;
        $reward->save();
    }

}
