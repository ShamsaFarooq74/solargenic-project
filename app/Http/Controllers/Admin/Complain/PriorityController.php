<?php

namespace App\Http\Controllers\Admin\Complain;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Models\TicketPriority;


class PriorityController extends Controller
{

    public function index() {

        $priority = TicketPriority::all();

        return view('admin.priority.index', ['priority' => $priority]);
    }

    public function store(Request $request) {

        $check_priority = TicketPriority::where('priority', trim(strtolower($request->priority_name)))->first();

        if(!($check_priority)) {

            $priority = new TicketPriority();

            $priority->priority = $request->priority_name;

            $priority->save();

            return redirect()->back()->with('success', 'Priority added successfully!');
        }

        else {

            return redirect()->back()->with('error', 'This priority already exists!');
        }
    }

    public function update(Request $request) {

        $id = $request->priority_id;
        $priority_name = $request->priority_name;

        $check_priority = TicketPriority::where('id', '!=', $id)->where('priority', trim(strtolower($priority_name)))->first();

        if(!($check_priority)) {

            $priority = TicketPriority::findOrFail($id);

            $priority->priority = $priority_name;

            $priority->save();

            return redirect()->back()->with('success', 'Priority updated successfully!');
        }

        else {

            return redirect()->back()->with('error', 'This priority already exists!');
        }
    }

    public function delete(Request $request) {

        $id = $request->priority_id;

        $priority = TicketPriority::where('id', $id)->delete();

        return redirect()->back()->with('success', 'Priority deleted successfully!');
    }

}
