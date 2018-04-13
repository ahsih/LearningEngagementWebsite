@extends('layouts.otherPage') @section('pageContent')
    <div class="container lightBlue">
        <div class="panel panel-heading">
            <h3 class="margin-zero-top noMarginBottom font-navy text-center">Attendance for your module</h3>
            <div class="panel-body">
                <h4 class="font-navy margin-zero-top">Student Attendance in your module</h4>
                @if($modules != null && sizeof($modules) > 0)
                    @foreach($modules as $module)
                        <h5 class="font-navy">Module {{ $module->module_name }}-> There are total of <span
                                    class="text-danger">{{ $module->lessonStart->count() }}</span> lessons</h5>
                        <div class="scrollable-studentAttendance">
                            <table class="table table-striped">
                                <tr>
                                    <th>UserName</th>
                                    <th>Email</th>
                                    <th>Attendance rate</th>
                                </tr>
                                <?php
                                $listOfUsers = DB::table('module_user')
                                    ->join('users', 'module_user.user_id', '=', 'users.id')
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
                                            <td>{{ $result }}</td>
                                        </tr>
                                    @endif
                                @endforeach
                            </table>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
@stop