@extends('layouts.otherPage') @section('pageContent')
    <div class="container lightBlue">
        <div class="panel panel-heading">
            <h3 class="titleText text-center">Setup your module reward</h3>
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-9 col-sm-9 col-sm-9">
                        <div class="text-danger">
                            @if(Session::has('rewardError'))
                                @foreach(Session::get('rewardError') as $error)
                                    <p class="noMarginBottom"><b>{{ $error }}</b></p>
                                @endforeach
                            @endif
                            {{ Session::forget('rewardError') }}
                        </div>
                        <div class="text-success">
                            @if(Session::has('rewardSuccess'))
                                <p class="noMarginBottom"><b>{{ Session::get('rewardSuccess') }}</b></p>
                            @endif
                            {{ Session::forget('rewardSuccess') }}
                        </div>
                        <h4 class="titleText">Add new reward</h4>
                        {!! Form::open(['action' => 'RewardController@createReward']) !!}
                        {!! Form::token() !!}
                        <div class="form-group">
                            <label>Module List</label>
                            <select class="form-control" name="moduleList">
                                @foreach($userModules as $module)
                                    <option value="{{ $module->id }}">{{ $module->module_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Reward Name</label>
                            <input class="form-control" type="text" name="rewardName"
                                   placeholder="Type your reward name here."/>
                        </div>
                        <div class="form-group">
                            <label>Amount to achieve</label>
                            <input class="form-control" type="number" name="amountAchieve" min="1" max="50"/>
                        </div>
                        <button type="submit" class="btn btn-success">Submit</button>
                        {!! Form::close() !!}
                    </div>
                    <div class="col-lg-3 col-sm-3 col-sm-3">
                        <h3 class="titleText text-center noMarginBottom">Your Reward</h3>
                        <small class="text-primary"> Click the minus sign to delete the reward</small>
                        @if(sizeof($listOfRewards) > 0)
                            @foreach($listOfRewards as $reward)
                                {!! Form::open(['action' => 'RewardController@deleteReward']) !!}
                                {!! Form::token() !!}
                                <input type="hidden" value="{{ $reward->id }}" name="deleteRewardID"/>
                                <p class="text-center"><b>
                                    <button class="glyphicon glyphicon-minus-sign buttonWithoutButtonlayout set-red"></button>
                                    <span
                                            class="text-success">Module: {{ $reward->modules->module_name }}</span><span
                                            class="text-warning">Reward: {{  $reward->reward_name }}</span>
                                        <span>Amount to achieve:{{ $reward->amount_to_achieve }}</span></b></p>
                                {!! Form::close() !!}
                            @endforeach
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
@stop