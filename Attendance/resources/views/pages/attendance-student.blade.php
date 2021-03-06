@extends('layouts.otherPage') @section('pageContent')
    <div class="container lightBlue">
        <div class="panel panel-heading">
            <h3 class="margin-zero-top noMarginBottom font-navy text-center">Your Recorded Attendance</h3>
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-7 col-md-6 col-sm-6">
                        <h4 class="font-navy">Your Attendance Level</h4>
                        <table class="table table-striped">
                            <tr>
                                <th>Module Name</th>
                                <th>You have attend</th>
                                <th>Total Lessons</th>
                                <th>Attendance Level</th>
                            </tr>
                            @if($modules != null && sizeof($modules) > 0)
                                @foreach($modules as $module)
                                    <tr>
                                        <td>{{ $module->module_name }}</td>
                                        <td>{{ $user->studentAttendance->where('module_id',$module->id)->count() }}</td>
                                        <td>{{ $module->lessonStart->count() }}</td>
                                        <?php
                                        //Get the attendance result
                                        if ($module->lessonStart->count() != 0) {
                                            $result = $user->studentAttendance->where('module_id', $module->id)->count() / $module->lessonStart->count() * 100;
                                            $result = number_format($result, 2, '.', "");
                                        }else{
                                            $result = number_format(0, 2, '.', "");;
                                        }
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
                                @endforeach
                            @endif
                        </table>
                    </div>
                    <div class="col-lg-5 col-md-5 col-sm-5">
                        <h4 class="font-navy">Recorded Attendance</h4>
                        @if($modules != null && sizeof($modules) > 0)
                            @foreach($modules as $module)
                                <h5 class="font-navy">Module {{ $module->module_name }} Register Time</h5>
                                <div class="scrollable-attendance">
                                    <table class="table table-striped">
                                        <tr>
                                            <th class="text-center">Registered On</th>
                                        </tr>
                                        @foreach($user->studentAttendance->where('module_id',$module->id) as $studentAttendance)
                                            <tr>
                                                <td>{{ $studentAttendance->lessonStart->start_time }}</td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop