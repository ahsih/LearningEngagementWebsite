<?php

namespace attendance\Http\Controllers;

use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function store(){

        //Create new group
        $group = Group::create(['name' => request('name')]);

        $users = collect(request('users'));
        $users->push(auth()->user()->id);

        $group->users()->attach($users);

        broadcast(new GroupCreated($group))->toOthers();

        return $group;

    }
}
