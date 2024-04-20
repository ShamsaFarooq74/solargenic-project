<?php


namespace App\Http\Controllers\Api;

use App\Http\Models\Plant;
use DB;
use Auth;
use App\Http\Models\Ticket;
use App\Http\Models\TicketAgent;
use App\Http\Models\TicketStatus;
use App\Http\Models\TicketPriority;
use App\Http\Models\TicketDueIn;
use App\Http\Models\TicketHistory;
use App\Http\Models\TicketDescription;
use App\Http\Models\TicketAttachment;
use App\Http\Models\TicketSource;
use App\Http\Models\FaultAndAlarm;
use App\Http\Models\TicketCategory;
use App\Http\Models\TicketSubCategory;
use App\Http\Models\User;
use App\Http\Models\Notification;
use App\Http\Models\PlantUser;
use Illuminate\Support\Facades\Validator;
use App\Http\Models\Agent;
use App\Http\Models\Employee;
use Illuminate\Http\Request;


class ComplainController extends ResponseController
{
    public function allComplains(Request $request)
    {
        $plant_user = PlantUser::where('user_id', request()->user()->id)->pluck('plant_id')->toArray();

        $tickets = Ticket::where('status', '!=', 6)->whereIn('plant_id', $plant_user)->orderBy('id','desc')->get();

        $tickets = $tickets->map(function ($ticket) {

            $ticket['status_name'] = TicketStatus::find($ticket->status)->status;
            $ticket['source_name'] = TicketSource::find($ticket->source)->name;
            $ticket['priority_name'] = TicketPriority::find($ticket->priority)->priority;
            if(User::find($ticket->created_by)){
                $ticket['created_by_name'] = User::find($ticket->created_by)->name;
            }
            $ticket['updated_by_name'] = User::find($ticket->updated_by)->name;

            return $ticket;
        });

        foreach($tickets as $key => $tick) {

            $agent_name = '';

            $agent_id = TicketAgent::where('ticket_id', $tick->id)->first();

            if($agent_id) {

                $agent_obj = User::where('id',$agent_id->employee_id)->first();

                if(!($agent_obj)) {

                    $agent_name = '';
                }
                else {

                    $agent_name = $agent_obj->name;
                }
            }

            $default_description = TicketHistory::where('ticket_id', '=', $tick->id)->where('is_default', '=', 1)->first();
            $tick->setAttribute('assign_to', $agent_name);
            $tick->setAttribute('default_desc', $default_description ? $default_description->description : '');
        }

        if(count($tickets) > 0){
            return $this->sendResponse(1, 'Showing all complains',$tickets);
        }

        return $this->sendResponse(1, 'No complains found',$tickets);
    }

    public function ticketData() {

        $priorities = TicketPriority::all();

        $categories = DB::table('ticket_category')
                        ->join('ticket_source_has_category', 'ticket_category.id', 'ticket_source_has_category.category_id')
                        // ->where('ticket_source_has_category.source_id', 1)
                        ->select('ticket_category.category_name', 'ticket_source_has_category.category_id as id',
                                'ticket_source_has_category.source_id', 'ticket_source_has_category.created_at', 'ticket_source_has_category.updated_at')
                        ->get();

        $category_ids = DB::table('ticket_category')
                        ->join('ticket_source_has_category', 'ticket_category.id', 'ticket_source_has_category.category_id')
                        // ->where('ticket_source_has_category.source_id', 1)
                        ->pluck('ticket_source_has_category.category_id')
                        ->toArray();

        $sub_categories = TicketSubCategory::whereIn('ticket_category_id', $category_ids)->get();

        $response = [
            'status' => 1,
            'message' => 'Showing All Data',
            'priorities' => $priorities,
            'categories' => $categories,
            'sub_categories' => $sub_categories,
        ];

        return response()->json($response, 200);
    }

    public function storeComplain(Request $request) {

        // validation
        $validator = Validator::make($request->all(), [
            'plant_id' => 'required',
            'priority' => 'required',
            'category' => 'required',
            'title' => 'required',
            'description' => 'required',
        ]);

        if ($validator->fails()) {

            $response = [
                'status' => 0,
                'message' => 'Error! Some fields are empty!',
            ];

            return response()->json($response, 500);
        }

        $attachments = [];

        if($request->hasfile('attachment'))
        {
            foreach($request->file('attachment') as $file)
            {
                $name = $file->getClientOriginalName();
                $name = date("dmyHis.").gettimeofday()["usec"].'_'.$name;
                $directory = "/complain_attactment/";
                $path = base_path() . "/public" . $directory;
                $file->move($path, $name);
                $attachments[] = $name;
            }
        }

        // creating tickets
        $input =  $request->all();

        $un_st_id = TicketStatus::where('status', '=', 'Unassigned')->pluck('id');
        $sourc = TicketSource::where('name', '=', 'Manual')->pluck('id');
        $duration = TicketSubCategory::where('id', '=', $request->sub_category)->pluck('duration');
        $companyData = Plant::select('company_id')->where('id',$request->plant_id)->first();
        $input['company_id'] =  $companyData->company_id;

        $input['created_by'] = $request->user()->id;
        $input['updated_by'] =  $request->user()->id;

        $input['status'] = isset($un_st_id[0]) ? $un_st_id[0] : 1;
        $input['source'] = isset($sourc[0]) ? $sourc[0] : 1;

        $ticket_responce = new Ticket();

        $ticket_responce->plant_id = $request->plant_id;
        $ticket_responce->company_id = $input['company_id'];
        $ticket_responce->status = $input['status'];
        $ticket_responce->source = $input['source'];
        $ticket_responce->priority = $request->priority;
        $ticket_responce->category = $request->category;
        $ticket_responce->sub_category = $request->sub_category;
        $ticket_responce->due_in = isset($duration[0]) ? $duration[0] : '';
        $ticket_responce->alternate_email = $request->alternate_email;
        $ticket_responce->alternate_contact = $request->alternate_contact;
        $ticket_responce->title = $request->title;
        $ticket_responce->description = $request->description;
        $ticket_responce->received_medium = 'Manual';
        $ticket_responce->platform = $request->platform;
        $ticket_responce->created_by = $input['created_by'];
        $ticket_responce->updated_by = $input['updated_by'];
        $ticket_responce->created_at = date('Y-m-d H:i:s');
        $ticket_responce->updated_at = date('Y-m-d H:i:s');

        $ticket_responce->save();

        //ticket description
        $history['ticket_id'] = $ticket_responce->id;
        $history['user_id'] = $request->user()->id;
        $history['description'] = $input['description'];
        $history['is_default'] = 1;
        $history_responce = TicketHistory::create($history);

        // ticket attachment
        if($request->hasfile('attachment')) {

            $attachment_input['ticket_description_id'] = $history_responce->id;
            foreach($attachments as $key => $attachment) {

                $attachment_type[] = \File::extension($attachment);
                $attachment_input['attachment_type'] = implode(',', $attachment_type);
            }
            $attachment_input['attachment'] = implode(',', $attachments);

            $attachment_responce = TicketAttachment::create($attachment_input);
        }
        else {

            $attachment_input['ticket_description_id'] = $history_responce->id;
            $attachment_input['attachment_type'] = NULL;
            $attachment_input['attachment'] = NULL;

            $attachment_responce = TicketAttachment::create($attachment_input);
        }

        if($ticket_responce){

            $response = [
                'status' => 1,
                'message' => 'Ticket created successfully!',
            ];

            return response()->json($response, 200);

        }
        else{

            $response = [
                'status' => 0,
                'message' => 'Ticket not created!',
            ];

            return response()->json($response, 500);
        }
    }

    public function addComments(Request $request) {

        // validation
        $validator = Validator::make($request->all(), [
            'ticket_id' => 'required',
            'description' => 'required',
        ]);

        if ($validator->fails()) {

            $response = [
                'status' => 0,
                'message' => 'Error! Some fields are empty!',
            ];

            return response()->json($response, 500);
        }

        $attachments = [];

        if($request->hasfile('attachment'))
        {
            foreach($request->file('attachment') as $file)
            {
                $name = $file->getClientOriginalName();
                $name = date("dmyHis.").gettimeofday()["usec"].'_'.$name;
                $directory = "/complain_attactment/";
                $path = base_path() . "/public" . $directory;
                $file->move($path, $name);
                $attachments[] = $name;
            }
        }

        $ticket_responce = Ticket::findOrFail($request->ticket_id);

        $ticket_responce->updated_by = $request->user()->id;
        $ticket_responce->updated_at = date('Y-m-d H:i:s');

        $ticket_responce->save();

        //ticket description
        $history['ticket_id'] = $ticket_responce->id;
        $history['user_id'] = $request->user()->id;
        $history['description'] = $request->description;
        $history['is_default'] = 0;
        $history_responce = TicketHistory::create($history);

        // ticket attachment
        if($request->hasfile('attachment')) {

            $attachment_input['ticket_description_id'] = $history_responce->id;
            foreach($attachments as $key => $attachment) {

                $attachment_type[] = \File::extension($attachment);
                $attachment_input['attachment_type'] = implode(',', $attachment_type);
            }
            $attachment_input['attachment'] = implode(',', $attachments);

            $attachment_responce = TicketAttachment::create($attachment_input);
        }
        else {

            $attachment_input['ticket_description_id'] = $history_responce->id;
            $attachment_input['attachment_type'] = NULL;
            $attachment_input['attachment'] = NULL;

            $attachment_responce = TicketAttachment::create($attachment_input);
        }

        if($ticket_responce){

            $response = [
                'status' => 1,
                'message' => 'Comments added successfully!',
            ];

            return response()->json($response, 200);

        }
        else{

            $response = [
                'status' => 0,
                'message' => 'Comments not added successfully!',
            ];

            return response()->json($response, 500);
        }
    }

    public function ticketHistory(Request $request) {

        $id = $request->ticket_id;

        $description_detail = DB::table('users')
                                ->join('ticket_history', 'users.id', 'ticket_history.user_id')
                                ->select('users.name', 'ticket_history.id', 'ticket_history.ticket_id', 'ticket_history.description as history_desc', 'ticket_history.history_changes', 'ticket_history.created_at')
                                ->where('ticket_history.ticket_id', '=', $id)
                                ->where('ticket_history.is_default', '=', 0)
                                ->orderBy('ticket_history.created_at', 'DESC')
                                ->get();

        foreach($description_detail as $key => $d_d) {

            $d_d->created_at = date('h:i A, d/m', strtotime($d_d->created_at));
        }

        $response = [
            'status' => 1,
            'message' => 'Showing All History',
            'history' => $description_detail,
        ];

        return response()->json($response, 200);
    }

    public function updateTicketStatus(Request $request) {

        $validator = Validator::make($request->all(), [
            'ticket_id' => 'required',
            'status' => 'required',
        ]);

        if ($validator->fails()) {

            $response = [
                'status' => 0,
                'message' => 'Error! Some fields are empty!',
            ];

            return response()->json($response, 500);
        }

        $ticket = Ticket::findOrFail($request->ticket_id);

        if($ticket->status == 6 && $ticket->user_approved == 'N') {

            if($request->status === 'unresolved') {

                $ticket->status = 2;
                $ticket->save();

                $ticket_history = new TicketHistory();

                $ticket_history->ticket_id = $request->ticket_id;
                $ticket_history->user_id = $request->user()->id;
                $ticket_history->description = 'Ticket re-opened by user';

                $ticket_history->save();

                $ticket_attachment = TicketAttachment::create(['ticket_description_id' => $ticket_history->id]);
            }

            else if($request->status === 'resolved') {

                $ticket->user_approved = 'Y';
                $ticket->save();
            }
        }

        $noti = Notification::where('ticket_id', $request->ticket_id)->delete();

        return $this->sendResponse(1, 'Response saved successfully',null);
    }
}
