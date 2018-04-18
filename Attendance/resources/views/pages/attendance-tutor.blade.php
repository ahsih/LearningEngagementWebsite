@extends('layouts.otherPage') @section('pageContent')
    <div class="container lightBlue">
        <div class="panel panel-heading">
            <h3 class="margin-zero-top noMarginBottom font-navy text-center">Attendance For Your Module</h3>
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-8 col-sm-8 col-md-8">
                        <div id="attendanceSetting">
                            <h4 class="font-navy margin-zero-top">Create Attendance Rate</h4>
                            <div class="text-danger">
                                @if(Session::has('attendanceError'))
                                    <p class="noMarginBottom"><b>{{ Session::get('attendanceError') }}</b></p>
                                @endif
                                {{ Session::forget('pollingError') }}
                            </div>
                            <div class="text-success">
                                @if(Session::has('attendanceSuccess'))
                                    <p class="noMarginBottom"><b>{{ Session::get('attendanceSuccess') }}</b></p>
                                @endif
                                {{ Session::forget('attendanceSuccess') }}
                            </div>
                            {!! Form::open(['action' => 'AttendanceController@setAttendanceSetting']) !!}
                            {!! Form::token() !!}
                            <div class="form-group">
                                <label>Modules</label>
                                <select class="form-control" name="modulesList">
                                    @if($modules != null && sizeof($modules) > 0)
                                        @foreach($modules as $module)
                                            <option value="{{ $module->id }}">{{ $module->module_name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Set Your Module Attendance Level</label>
                                <input type="number" step=".01" class="form-control" name="percentRate"
                                       placeholder="Set your acceptable attendance rate for this module"/>
                            </div>
                            <button type="submit" class="btn btn-success pull-right">Submit</button>
                            {!! Form::close() !!}
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-4 col-md-4">
                        <div id="moduleAttendanceRate">
                            <h4 class="font-navy margin-zero-top noMarginBottom">Module Acceptable Attendance Level</h4>
                            <small class="text-primary margin-zero-top">By default, an acceptable attendance is 80%
                            </small>
                            <table class="table table-striped">
                                <tr>
                                    <th>Module Name</th>
                                    <th>Acceptable Attendance Level</th>
                                </tr>
                                @if($modules != null && sizeof($modules) > 0)
                                    @foreach($modules as $module)
                                        <tr>
                                            <td>{{ $module->module_name }}</td>
                                            @if($module->attendanceSetting != null)
                                                <td>{{ $module->attendanceSetting->percentRate }}%</td>
                                            @else
                                                <td>80%</td>
                                            @endif
                                        </tr>
                                    @endforeach
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
                <br>
                <!-- Create a line -->
                <hr>
                <h4 class="font-navy margin-zero-top">Student Attendance In Your Module</h4>
                @if($modules != null && sizeof($modules) > 0)
                    @foreach($modules as $module)
                        <h5 class="font-navy">Module {{ $module->module_name }}-> There are a total of <span
                                    class="text-danger">{{ $module->lessonStart->count() }}</span> lessons</h5>
                        <div class="scrollable-studentAttendance">
                            <table class="table table-striped">
                                <tr>
                                    <th>UserName</th>
                                    <th>Email</th>
                                    <th>Attendance Level</th>
                                </tr>
                                <?php
                                $listOfUsers = DB::table('module_user')
                                    ->join('users', 'module_user.user_id', '=', 'users.id')
                                    ->join('role_user', 'users.id', '=', 'role_user.user_id')
                                    ->leftJoin('student_attendances', 'users.id', '=', 'student_attendances.user_id')
                                    ->select(DB::raw("count('users.id') as attendanceCount, users.*"))
                                    ->where('module_user.module_id', '=', $module->id)
                                    ->groupBy('users.id')
                                    ->orderBy('attendanceCount')
                                    ->get();
                                ?>
                                @foreach($listOfUsers as $user)
                                    @if(\attendance\User::find($user->id)->hasRole('student'))
                                        <tr>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <?php
                                            //Get the attendance result
                                            $user = \attendance\User::find($user->id);
                                            $result = $user->studentAttendance->where('module_id', $module->id)->count() / $module->lessonStart->count() * 100;
                                            $result = number_format($result, 2, '.', "");
                                            ?>
                                            @if($module->attendanceSetting != null)
                                                @if($result < $module->attendanceSetting->percentRate)
                                                    <td class="text-danger">{{ $result }}</td>
                                                @else
                                                    <td class="text-success">{{ $result }}</td>
                                                @endif
                                            @else
                                                @if($result < 80)
                                                    <td class="text-danger">{{ $result }}</td>
                                                @else
                                                    <td class="text-success">{{ $result }}</td>
                                                @endif
                                            @endif
                                        </tr>
                                    @endif
                                @endforeach
                            </table>
                            <?php
                                $amountTutor = 0;
                                //Get a total number of the tutor
                                foreach($module->users as $user){
                                    if($user->hasRole('tutor')){
                                            $amountTutor++;
                                    }
                                }
                            ?>
                            <h5>There are a total of <span class="text-success">{{$module->users->count() - $amountTutor }}</span>
                                students recorded in this module</h5>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
@stop