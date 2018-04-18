@extends('layouts.homePage') @section('module')
    <div class="container lightBlue">
        <div class="panel panel-default">
            <div class="panel-body">
                <!-- Get all the modules the tutor teaches -->
                <Label id="modules">Your Study Modules:
                    <span class="text-danger">
                    @foreach ($modules as $module)
                        {{ $module->module_name }}
                    @endforeach
                    </span>
                </label>
                </br>
                <h4>Request To Join The Module:</h4>
                <input type="button" class="btn btn-success" id="expandModules" value="Module List"></br>
                <hint>You can ask your module tutor to remove you from the module</hint>
            </div>
        </div>
    </div>
@stop
@section('polling feature')
    <div class="panel panel-heading">
        <h2 class="module-bottom-zero font-navy">@if ($moduleName != null)Classroom Polling Module:
            {{ $moduleName }}
            @else You Do Not Have A Classroom Polling Module
            @endif </h2>
        <div class="panel-body">
            <div id="studentPollingNotifications"></div>
            @if($questions != null && sizeof($questions) > 0)
                @for($i = 0;$i <= $activeLesson->question_count;$i++)
                    @if(!\attendance\Response::where('question_id','=',$questions[$i]->id)
                ->where('user_id','=',Auth::user()->id)
                ->exists())
                        <div id="question{{ $questions[$i]->id }}">
                            <div class="panel-heading classroomHeading">
                                <p class="noMarginBottom">{{ $questions[$i]->question }}</p>
                            </div>
                            @foreach($questions[$i]->optionalAnswers as $optionalAnswer)
                                <a href="#" class="optionSelected">
                                    <input type="hidden" value="{{ $optionalAnswer->id }}"/>
                                    <input type="hidden" class="questionID" value="{{ $questions[$i]->id }}"/>
                                    <div class="classroomAnswer">
                                        <p class="marginBottomByFive">{{ $optionalAnswer->optional_answer }}</p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p class="text-primary"> You have already filled the question: <span
                                    class="text-danger">{{ $questions[$i]->question }}</span></p>
                    @endif
                @endfor
            @else
                <p> None of the Lesson Polling Has Been Started yet</p>
            @endif
        </div>
    </div>
@stop
@section('reward')
    <div class="container lightBlue">
        <div class="panel panel-heading">
            <h2 class="noMarginBottom margin-zero-top font-navy text-center">Rewards</h2>
            <div class="panel-body noPadding">
                <div id="rewardAmountTitle">
                    @if($rewardPoint > 0)
                        <p class="noMarginBottom">Your current reward point on this module: {{ $moduleName }} is: <b
                                    class="text-danger circleNumber">{{ $rewardPoint }}</b></p>
                    @else
                        <p class="margin-zero-top noMarginBottom"> You have no reward point on this
                            module: {{ $moduleName }}</p>
                    @endif
                    <small>Each Polling Respond Give You One Point</small><br>
                    <small class="margin-zero-top">You lose your reward point once you claim the reward!</small>
                </div>
                <div class="scrollable-rewardList">
                    <table class="table table-condensed fixLayout">
                        <tr>
                            <th>Reward Name</th>
                            <th>Amount to achieve</th>
                            <th>Required</th>
                            <th>Claim</th>
                        </tr>
                        @if($rewardList != null && sizeof($rewardList) > 0)
                            @foreach($rewardList as $reward)
                                <?php
                                $foundAward = false;
                                ?>
                                <tr>
                                    <td>Prize: {{ $reward->reward_name }}</td>
                                    <td>{{ $reward->amount_to_achieve }}</td>
                                    @if(sizeof($award) > 0)
                                        @foreach($award as $index)
                                            @if($index->reward_id == $reward->id && $index->prize_taken == false)
                                                <td class="text-success">Please notify your module tutor</td>
                                                <td id="reward{{ $reward->id }}">
                                                    <button class="btn btn-primary">Already Claimed</button>
                                                </td>
                                                <?php
                                                $foundAward = true;
                                                ?>
                                            @endif
                                        @endforeach
                                    @endif
                                    @if(!$foundAward)
                                        @if($rewardPoint >= $reward->amount_to_achieve)
                                            <td class="text-success">You Are Ready To Claim!</td>
                                            <td id="reward{{ $reward->id }}">
                                                <a class="rewardClaim">
                                                    <input type="hidden" value="{{ $reward->id }}" id="reward"/>
                                                    <button class="btn btn-success" id="claimTheReward">Claim Now
                                                    </button>
                                                </a>
                                            </td>
                                        @else
                                            <td class="text-danger">You
                                                Required: {{ $reward->amount_to_achieve - $rewardPoint }} more
                                                points
                                            </td>
                                            <td>
                                                <button class="btn btn-danger" disabled>Cannot Claim</button>
                                            </td>
                                        @endif
                                    @endif
                                </tr>
                            @endforeach
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop