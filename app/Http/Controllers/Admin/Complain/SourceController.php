<?php

namespace App\Http\Controllers\Admin\Complain;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Models\TicketSource;


class SourceController extends Controller
{

    public function index() {

        $source = TicketSource::all();

        return view('admin.source.index', ['source' => $source]);
    }

    public function store(Request $request) {

        $check_source = TicketSource::where('name', trim(strtolower($request->source_name)))->first();

        if(!($check_source)) {

            $source = new TicketSource();

            $source->name = $request->source_name;

            $source->save();

            return redirect()->back()->with('success', 'Source added successfully!');
        }

        else {

            return redirect()->back()->with('error', 'This source already exists!');
        }
    }

    public function update(Request $request) {

        $id = $request->source_id;
        $source_name = $request->source_name;

        $check_source = TicketSource::where('id', '!=', $id)->where('name', trim(strtolower($source_name)))->first();

        if(!($check_source)) {

            $source = TicketSource::findOrFail($id);

            $source->name = $source_name;

            $source->save();

            return redirect()->back()->with('success', 'Source updated successfully!');
        }

        else {

            return redirect()->back()->with('error', 'This source already exists!');
        }
    }

    public function delete(Request $request) {

        $id = $request->source_id;

        $source = TicketSource::where('id', $id)->delete();

        return redirect()->back()->with('success', 'Source deleted successfully!');
    }

}
