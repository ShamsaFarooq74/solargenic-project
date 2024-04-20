<?php

namespace App\Http\Controllers\Admin\Complain;

use DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Models\TicketCategory;
use App\Http\Models\TicketSource;
use App\Http\Models\TicketSourceHasCategory;

class CategoryController extends Controller
{

    public function index() {

        $category = TicketCategory::with([
            'ticket_source_has_category' => function ($q1) {
                return $q1->with('ticket_source');
            }])->get();

        $source = TicketSource::all();

        return view('admin.category.index', ['category' => $category, 'source' => $source]);
    }

    public function store(Request $request) {

        $check_category = TicketCategory::where('category_name', trim(strtolower($request->category_name)))->first();

        if(!($check_category)) {

            $category = new TicketCategory();

            $category->category_name = $request->category_name;

            $category->save();

            foreach($request->ticket_source_id as $s_id) {

                $source_has_category = new TicketSourceHasCategory();
                $source_has_category->source_id = $s_id;
                $source_has_category->category_id = $category->id;
                $source_has_category->save();
            }

            return redirect()->back()->with('success', 'Category added successfully!');
        }

        else {

            return redirect()->back()->with('error', 'This category already exists for this source!');
        }
    }

    public function update(Request $request) {

        $id = $request->category_id;

        $check_category = TicketCategory::where('id', '!=', $id)->where('category_name', trim(strtolower($request->category_name)))->first();

        if(!($check_category)) {

            $category = TicketCategory::findOrFail($id);

            $category->category_name = $request->category_name;

            $category->save();

            $s_h_c = TicketSourceHasCategory::where('category_id', $id)->delete();

            foreach($request->ticket_source_id as $s_id) {

                $source_has_category = new TicketSourceHasCategory();
                $source_has_category->source_id = $s_id;
                $source_has_category->category_id = $category->id;
                $source_has_category->save();
            }

            return redirect()->back()->with('success', 'Category updated successfully!');
        }

        else {

            return redirect()->back()->with('error', 'This category already exists for this source!');
        }
    }

    public function delete(Request $request) {

        $id = $request->category_id;

        $s_h_c = TicketSourceHasCategory::where('category_id', $id)->delete();

        $category = TicketCategory::where('id', $id)->delete();

        return redirect()->back()->with('success', 'Category deleted successfully!');
    }

}
