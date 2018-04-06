@extends('layouts.otherPage') @section('pageContent')
    <div class="container lightBlue">
        <div class="panel panel-heading">
            <h3 class="titleText text-center margin-zero-top noMarginBottom">Your Reward</h3>
            <div class="panel-body">
                <h4 class="font-navy">Your Reward Points</h4>
                <table class="table table-striped">
                    <tr>
                        <th>Module Name</th>
                        <th>Reward Point</th>
                    </tr>
                    @if(sizeof($modules) > 0)
                        @foreach($modules as $module)
                            <?php
                            $rewardFound = false;
                            ?>
                            @if(sizeof($module->rewardAchieved) > 0)
                                @foreach($module->rewardAchieved as $rewardAchieved)
                                    @if($rewardAchieved->user_id == Auth::user()->id)
                                        <tr>
                                            <td class="text-primary">{{  $rewardAchieved->module->module_name }}</td>
                                            <td class="text-primary">{{ $rewardAchieved->amount }}</td>
                                        </tr>
                                        <?php
                                        $rewardFound = true;
                                        ?>
                                    @endif
                                @endforeach
                            @endif
                            @if(!$rewardFound)
                                <tr>
                                    <td class="text-primary">{{  $module->module_name }}</td>
                                    <td class="text-primary">0</td>
                                </tr>
                            @endif
                        @endforeach
                    @endif
                </table>
                <h4 class="font-navy">Your award list</h4>
                <table class="table table-striped">
                    <tr>
                        <th>Module Name</th>
                        <th>Award Name</th>
                        <th>Prize</th>
                    </tr>
                    @if(sizeof($userAwards) > 0)
                        @foreach($userAwards as $award)
                            <tr>
                                <td class="text-primary">{{ $award->module->module_name }}</td>
                                <td class="text-primary">{{ $award->reward->reward_name }}</td>
                                @if($award->prize_taken)
                                    <td class="text-success">Prize has been taken</td>
                                @else
                                    <td class="text-danger">Prize has not been taken</td>
                                @endif
                            </tr>
                        @endforeach
                    @endif
                </table>
            </div>
        </div>
    </div>
@stop