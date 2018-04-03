<?php

namespace attendance\Http\Controllers;

use attendance\Module;
use attendance\Reward;
use attendance\User;
use Illuminate\Http\Request;
use Auth;

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
            return view('pages.rewardPage-tutor')->with($data);
        }
    }

    /**
     * Delete the reward from the module
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function deleteReward()
    {
        //get the reward ID
        $rewardID = request()->deleteReward;

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
        if ($rewardName != null || $rewardName == "" || ctype_space($rewardName)) {
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
