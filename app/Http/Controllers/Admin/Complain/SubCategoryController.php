<?php

namespace App\Http\Controllers\Admin\Complain;

use DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Models\TicketCategory;
use App\Http\Models\TicketSubCategory;


class SubCategoryController extends Controller
{

    public function index() {

        $category = TicketCategory::all();
        $sub_category = DB::table('ticket_sub_category')
                        ->join('ticket_category', 'ticket_sub_category.ticket_category_id', 'ticket_category.id')
                        ->select('ticket_sub_category.*', 'ticket_category.id as category_id', 'ticket_category.category_name')
                        ->get();

        return view('admin.sub-category.index', ['category' => $category, 'sub_category' => $sub_category]);
    }

    public function store(Request $request) {

        $check_category = TicketSubCategory::where('ticket_category_id', $request->ticket_category_id)->where('sub_category_name', trim(strtolower($request->sub_category_name)))->first();

        if(!($check_category)) {

            $category = new TicketSubCategory();

            $category->ticket_category_id = $request->ticket_category_id;
            $category->sub_category_name = $request->sub_category_name;
            $category->duration = $request->duration;

            $category->save();

            return redirect()->back()->with('success', 'Sub-Category added successfully!');
        }

        else {

            return redirect()->back()->with('error', 'This sub-category already exists for this category!');
        }
    }

    public function update(Request $request) {

        $id = $request->sub_category_id;

        $check_category = TicketSubCategory::where('id', '!=', $id)->where('ticket_category_id', $request->ticket_category_id)->where('sub_category_name', trim(strtolower($request->sub_category_name)))->first();

        if(!($check_category)) {

            $category = TicketSubCategory::findOrFail($id);

            $category->ticket_category_id = $request->ticket_category_id;
            $category->sub_category_name = $request->sub_category_name;
            $category->duration = $request->duration;

            $category->save();

            return redirect()->back()->with('success', 'Sub-Category updated successfully!');
        }

        else {

            return redirect()->back()->with('error', 'This sub-category already exists for this category!');
        }
    }

    public function delete(Request $request) {

        $id = $request->sub_category_id;

        $category = TicketSubCategory::where('id', $id)->delete();

        return redirect()->back()->with('success', 'Sub-Category deleted successfully!');
    }

}
