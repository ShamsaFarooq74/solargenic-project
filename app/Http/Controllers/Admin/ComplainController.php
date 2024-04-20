<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Api\NotificationController;
use App\Http\Models\NotificationEmail;
use DB;
use Artisan;
use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Models\Plant;
use App\Http\Models\PlantUser;
use App\Http\Models\UserCompany;
use App\Http\Models\User;
use App\Http\Models\PlantType;
use App\Http\Models\SystemType;
use App\Http\Models\Company;
use App\Http\Models\Ticket;
use App\Http\Models\TicketAgent;
use App\Http\Models\TicketStatus;
use App\Http\Models\TicketPriority;
use App\Http\Models\TicketDueIn;
use App\Http\Models\TicketHistory;
use App\Http\Models\TicketDescription;
use App\Http\Models\TicketAttachment;
use App\Http\Models\TicketCategory;
use App\Http\Models\TicketSubCategory;
use App\Http\Models\TicketSource;
use App\Http\Models\FaultAndAlarm;
use App\Http\Models\Agent;
use App\Http\Models\Employee;
use App\Http\Models\Notification;


class ComplainController extends Controller
{

    public function __construct()
    {
        date_default_timezone_set("Asia/Karachi");

    }

    public function complain_mgm_system(Request $request)
    {
        $com_arr = [];

        $input = $request->all();

        // dd($request->plant_name);
        $plant_nam = !isset($request->plant_name) || $request->plant_name == null || $request->plant_name == "all" ? 'all' : $request->plant_name;
        $company = !isset($request->company) || $request->company == null || $request->company == "all" ? 'all' : $request->company;
        $plant_type = $request->plant_type == "all" ? '' : $request->plant_type;
        $province = $request->province == "all" ? '' : $request->province;
        $city = $request->city == "all" ? '' : $request->city;
        $plants_input = $request->plants == "all" ? '' : $request->plants;

        $plant_names = [];
        $plant_name = [];
        $company_arr = array();

        if (Auth::user()->roles == 1 || Auth::user()->roles == 2) {
            //dd('Auth::user()->roles == 1');
            $plant_names = Plant::pluck('id');
            $plant_names = $plant_names->toArray();

            $plants = Plant::all();
            $plant_type_id = Plant::groupBy('plant_type')->pluck('plant_type');
            // $system_type_id = Plant::groupBy('system_type')->pluck('system_type');

            $filter_data['company_array'] = Company::all();
            $filter_data['province_array'] = Plant::select('province')->where('province', '!=', NULL)->groupBy('province')->get();
            $filter_data['city_array'] = Plant::select('city')->where('city', '!=', NULL)->groupBy('city')->get();

            // $system_types = array();
            $plant_types = array();
            foreach ($plants as $plant) {
                // $system_types[] = $plant->system_type;
                $plant_types[] = $plant->plant_type;
            }
            // $filter_data['system_type'] = SystemType::whereIn('id',$system_types)->get();
            $filter_data['plant_type'] = PlantType::whereIn('id', $plant_types)->get();

            $filter_data['plants'] = Plant::get(['id', 'plant_name', 'company_id']);
        } else if (Auth::user()->roles == 3 || Auth::user()->roles == 4) {
            //dd('Auth::user()->roles == 3');
            $plant_names = Plant::where('company_id', Auth::user()->company_id)->pluck('id');
            $plant_names = $plant_names->toArray();

            if (empty($plant_names) && Auth::user()->roles == 3) {
                return redirect()->route('admin.build.plant');
            } else if (empty($plant_names) && Auth::user()->roles == 4) {
                return redirect()->route('admin.plants');
            }

            $plants = Plant::whereIn('id', $plant_names)->get();
            $plant_type_id = Plant::groupBy('plant_type')->whereIn('id', $plant_names)->pluck('plant_type');
            // $system_type_id = Plant::groupBy('system_type')->whereIn('id', $plant_names)->pluck('system_type');

            $filter_data['province_array'] = Plant::select('province')->where('province', '!=', NULL)->whereIn('id', $plant_names)->groupBy('province')->get();
            $filter_data['city_array'] = Plant::select('city')->where('city', '!=', NULL)->whereIn('id', $plant_names)->groupBy('city')->get();

            // $system_types = array();
            $plant_types = array();
            foreach ($plants as $plant) {
                // $system_types[] = $plant->system_type;
                $plant_types[] = $plant->plant_type;
            }
            // $filter_data['system_type'] = SystemType::whereIn('id',$system_types)->get();
            $filter_data['plant_type'] = PlantType::whereIn('id', $plant_types)->get();
            $filter_data['plants'] = Plant::whereIn('id', $plant_names)->get();
        }
        if ($plant_nam == 'all') {

            $plant_name = $plant_names;

            //$input['plant_name'] = $plant_name;
        } else {

            $plant_name = $plant_nam;

            $input['plant_name'] = $plant_name;
        }

        if ($company == 'all') {

            $company_arr = UserCompany::where('user_id', Auth::user()->id)->pluck('company_id')->toArray();
            $company_arr = (array)$company_arr;
            $company_plant_arr = Plant::whereIn('company_id', $company_arr)->pluck('id')->toArray();
            $plant_name = array_intersect($plant_name, $company_plant_arr);

            if (isset($request->company)) {

                $input['company'] = $company_arr;
                //$input['plant_name'] = $plant_name;
            }
        } else {

            $company_arr = $company;
            $company_arr = (array)$company_arr;
            $company_plant_arr = Plant::whereIn('company_id', $company_arr)->pluck('id')->toArray();
            $plant_name = array_intersect($plant_name, $company_plant_arr);

            if (isset($request->company)) {

                $input['company'] = $company_arr;
                $input['plant_name'] = $plant_name;
            }
        }

        Session::put(['filter' => $input]);

        $plant_name = (array)$plant_name;

        $where_array = array();

        if ($plant_type) {
            $where_array['plants.plant_type'] = $plant_type;
        }

        if ($province) {
            $where_array['plants.province'] = $province;
        }
        if ($city) {
            $where_array['plants.city'] = $city;
        }
        $plants_dashboard = Plant::where($where_array)->whereIn('id', $plant_name)->pluck('id');
        // return $plants_dashboard;
        // return [$where_array,$plants_dashboard,$plant_name];
        Artisan::call('cache:clear');
        $ticket_category = DB::table('tickets')->whereIn("plant_id", $plants_dashboard)
            ->join('ticket_agent', 'tickets.id', 'ticket_agent.ticket_id')
            ->join('ticket_category', 'tickets.category', 'ticket_category.id')
            ->join('users', 'ticket_agent.employee_id', 'users.id')
            ->select('users.id', 'users.name', 'ticket_category.category_name', DB::raw('COUNT(tickets.id) as ticket_count'))
            ->where('tickets.status', '!=', 6)
            ->groupBy('users.id')
            ->get();
        // return $ticket_category;
        $ticket_total = Ticket::where('status', '!=', 6)->whereIn("plant_id", $plants_dashboard)->get(['id', 'status', 'due_in', 'created_at']);
        // return $ticket_total;

        $curr_approach_count = 0;
        $past_approach_count = 0;
        $ticket_count = 0;

        foreach ($ticket_total as $key => $t_t) {
            // return $t_t;

            $ticket_count++;

            $due_in = date("Y-m-d H:i:s", strtotime($t_t->created_at) + (int)($t_t->due_in) * 3600);

            if (strtotime($due_in) > strtotime(date('Y-m-d H:i:s'))) {

                $curr_approach_count++;
            } else if (strtotime($due_in) <= strtotime(date('Y-m-d H:i:s'))) {

                $past_approach_count++;
            }
        }

        $ticket_company = DB::table('tickets')->whereIn("plant_id", $plants_dashboard)
            ->join('ticket_agent', 'tickets.id', 'ticket_agent.ticket_id')
            ->join('companies', 'tickets.company_id', 'companies.id')
            ->join('users', 'ticket_agent.employee_id', 'users.id')
            ->select('users.id', 'users.name', 'companies.company_name', DB::raw('COUNT(tickets.id) as ticket_count'))
            ->where('tickets.status', '!=', 6)
            ->groupBy('users.id')
            ->get();


        $max_ticket_plants = DB::table('tickets')->whereIn("plant_id", $plants_dashboard)
            ->join('plants', 'plants.id', 'tickets.plant_id')
            ->select('plant_id', 'plants.plant_name', DB::raw('count(*) as total'))
            ->groupBy('plant_id')
            ->limit('5')
            ->orderby('total', 'desc')
            ->get();
        $min_ticket_plants = DB::table('tickets')->whereIn("plant_id", $plants_dashboard)
            ->join('plants', 'plants.id', 'tickets.plant_id')
            ->select('plant_id', 'plants.plant_name', DB::raw('count(*) as total'))
            ->groupBy('plant_id')
            ->limit('5')
            ->orderby('total', 'asc')
            ->get();

        return view('admin.complain.complain-mgm-system', ['filter_data' => $filter_data, 'ticket_category' => $ticket_category, 'ticket_company' => $ticket_company, 'curr_approach_count' => $curr_approach_count, 'max_ticket_plants' => $max_ticket_plants, 'min_ticket_plants' => $min_ticket_plants, 'past_approach_count' => $past_approach_count, 'ticket_count' => $ticket_count]);
    }

    public function list_ticket(Request $request)
    {
        $where_array = array();

        if (Auth::user()->roles == 3 || Auth::user()->roles == 4) {
            $company_id = Auth::user()->company_id;
            $where_array['company_id'] = $company_id;
        }

        $tickets = Ticket::where($where_array);

        if (Auth::user()->roles == 5) {

            $plant_array = PlantUser::where('user_id', Auth::user()->id)->pluck('plant_id')->toArray();

            $tickets->whereIn('plant_id', $plant_array);
        }

        if (Auth::user()->roles == 6) {

            $ticket_array = TicketAgent::where('employee_id', Auth::user()->id)->pluck('ticket_id')->toArray();

            $tickets->whereIn('id', $ticket_array);
        }

        $tickets = $tickets->orderBy('created_at', 'desc')->get();

        foreach ($tickets as $key => $ticket) {
            $agents_array = [];
            $ticket_agents = TicketAgent::where('ticket_id', '=', $ticket->id)->get();
            //dd($ticket_agents);
            foreach ($ticket_agents as $key => $ticket_agent) {
                $ticket_agents_name = User::where('id', '=', $ticket_agent->employee_id)->first();
                if($ticket_agents_name) {
                    array_push($agents_array, $ticket_agents_name->name);
                }
            }
            $ticket->agents = implode(',', $agents_array);
        }

        $tickets = $tickets->map(function ($ticket) {
            $ticket['plant_name'] = Plant::find($ticket->plant_id)->plant_name;
            $ticket['company_name'] = Company::find($ticket->company_id)->company_name;
            return $ticket;
        });

        return view('admin.complain.list_ticket', compact('tickets'));
    }

    public function add_ticket()
    {
        if (Auth::user()->roles == 6) {
            return redirect()->back()->with('error', 'You have no right to add ticket!');
        }

        $where_array = array();
        $where_com_array = array();
        $plants_detail = null;
        if (Auth::user()->roles == 3 || Auth::user()->roles == 4) {
            $company_id = Auth::user()->company_id;
            $where_array['company_id'] = $company_id;
            $where_com_array['id'] = $company_id;
        }

        if (Auth::user()->roles == 5) {

            $plants = DB::table('plants')
                ->join('plant_user', 'plants.id', 'plant_user.plant_id')
                ->select('plants.id', 'plants.plant_name', 'plant_user.user_id')
                ->where('plant_user.user_id', '=', Auth::user()->id)
                ->get();
        } else {

            $plants = Plant::where($where_array)->get();
        }
        $companies = Company::where($where_com_array)->get();
        $status = TicketStatus::where('is_hidden', 0)->get();
        $sources = TicketSource::all();
        $otherSource = TicketSource::where('name', '=', 'Others')->exists() ? TicketSource::where('name', '=', 'Others')->first()->id : 1;
        $priority = TicketPriority::all();

        $employees = DB::table('users')
            ->join('plant_user', 'users.id', 'plant_user.user_id')
            ->select('users.id', 'users.name', 'plant_user.plant_id')
            ->where('users.roles', 6)
            ->get();

        $categories = DB::table('ticket_category')
            ->join('ticket_source_has_category', 'ticket_category.id', 'ticket_source_has_category.category_id')
            ->select('ticket_category.category_name', 'ticket_source_has_category.*')
            ->get();

        $sub_categories = TicketSubCategory::all();

        return view('admin.complain.add_ticket', compact('plants', 'employees', 'companies', 'status', 'sources', 'employees', 'priority', 'categories', 'sub_categories', 'otherSource'));
    }

    public function ticket_plant_detail(Request $request)
    {

        $plant_id = $request->plant_id;

        $plant_detail = DB::table('plants')
            //->join('plant_user', 'plants.id', 'plant_user.plant_id')
            ->join('companies', 'companies.id', 'plants.company_id')
            //->join('users', 'users.id', 'plant_user.user_id')
            ->join('plant_type', 'plant_type.id', 'plants.plant_type')
            ->join('system_type', 'system_type.id', 'plants.system_type')
            ->select('plants.id', 'plants.plant_name', 'plants.phone', 'plants.capacity', 'plants.plant_pic', 'plants.is_online', 'plants.plant_pic', 'plant_type.type as plant_type', 'system_type.type as system_type', 'companies.company_name', 'companies.email as company_email', 'companies.logo')
            ->where('plants.id', '=', $plant_id)
            ->get();

        return $plant_detail;
    }

    public function store_ticket(Request $request)
    {
        if (Auth::user()->roles == 5 || Auth::user()->roles == 6) {

            // validation
            $validator = Validator::make($request->all(), [
                'plant_id' => 'required',
                'priority' => 'required',
                'category' => 'required',
                'title' => 'required',
                'description' => 'required',
            ]);
        } else {

            // validation
            $validator = Validator::make($request->all(), [
                'plant_id' => 'required',
                'company_id' => 'required',
                'status' => 'required',
                'source' => 'required',
                'priority' => 'required',
                'category' => 'required',
                'title' => 'required',
                'description' => 'required',
            ]);
        }

        if ($validator->fails()) {
            Session::flash('message', 'Sorry! Might be required fields are empty.');
            Session::flash('alert-class', 'alert-danger');
            return redirect('admin/add-ticket');
        }

        if ($request->hasfile('attachment')) {
            foreach ($request->file('attachment') as $file) {
                $name = $file->getClientOriginalName();
                $name = date("dmyHis.") . gettimeofday()["usec"] . '_' . $name;
                $file->move(public_path() . '/complain_attactment/', $name);
                $attachments[] = $name;
            }
        }

        // creating tickets
        $input = $request->all();
        $un_st_id = TicketStatus::where('status', '=', 'Unassigned')->pluck('id');
        $sourc = TicketSource::where('name', '=', 'Others')->pluck('id');

        if (Auth::user()->roles == 5 || Auth::user()->roles == 6) {
            $input['company_id'] = Plant::where('id', $request->plant_id)->first()->company_id;
        }
        if ($request->has('notify_by')) {
            $noti_To_array = $input['notify_by'];
            $input['notify_by'] = implode(',', $input['notify_by']);
        }

        $input['created_by'] = Auth::user()->id;
        $input['updated_by'] = Auth::user()->id;

        if (Auth::user()->roles == 5) {

            $input['status'] = isset($un_st_id[0]) ? $un_st_id[0] : 1;
            $input['source'] = isset($sourc[0]) ? $sourc[0] : 1;
        }

        $closed_time = date("Y-m-d H:i:s", strtotime(date('Y-m-d H:i:s')) + (int)($request->due_in) * 3600);

        $ticket_responce = new Ticket();

        $ticket_responce->plant_id = $request->plant_id;
        $ticket_responce->company_id = $input['company_id'];
        $ticket_responce->status = $input['status'];
        $ticket_responce->source = $input['source'];
        $ticket_responce->priority = $request->priority;
        $ticket_responce->category = $request->category;
        $ticket_responce->sub_category = $request->sub_category;
        $ticket_responce->due_in = $request->due_in;
        $ticket_responce->closed_time = $closed_time;
        $ticket_responce->alternate_email = $request->alternate_email;
        $ticket_responce->alternate_contact = $request->alternate_contact;
        $ticket_responce->title = $request->title;
        $ticket_responce->description = $request->description;
        if ($request->has('notify_by')) {
            $ticket_responce->notify_by = $input['notify_by'];
        }
        $ticket_responce->received_medium = 'Manual';
        $ticket_responce->platform = 'web';
        $ticket_responce->created_by = $input['created_by'];
        $ticket_responce->updated_by = $input['updated_by'];
        $ticket_responce->created_at = date('Y-m-d H:i:s');
        $ticket_responce->updated_at = date('Y-m-d H:i:s');

        $ticket_responce->save();

        if ($request->has('assign_to') && $request->assign_to != '') {

            if (Auth::user()->roles == 1 || Auth::user()->roles == 2 || Auth::user()->roles == 3 || Auth::user()->roles == 4) {

                // assigning to agents
                /*foreach ($input['assign_to'] as  $agents) {
                    $agent_input['ticket_id'] = $ticket_responce->id;
                    $agent_input['employee_id'] = $agents;
                    $agent_responce = TicketAgent::create($agent_input);
                }*/
                $agent_input['ticket_id'] = $ticket_responce->id;
                $agent_input['employee_id'] = $input['assign_to'];
                $agent_responce = TicketAgent::create($agent_input);
            }
        }

        //ticket description
        $history['ticket_id'] = $ticket_responce->id;
        $history['user_id'] = Auth::user()->id;
        $history['description'] = $input['description'];
        $history['is_default'] = 1;
        $history_responce = TicketHistory::create($history);

        // ticket attachment
        if ($request->hasfile('attachment')) {

            $attachment_input['ticket_description_id'] = $history_responce->id;
            foreach ($attachments as $key => $attachment) {

                $attachment_type[] = \File::extension($attachment);
                $attachment_input['attachment_type'] = implode(',', $attachment_type);
            }
            $attachment_input['attachment'] = implode(',', $attachments);

            $attachment_responce = TicketAttachment::create($attachment_input);
        } else {

            $attachment_input['ticket_description_id'] = $history_responce->id;
            $attachment_input['attachment_type'] = NULL;
            $attachment_input['attachment'] = NULL;

            $attachment_responce = TicketAttachment::create($attachment_input);
        }
        //Add Notification for all user related to plant
        if($request->has('notify_by')) {

            if( in_array( "app" ,$noti_To_array ) ) {
                if ($request->plant_id) {

                    $plant_user = PlantUser::where('plant_id', $request->plant_id)->get();

                    if ($plant_user) {

                        foreach ($plant_user as $key => $usr) {

                            $noti_app = new Notification();
                            $noti_app->user_id = $usr->user_id;
                            $noti_app->plant_id = $usr->plant_id;
                            $noti_app->ticket_id = $ticket_responce->id;
                            $noti_app->description = "Your ticket # " . $ticket_responce->id . " with title " . $request->title . " has been opened";
                            $noti_app->title = "New Ticket created - Ticket # " . $ticket_responce->id;
                            $noti_app->notification_type = 'Ticket';
                            $noti_app->schedule_date = date('Y-m-d H:i:s');
                            $noti_app->sent_status = 'N';
                            $noti_app->is_msg_app = 'Y';
                            $noti_app->is_notification_required = 'Y';

                            $noti_app->save();
                        }
                    }

                }
            }
            if( in_array( "email" ,$noti_To_array ) ) {
                if ($request->plant_id) {

                    $plant_user = PlantUser::where('plant_id', $request->plant_id)->get();

                    if ($plant_user) {

                        foreach ($plant_user as $key => $usr) {
                            $userMail = User::where('id',$usr->user_id)->first();

                            $noti_mail = new NotificationEmail();
                            $noti_mail->user_id = $usr->user_id;
                            $noti_mail->ticket_id = $ticket_responce->id;
                            $noti_mail->email_type = "New-ticket";
                            if($userMail){
                                $noti_mail->to_email = $userMail->email;
                            }
                            $noti_mail->cc_email = $request->alternate_email;
                            $noti_mail->email_subject = "New Ticket created - Ticket # " . $ticket_responce->id;
                            $noti_mail->email_body = "Your ticket # " . $ticket_responce->id . " with title " . $request->title . " has been opened";
                            $noti_mail->schedule_date = date('Y-m-d H:i:s');
                            $noti_mail->email_sent_status = 'N';

                            $noti_mail->save();
                        }
                    }

                }
                $Commu_controller = new CommunicationController();
                $Commu_controller->send_comm_email();
            }


            $notification_controller = new CommunicationController();
            $notification_controller->send_comm_app_notification();

        }
        // checks
        if ($ticket_responce) {
            Session::flash('message', 'Ticket inserted successfully');
            Session::flash('alert-class', 'alert-success');
            return redirect()->route('admin.ticket.list')->with('success', 'Ticket added successfully!');

        } else {
            Session::flash('message', 'Sorry! Ticket not added');
            Session::flash('alert-class', 'alert-danger');
            return redirect('admin/add-ticket');
        }
    }

    public function view_edit_ticket(Request $request, $id)
    {
        $userID = $request->user()->id;
        $ticket_id = $id;

        //$auth_plant_id = PlantUser::where();

        $ticket = Ticket::where('id', '=', $ticket_id)->first();
        if (!$ticket) {
            return redirect()->back()->with('error', 'Invalid Ticket ID!');
        } else if ((Auth::user()->roles == 3 || Auth::user()->roles == 4) && $ticket->company_id != Auth::user()->company_id) {
            return redirect()->back()->with('error', 'You have no permission to view that ticket!');
        } else if ($ticket->status == 6 && Auth::user()->roles == 5) {
            return redirect()->back()->with('error', 'You have no permission to view closed ticket!');
        }
        $ticket_agent = TicketAgent::where('ticket_id', '=', $ticket_id)->first();
        $plant_detail = Plant::with('company')->where('id', '=', $ticket->plant_id)->get();
        $plant_detail = $plant_detail->map(function ($plant) {
            $plant['plant_type'] = PlantType::find($plant->plant_type)->type;
            $plant['system_type_name'] = SystemType::find($plant->system_type)->type;
            return $plant;
        });

        $plant_detail = $plant_detail[0];

        $default_description = DB::table('ticket_history')
            ->leftJoin('ticket_attachment', 'ticket_history.id', 'ticket_attachment.ticket_description_id')
            ->select('ticket_history.description', 'ticket_attachment.attachment')
            ->where('ticket_history.ticket_id', '=', $ticket_id)
            ->where('ticket_history.is_default', '=', 1)
            ->first();

        $description_detail = DB::table('users')
            ->join('ticket_history', 'users.id', 'ticket_history.user_id')
            ->leftJoin('ticket_attachment', 'ticket_history.id', 'ticket_attachment.ticket_description_id')
            ->select('users.name', 'ticket_history.description', 'ticket_history.history_changes', 'ticket_history.created_at', 'ticket_attachment.attachment')
            ->where('ticket_history.ticket_id', '=', $ticket_id)
            ->where('ticket_history.is_default', '=', 0)
            ->orderBy('ticket_history.created_at', 'DESC')
            ->get();

        foreach ($description_detail as $key => $d_d) {

            $d_d->history_changes = json_decode($d_d->history_changes);
        }

        // return $description_detail;

        $where_array = array();
        $where_com_array = array();
        if (Auth::user()->roles != 1 && Auth::user()->roles != 2) {
            $company_id = Auth::user()->company_id;
            $where_array['company_id'] = $company_id;
            $where_com_array['id'] = $company_id;
        }

        if ($ticket->status == 6) {
            $status = TicketStatus::all();
        } else {
            $status = TicketStatus::where('is_hidden', 0)->get();
        }

        $companies = Company::where($where_com_array)->get();
        $sources = TicketSource::all();
        $due_in = TicketDueIn::all();
        $priority = TicketPriority::all();

        Artisan::call('cache:clear');

        $employees = DB::table('users')
            ->join('plant_user', 'users.id', 'plant_user.user_id')
            ->select('users.id', 'users.name', 'plant_user.plant_id')
            ->where('users.roles', 6)
            ->get();

        $employees_list = DB::select("SELECT users.id, users.name, plant_user.user_id FROM plant_user INNER JOIN users ON plant_user.user_id=users.id WHERE users.roles = 6 GROUP BY users.id");

        $categories = DB::table('ticket_category')
            ->join('ticket_source_has_category', 'ticket_category.id', 'ticket_source_has_category.category_id')
            ->select('ticket_category.category_name', 'ticket_source_has_category.*')
            ->get();
        $sub_categories = TicketSubCategory::all();
        $description_history = TicketHistory::where('ticket_id', $id)->get();
        $ticketCreatedBy = User::where('id',$ticket->created_by)->first();
        $ticketCreatedByuser = isset($ticketCreatedBy->name) ? $ticketCreatedBy->name : "N/A";
//        return [$ticket ,$ticketCreatedByuser,$ticket_agent ,$plant_detail ,$default_description, $description_detail, $employees , $employees_list ,$companies ];
//        return $plant_detail;
        return view('admin.complain.view_edit_ticket', compact('ticket','ticketCreatedByuser', 'ticket_agent', 'plant_detail', 'default_description', 'description_detail', 'employees', 'employees_list', 'companies', 'status', 'sources', 'categories', 'sub_categories', 'priority', 'due_in'));
    }

    public function update_ticket(Request $request, $id)
    {
//		   date_default_timezone_set('Asia/Karachi');
        if (Auth::user()->roles == 5 || Auth::user()->roles == 6) {

            // validation
            $validator = Validator::make($request->all(), [
                'description' => 'required',
            ]);
        } else {

            // validation
            $validator = Validator::make($request->all(), [
                'plant_id' => 'required',
                'company_id' => 'required',
                'status' => 'required',
                'source' => 'required',
                'priority' => 'required',
                'category' => 'required',
                'title' => 'required',
                'description' => 'required',
            ]);
        }

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->messages());
        }

        // creating tickets
        $input = $request->all();
        $un_st_id = TicketStatus::where('status', '=', 'Unassigned')->pluck('id');
        $sourc = TicketSource::where('name', '=', 'Manual')->pluck('id');

        if (Auth::user()->roles == 5 || Auth::user()->roles == 6) {

            $input['company_id'] = Plant::where('id', $request->plant_id)->first()->company_id;
        }

        if ($request->has('notify_by')) {
            $noti_To_array = $input['notify_by'];
            $input['notify_by'] = implode(',', $input['notify_by']);
        }

        $input['updated_by'] = Auth::user()->id;

        if (Auth::user()->roles == 5 || Auth::user()->roles == 6) {

            $input['status'] = $un_st_id[0];
            $input['source'] = $sourc[0];
        }

        $ticket_responce = Ticket::findOrFail($id);
        $history_json_string = array();

        $closed_time = date("Y-m-d H:i:s", strtotime($ticket_responce->created_at) + (int)($request->due_in) * 3600);

        if (Auth::user()->roles == 1 || Auth::user()->roles == 2 || Auth::user()->roles == 3 || Auth::user()->roles == 4) {

            $ticket_responce->plant_id = $request->plant_id;
            $ticket_responce->company_id = $input['company_id'];
            $ticket_responce->status = $input['status'];
            $ticket_responce->source = $input['source'];
            $ticket_responce->priority = $request->priority;
            $ticket_responce->category = $request->category;
            $ticket_responce->sub_category = $request->sub_category;
            $ticket_responce->due_in = $request->due_in;
            $ticket_responce->closed_time = $closed_time;
            $ticket_responce->alternate_email = $request->alternate_email;
            $ticket_responce->alternate_contact = $request->alternate_contact;
            $ticket_responce->title = $request->title;
            if ($request->has('notify_by')) {
                $ticket_responce->notify_by = $input['notify_by'];
            }
            $ticket_responce->received_medium = $request->received_medium;
            $ticket_responce->platform = 'web';
            $ticket_responce->updated_by = $input['updated_by'];
            $ticket_responce->updated_at = date('Y-m-d H:i:s');

            $ticket_responce->save();

            $tick_agent = TicketAgent::where('ticket_id', $id)->pluck('employee_id')->toArray();
            $tick_agent_dlt = TicketAgent::where('ticket_id', $id)->delete();

            if ($request->has('assign_to') && !empty($request->assign_to)) {

                $agent_history = array_diff([$input['assign_to']], $tick_agent);
            }

            if ($request->has('assign_to') && $request->assign_to != '') {

                if (Auth::user()->roles == 1 || Auth::user()->roles == 2 || Auth::user()->roles == 3 || Auth::user()->roles == 4) {

                    $agent_input['ticket_id'] = $ticket_responce->id;
                    $agent_input['employee_id'] = $input['assign_to'];
                    $agent_responce = TicketAgent::create($agent_input);
                }
            }

            $history_changes = $ticket_responce->getChanges();
            $his_count = count($history_changes);

            $itr = 0;

            foreach ($history_changes as $key => $h_c) {

                if ($key == 'status') {

                    $h_c = TicketStatus::where('id', $h_c)->first()->status;
                    $key = 'Status';
                } else if ($key == 'source') {

                    $h_c = TicketSource::where('id', $h_c)->first()->name;
                    $key = 'Source';
                } else if ($key == 'priority') {

                    $h_c = TicketPriority::where('id', $h_c)->first()->priority;
                    $key = 'Priority';
                } else if ($key == 'due_in') {

                    if ($h_c == "") {
                        $h_c = '';
                    } else {

                        $h_c = $h_c . ' Hrs';
                    }

                    $key = 'Due In';
                } else if ($key == 'sub_category') {

                    $h_c = TicketSubCategory::where('id', $h_c)->first()->sub_category_name;
                    $key = 'Sub Category';
                } else if ($key == 'category') {
                    $h_c = TicketCategory::where('id', $h_c)->first()->category_name;
                    $key = 'Category';
                } else if ($key == 'notify_by') {
                    $key = 'Notify By';
                } else if ($key == 'received_medium') {
                    $key = 'Received By';
                } else if ($key == 'alternate_contact') {
                    $key = 'Alternate Contact';
                } else if ($key == 'alternate_email') {
                    $key = 'Alternate Email';
                } else if ($key == 'updated_by') {
                    $h_c = '';
                }

                if ($itr++ == ($his_count - 1)) {
                    break;
                } else {
                    $history_json_string[$key] = $h_c;
                }
            }

            if ($request->has('assign_to') && !empty($request->assign_to) && count($agent_history) > 0) {

                $it = 0;
                $h_c = '';

                foreach ($agent_history as $a_h) {

                    $h_c_1 = User::where('id', $a_h)->first()->name;

                    if ($it++ == (count($agent_history) - 1)) {

                        $h_c .= $h_c_1;
                    } else {

                        $h_c .= $h_c_1 . ',';
                    }
                }

                $history_json_string['Assign To'] = $h_c;
            } else if ($tick_agent_dlt) {

                $history_json_string['Assign To'] = '';
            }
        }

        // ticket history
        $history['ticket_id'] = $ticket_responce->id;
        $history['user_id'] = Auth::user()->id;
        $history['description'] = $input['description'];
        $history['is_default'] = 0;
        $history['history_changes'] = !empty($history_json_string) ? json_encode($history_json_string) : null;
        $history_responce = TicketHistory::create($history);

        // ticket attachment

        if ($request->hasfile('attachment')) {
            foreach ($request->file('attachment') as $file) {
                $name = $file->getClientOriginalName();
                $name = date("dmyHis.") . gettimeofday()["usec"] . '_' . $name;
                $file->move(public_path() . '/complain_attactment/', $name);
                $attachments[] = $name;
            }
        }

        if ($request->hasfile('attachment')) {

            $attachment_input['ticket_description_id'] = $history_responce->id;
            foreach ($attachments as $key => $attachment) {

                $attachment_type[] = \File::extension($attachment);
                $attachment_input['attachment_type'] = implode(',', $attachment_type);
            }
            $attachment_input['attachment'] = implode(',', $attachments);

            $attachment_responce = TicketAttachment::create($attachment_input);
        } else {

            $attachment_input['ticket_description_id'] = $history_responce->id;
            $attachment_input['attachment_type'] = NULL;
            $attachment_input['attachment'] = NULL;

            $attachment_responce = TicketAttachment::create($attachment_input);
        }
        //Add Notification for all user related to plant
        if($request->has('notify_by')) {

            if( in_array( "app" ,$noti_To_array ) ) {
                if ($request->plant_id) {

                    $plant_user = PlantUser::where('plant_id', $request->plant_id)->get();

                    if ($plant_user) {

                        foreach ($plant_user as $key => $usr) {

                            $noti_app = new Notification();
                            $noti_app->user_id = $usr->user_id;
                            $noti_app->plant_id = $usr->plant_id;
                            $noti_app->ticket_id = $ticket_responce->id;
                            $noti_app->description = "Your ticket # " . $ticket_responce->id . " with title " . $request->title . " has been opened";
                            $noti_app->title = "New Ticket created - Ticket # " . $ticket_responce->id;
                            $noti_app->notification_type = 'Ticket';
                            $noti_app->schedule_date = date('Y-m-d H:i:s');
                            $noti_app->sent_status = 'N';
                            $noti_app->is_msg_app = 'Y';
                            $noti_app->is_notification_required = 'Y';

                            $noti_app->save();
                        }
                    }

                }
            }
            if( in_array( "email" ,$noti_To_array ) ) {
                if ($request->plant_id) {

                    $plant_user = PlantUser::where('plant_id', $request->plant_id)->get();

                    if ($plant_user) {

                        foreach ($plant_user as $key => $usr) {
                            $userMail = User::where('id',$usr->user_id)->first();

                            $noti_mail = new NotificationEmail();
                            $noti_mail->user_id = $usr->user_id;
                            $noti_mail->ticket_id = $ticket_responce->id;
                            $noti_mail->email_type = "Update-ticket";
                            if($userMail){
                                $noti_mail->to_email = $userMail->email;
                            }
                            $noti_mail->cc_email = $request->alternate_email;
                            $noti_mail->email_subject = "Ticket Updated - Ticket # " . $ticket_responce->id;
                            $noti_mail->email_body = "Your ticket # " . $ticket_responce->id . " with title " . $request->title . " has been opened";
                            $noti_mail->schedule_date = date('Y-m-d H:i:s');
                            $noti_mail->email_sent_status = 'N';

                            $noti_mail->save();
                        }
                    }

                }
                $Commu_controller = new CommunicationController();
                $Commu_controller->send_comm_email();
            }



            $notification_controller = new CommunicationController();
            $notification_controller->send_comm_app_notification();

        }
        if ($ticket_responce) {
            return redirect()->back()->with('success', 'Ticket updated successfully!');

        } else {
            return redirect()->back()->with('error', 'Ticket not updated!');
        }
    }

    public function downloadAttachment($name)
    {

        try {

            Artisan::call('cache:clear');

            if (file_exists(public_path() . '/complain_attactment/' . $name)) {
                return response()->download(public_path() . '/complain_attactment/' . $name);
            } else {

                return redirect()->back()->with('error', 'File not exists!');
            }

        } catch (Exception $ex) {

            return redirect()->back()->with('error', 'File not exists!');
        }
    }

    public function updateTicketStatus($id)
    {
   date_default_timezone_set('Asia/Karachi');
        if (Auth::user()->roles == 4 || Auth::user()->roles == 5) {

            return redirect()->back()->with('error', 'You have no permission to close the ticket!');
        }

        $ticket = Ticket::findOrFail($id);
        $ticket->status = 6;
        $ticket->updated_by = Auth::user()->id;
        $ticket->updated_at = date('Y-m-d H:i:s');

        $due_in = date("Y-m-d H:i:s", strtotime($ticket->created_at) + (int)($ticket->due_in) * 3600);
//        return [$due_in,$ticket];
        if ($due_in >= date('Y-m-d H:i:s')) {

            $ticket->closed_approach_status = 1;
        } else {

            $ticket->closed_approach_status = 0;
        }
        $ticket->save();

        $history['ticket_id'] = $id;
        $history['user_id'] = Auth::user()->id;
        $history['description'] = 'Ticket closed';
        $history['is_default'] = 0;
        $history['history_changes'] = null;
        $history_responce = TicketHistory::create($history);


        $plant_id = $ticket->plant_id;
        $ticket_description = TicketHistory::where('ticket_id', $ticket->id)->where('is_default', 1)->pluck('description')[0];

        if ($plant_id) {

            $plant_user = PlantUser::where('plant_id', $plant_id)->get();
//            $ticket = Ticket::findOrFail($id);
            if ($plant_user) {

                foreach ($plant_user as $key => $usr) {
                        $noti_app = new Notification();
                        $noti_app->user_id = $usr->user_id;
                        $noti_app->plant_id = $usr->plant_id;
                        $noti_app->ticket_id = $ticket->id;
                        $noti_app->description = "Your ticket # " . $ticket->id . " marked for closure";
                        $noti_app->title = "Ticket closed - Ticket # " . $ticket->id;
                        $noti_app->notification_type = 'Ticket';
                        $noti_app->schedule_date = date('Y-m-d H:i:s');
                        $noti_app->sent_status = 'N';
                        $noti_app->is_msg_app = 'Y';
                        $noti_app->is_notification_required = 'Y';

                        $noti_app->save();
                    }
                }
            }
        $notification_controller = new CommunicationController();
        $notification_controller->send_comm_app_notification();
        return redirect()->route('admin.ticket.list');

    }

    public function list_ticket_customer(Request $request)
    {
        $plant_id_arr = array();
        $where_array = array();
        $where_com_array = array();
        if (Auth::user()->roles != 1 && Auth::user()->roles != 2) {
            $company_id = Auth::user()->company_id;
            $plant_id_arr = PlantUser::where('user_id', '=', Auth::user()->id)->pluck('plant_id');
            $where_array['company_id'] = $company_id;
            $where_com_array['id'] = $company_id;
        }

        $input = $request->all();
        Session::put(['filter' => $input]);

        $company_id = $request->company_id == "all" ? '' : $request->company_id;
        $plant_id = $request->plant_id == "all" ? '' : $request->plant_id;
        $ticket_id = $request->ticket_id == "all" ? '' : $request->ticket_id;
        $assign_to = $request->assign_to == "all" ? '' : $request->assign_to;
        $date_range = $request->date_range == "all" ? '' : $request->date_range;
        $status = $request->status == "all" ? '' : $request->status;
        $priority = $request->priority == "all" ? '' : $request->priority;
        $due_in = $request->due_in == "all" ? '' : $request->due_in;

        $filter_array = array();
        if ($company_id) {
            $filter_array[] = ['tickets.company_id', '=', $company_id];
        }
        if ($plant_id) {
            $filter_array[] = ['tickets.plant_id', '=', $plant_id];
        }
        if ($ticket_id) {
            $filter_array[] = ['tickets.id', '=', $ticket_id];
        }
//        if($assign_to){
//            $filter_array[] = ['tickets.assign_to','=', $assign_to];
//        }
        if ($status) {
            $join = "";
            $filter_array[] = ['tickets.status', '=', $status];
        }
        if ($priority) {
            $filter_array[] = ['tickets.priority', '=', $priority];
        }
        if ($due_in) {
            $filter_array[] = ['tickets.due_in', '=', $due_in];
        }
        if ($date_range) {
            $date = explode(' to ', $date_range);
            $filter_array[] = ['tickets.created_at', '>=', $date[0] . ' 00:00:00'];
            $filter_array[] = ['tickets.created_at', '<=', $date[1] . ' 23:59:00'];
        }

        if ($assign_to) {

            $tickets = Ticket::where($where_array)->where($filter_array)
                ->whereHas('Agent', function ($query) use ($assign_to) {
                    $query->where('employee_id', '=', $assign_to);
                })
                ->orderBy('id', 'desc')->paginate(10);
        } else {
            $tickets = Ticket::where($where_array)->where($filter_array)->whereIn('plant_id', $plant_id_arr)->orderBy('id', 'desc')->paginate(10);
        }


        foreach ($tickets as $key => $ticket) {
            $agents_array = [];
            $ticket_agents = TicketAgent::where('ticket_id', '=', $ticket->id)->get();
            //dd($ticket_agents);
            foreach ($ticket_agents as $key => $ticket_agent) {
                $ticket_agents_name = Employee::where('id', '=', $ticket_agent->employee_id)->first();
                if($ticket_agents_name) {
                    if (isset($ticket_agents_name->name) ? $ticket_agents_name->name : "N/A") {
                        array_push($agents_array, $ticket_agents_name->name);
                    }
                }
            }
            $ticket->agents = implode(',', $agents_array);
        }


        $plants = Plant::where($where_array)->get();
        $companies = Company::where($where_com_array)->get();
        $status = TicketStatus::all();
        $priority = TicketPriority::all();
        $filter_ticket = Ticket::where($where_array)->get();
        $agents = Employee::all();
        $due_in = TicketDueIn::all();

        //return $tickets;


        return view('admin.complain.list_ticket_customer', compact('tickets', 'filter_ticket', 'plants', 'companies', 'status', 'priority', 'agents', 'due_in'));
    }

    public function add_ticket_customer()
    {
        return view('admin.complain.add_ticket_customer');
    }

    public function view_edit_ticket_customer()
    {
        return view('admin.complain.view_edit_ticket_customer');
    }

    public function userPlantDetails($id)
    {
        return $id;
    }

    public function ticketPriorityGraph(Request $request)
    {

        $request_from = $request->request_from;
        $plant_array = array();
        if ($request_from === 'ticketdashboard') {

            $filter = json_decode($request->filter, true);
            $plants_name = json_decode($request->plant_name, true);
            $filter_arr = [];
            $plant_name = [];

            foreach ($filter as $key => $flt) {

                $filter_arr[$key] = $flt;
            }

            foreach ($plants_name as $pl_name) {

                $plant_name[] = $pl_name;
            }

            if (empty($plant_name)) {

                if (Auth::user()->roles == 1 || Auth::user()->roles == 2) {
                    $plant_names = Plant::pluck('id');
                    $plant_names = $plant_names->toArray();
                } else if (Auth::user()->roles == 3 || Auth::user()->roles == 4) {
                    $plant_names = Plant::where('company_id', Auth::user()->company_id)->pluck('id');
                    $plant_names = $plant_names->toArray();
                }

                $plant_name = $plant_names;
            }

            $plant_array = Plant::where($filter_arr)->whereIn('id', $plant_name)->pluck('id')->toArray();
        } else if ($request_from === 'plant_detail') {

            $plant_array = array($request->plant_id);
        } else {

            $plant_array = Plant::pluck('id')->toArray();
        }

        $time = $request->time;
        $dates = strtotime($request->date);
        $ticket_priority_data = TicketPriority::all();

        if ($time == 'day') {

            $date = date('Y-m-d', $dates);

            $ticket_priority_graph = [];
            $legend_array = [];
            foreach ($ticket_priority_data as $key => $t_m) {

                $legend_array[] = $t_m->priority;

                $arr = array();

                $arr[] = Ticket::whereIn('plant_id', $plant_array)->where('priority', $t_m->id)->whereDate('created_at', $date)->count();

                ${"file" . $key} = collect([
                    "value" => array_sum($arr),
                    "name" => $t_m->priority,
                ]);

                $ticket_priority_graph[] = ${"file" . $key};

            }

            $data['ticket_priority_graph'] = $ticket_priority_graph;
            $data['legend_array'] = $legend_array;

            return $data;
        }

        if ($time == 'month') {

            $date = date('Y-m', $dates);

            $explode_data = explode('-', $date);
            $mon = $explode_data[1];
            $yer = $explode_data[0];

            $ticket_priority_graph = [];
            $legend_array = [];

            foreach ($ticket_priority_data as $key => $t_m) {

                $legend_array[] = $t_m->priority;

                $arr = array();

                $arr[] = Ticket::whereIn('plant_id', $plant_array)->where('priority', $t_m->id)->whereYear('created_at', $yer)->whereMonth('created_at', $mon)->count();

                ${"file" . $key} = collect([
                    "value" => array_sum($arr),
                    "name" => $t_m->priority,
                ]);

                $ticket_priority_graph[] = ${"file" . $key};

            }

            $data['ticket_priority_graph'] = $ticket_priority_graph;
            $data['legend_array'] = $legend_array;

            return $data;
        }

        if ($time == 'year') {

            $date = $request->date;

            $ticket_priority_graph = [];
            $legend_array = [];

            foreach ($ticket_priority_data as $key => $t_m) {

                $legend_array[] = $t_m->priority;

                $arr = array();

                $arr[] = Ticket::whereIn('plant_id', $plant_array)->where('priority', $t_m->id)->whereYear('created_at', $date)->count();

                ${"file" . $key} = collect([
                    "value" => array_sum($arr),
                    "name" => $t_m->priority,
                ]);

                $ticket_priority_graph[] = ${"file" . $key};

            }

            $data['ticket_priority_graph'] = $ticket_priority_graph;
            $data['legend_array'] = $legend_array;

            return $data;
        }
    }

    public function plantTicketGraph(Request $request)
    {
//        return $request->status;
        $request_from = $request->request_from;
        $plant_array = array();
        if ($request->plant_name) {

            $filter = json_decode($request->filter, true);
            $plants_name = json_decode($request->plant_name, true);
            $filter_arr = [];
            $plant_name = [];

            foreach ($filter as $key => $flt) {

                $filter_arr[$key] = $flt;
            }

            foreach ($plants_name as $pl_name) {

                $plant_name[] = $pl_name;
            }

            if (empty($plant_name)) {

                if (Auth::user()->roles == 1 || Auth::user()->roles == 2) {
                    $plant_names = Plant::pluck('id');
                    $plant_names = $plant_names->toArray();
                } else if (Auth::user()->roles == 3 || Auth::user()->roles == 4) {
                    $plant_names = Plant::where('company_id', Auth::user()->company_id)->pluck('id');
                    $plant_names = $plant_names->toArray();
                }

                $plant_name = $plant_names;
            }

            $plant_array = Plant::where($filter_arr)->whereIn('id', $plant_name)->pluck('id')->toArray();
        } else {

            $plant_array = Plant::pluck('id')->toArray();
        }
        // return $plant_array;
        $time = $request->time;
        $dates = strtotime($request->date);
        if($request->status == "min") {
            if ($time == 'day') {

                $date = date('Y-m-d', $dates);


                $ticket_plants_graph = [];
                $legend_array = [];;

                $arr = array();

                $max_ticket_plants = DB::table('tickets')->whereDate('tickets.created_at', $date)
                    ->whereIn("plant_id", $plant_array)
                    ->join('plants', 'plants.id', 'tickets.plant_id')
                    ->select('plant_id', 'plants.plant_name', DB::raw('count(*) as total'))
                    ->groupBy('plant_id')
                    ->limit('5')
                    ->orderby('total', 'asc')
                    ->get();
                //            return [$max_ticket_plants, $date];
                if (count($max_ticket_plants) > 0) {
                    foreach ($max_ticket_plants as $key => $plantsTicket) {
                        $legend_array[] = $plantsTicket->plant_name;
                        //                $plantKey = $key + 1;
                        //                $ticket_plants_graph[] = 'plant' . $plantKey;
                        //                $ticket_plants_graph[] = $plantsTicket->total;

                        ${"file" . $key} = collect([
                            "value" => $plantsTicket->total,
                            "name" => $plantsTicket->plant_name,
                        ]);
                        //
                        $ticket_plants_graph[] = ${"file" . $key};

                    }


                    $data['ticket_plants_graph'] = $ticket_plants_graph;
                    $data['legend_array'] = $legend_array;

                    return $data;
                } else {
                    return $this->defaultPlantsTickets($plant_array);
                }
            }

            if ($time == 'month') {

                $date = date('Y-m', $dates);

                $explode_data = explode('-', $date);
                $mon = $explode_data[1];
                $yer = $explode_data[0];

                $ticket_plants_graph = [];
                $legend_array = [];

                //            foreach ($ticket_priority_data as $key => $t_m) {

                //                $legend_array[] = $t_m->priority;

                $arr = array();

                $max_ticket_plants = DB::table('tickets')->whereYear('tickets.created_at', $yer)->whereMonth('tickets.created_at', $mon)
                    ->whereIn("plant_id", $plant_array)
                    ->join('plants', 'plants.id', 'tickets.plant_id')
                    ->select('plant_id', 'plants.plant_name', DB::raw('count(*) as total'))
                    ->groupBy('plant_id')
                    ->limit('5')
                    ->orderby('total', 'asc')
                    ->get();
                //            return [$max_ticket_plants, $date];
                if (count($max_ticket_plants) > 0) {
                    foreach ($max_ticket_plants as $key => $plantsTicket) {
                        $legend_array[] = $plantsTicket->plant_name;
                        //                $plantKey = $key + 1;
                        //                $ticket_plants_graph[] = 'plant' . $plantKey;
                        //                $ticket_plants_graph[] = $plantsTicket->total;

                        ${"file" . $key} = collect([
                            "value" => $plantsTicket->total,
                            "name" => $plantsTicket->plant_name,
                        ]);
                        //
                        $ticket_plants_graph[] = ${"file" . $key};

                    }

                    $data['ticket_plants_graph'] = $ticket_plants_graph;
                    $data['legend_array'] = $legend_array;
                    return $data;

                } else {
                    return $this->defaultPlantsTickets($plant_array);
                }
            }

            if ($time == 'year') {

                $date = $request->date;

                $ticket_plants_graph = [];
                $legend_array = [];
                $max_ticket_plants = DB::table('tickets')->whereYear('tickets.created_at', $date)
                    ->whereIn("plant_id", $plant_array)
                    ->join('plants', 'plants.id', 'tickets.plant_id')
                    ->select('plant_id', 'plants.plant_name', DB::raw('count(*) as total'))
                    ->groupBy('plant_id')
                    ->limit('5')
                    ->orderby('total', 'asc')
                    ->get();
                //            return [$max_ticket_plants, $date];
                if (count($max_ticket_plants) > 0) {
                    foreach ($max_ticket_plants as $key => $plantsTicket) {
                        $legend_array[] = $plantsTicket->plant_name;
                        //                $plantKey = $key + 1;
                        //                $ticket_plants_graph[] = 'plant' . $plantKey;
                        //                $ticket_plants_graph[] = $plantsTicket->total;

                        ${"file" . $key} = collect([
                            "value" => $plantsTicket->total,
                            "name" => $plantsTicket->plant_name,
                        ]);
                        //
                        $ticket_plants_graph[] = ${"file" . $key};

                    }
                    //            return [$legend_array, $ticket_plants_graph];

                    $data['ticket_plants_graph'] = $ticket_plants_graph;
                    $data['legend_array'] = $legend_array;

                    return $data;
                } else {
                    return $this->defaultPlantsTickets($plant_array);
                }
            }
        }
        else{
            if ($time == 'day') {

                $date = date('Y-m-d', $dates);


                $ticket_plants_graph = [];
                $legend_array = [];;

                $arr = array();

                $max_ticket_plants = DB::table('tickets')->whereDate('tickets.created_at', $date)
                    ->whereIn("plant_id", $plant_array)
                    ->join('plants', 'plants.id', 'tickets.plant_id')
                    ->select('plant_id', 'plants.plant_name', DB::raw('count(*) as total'))
                    ->groupBy('plant_id')
                    ->limit('5')
                    ->orderby('total', 'desc')
                    ->get();
                //            return [$max_ticket_plants, $date];
                if (count($max_ticket_plants) > 0) {
                    foreach ($max_ticket_plants as $key => $plantsTicket) {
                        $legend_array[] = $plantsTicket->plant_name;
                        //                $plantKey = $key + 1;
                        //                $ticket_plants_graph[] = 'plant' . $plantKey;
                        //                $ticket_plants_graph[] = $plantsTicket->total;

                        ${"file" . $key} = collect([
                            "value" => $plantsTicket->total,
                            "name" => $plantsTicket->plant_name,
                        ]);
                        //
                        $ticket_plants_graph[] = ${"file" . $key};

                    }


                    $data['ticket_plants_graph'] = $ticket_plants_graph;
                    $data['legend_array'] = $legend_array;

                    return $data;
                } else {
                    return $this->defaultPlantsTickets($plant_array);
                }
            }

            if ($time == 'month') {

                $date = date('Y-m', $dates);

                $explode_data = explode('-', $date);
                $mon = $explode_data[1];
                $yer = $explode_data[0];

                $ticket_plants_graph = [];
                $legend_array = [];

                //            foreach ($ticket_priority_data as $key => $t_m) {

                //                $legend_array[] = $t_m->priority;

                $arr = array();

                $max_ticket_plants = DB::table('tickets')->whereYear('tickets.created_at', $yer)->whereMonth('tickets.created_at', $mon)
                    ->whereIn("plant_id", $plant_array)
                    ->join('plants', 'plants.id', 'tickets.plant_id')
                    ->select('plant_id', 'plants.plant_name', DB::raw('count(*) as total'))
                    ->groupBy('plant_id')
                    ->limit('5')
                    ->orderby('total', 'desc')
                    ->get();
                //            return [$max_ticket_plants, $date];
                if (count($max_ticket_plants) > 0) {
                    foreach ($max_ticket_plants as $key => $plantsTicket) {
                        $legend_array[] = $plantsTicket->plant_name;
                        //                $plantKey = $key + 1;
                        //                $ticket_plants_graph[] = 'plant' . $plantKey;
                        //                $ticket_plants_graph[] = $plantsTicket->total;

                        ${"file" . $key} = collect([
                            "value" => $plantsTicket->total,
                            "name" => $plantsTicket->plant_name,
                        ]);
                        //
                        $ticket_plants_graph[] = ${"file" . $key};

                    }

                    $data['ticket_plants_graph'] = $ticket_plants_graph;
                    $data['legend_array'] = $legend_array;
                    return $data;

                } else {
                    return $this->defaultPlantsTickets($plant_array);
                }
            }

            if ($time == 'year') {

                $date = $request->date;

                $ticket_plants_graph = [];
                $legend_array = [];
                $max_ticket_plants = DB::table('tickets')->whereYear('tickets.created_at', $date)
                    ->whereIn("plant_id", $plant_array)
                    ->join('plants', 'plants.id', 'tickets.plant_id')
                    ->select('plant_id', 'plants.plant_name', DB::raw('count(*) as total'))
                    ->groupBy('plant_id')
                    ->limit('5')
                    ->orderby('total', 'desc')
                    ->get();
                //            return [$max_ticket_plants, $date];
                if (count($max_ticket_plants) > 0) {
                    foreach ($max_ticket_plants as $key => $plantsTicket) {
                        $legend_array[] = $plantsTicket->plant_name;
                        //                $plantKey = $key + 1;
                        //                $ticket_plants_graph[] = 'plant' . $plantKey;
                        //                $ticket_plants_graph[] = $plantsTicket->total;

                        ${"file" . $key} = collect([
                            "value" => $plantsTicket->total,
                            "name" => $plantsTicket->plant_name,
                        ]);
                        //
                        $ticket_plants_graph[] = ${"file" . $key};

                    }
                    //            return [$legend_array, $ticket_plants_graph];

                    $data['ticket_plants_graph'] = $ticket_plants_graph;
                    $data['legend_array'] = $legend_array;

                    return $data;
                } else {
                    return $this->defaultPlantsTickets($plant_array);
                }
            }
        }
    }

    public function defaultPlantsTickets($plant_array){

        $ticket_plants_graph = [];
        $legend_array = [];
        ${"file"} = collect([
            "value" => 0,
            "name" => "No Ticket",
        ]);
        $legend_array[] ="No Ticket";
        $ticket_plants_graph[] = ${"file"};

        $data['ticket_plants_graph'] = $ticket_plants_graph;
        $data['legend_array'] = $legend_array;


        return $data;

    }
    public function ticketStatusGraph(Request $request)
    {

        $request_from = $request->request_from;
        $plant_array = array();
        if ($request->plant_name) {

            $filter = json_decode($request->filter, true);
            $plants_name = json_decode($request->plant_name, true);
            $filter_arr = [];
            $plant_name = [];

            foreach ($filter as $key => $flt) {

                $filter_arr[$key] = $flt;
            }

            foreach ($plants_name as $pl_name) {

                $plant_name[] = $pl_name;
            }

            if (empty($plant_name)) {

                if (Auth::user()->roles == 1 || Auth::user()->roles == 2) {
                    $plant_names = Plant::pluck('id');
                    $plant_names = $plant_names->toArray();
                } else if (Auth::user()->roles == 3 || Auth::user()->roles == 4) {
                    $plant_names = Plant::where('company_id', Auth::user()->company_id)->pluck('id');
                    $plant_names = $plant_names->toArray();
                }

                $plant_name = $plant_names;
            }

            $plant_array = Plant::where($filter_arr)->whereIn('id', $plant_name)->pluck('id')->toArray();
        } else {

            $plant_array = Plant::pluck('id')->toArray();
        }

        $time = $request->time;
        $dates = strtotime($request->date);
        $ticket_status_data = TicketStatus::all();

        if ($time == 'day') {

            $date = date('Y-m-d', $dates);

            $ticket_status_graph = [];
            $legend_array = [];

            foreach ($ticket_status_data as $key => $t_m) {

                $legend_array[] = $t_m->status;

                $arr = array();

                $arr[] = Ticket::whereIn("plant_id", $plant_array)->where('status', $t_m->id)->whereDate('created_at', $date)->count();

                ${"file" . $key} = collect([
                    "value" => array_sum($arr),
                    "name" => $t_m->status,
                ]);

                $ticket_status_graph[] = ${"file" . $key};

            }

            $data['ticket_status_graph'] = $ticket_status_graph;
            $data['legend_array'] = $legend_array;

            return $data;
        }

        if ($time == 'month') {

            $date = date('Y-m', $dates);

            $explode_data = explode('-', $date);
            $mon = $explode_data[1];
            $yer = $explode_data[0];

            $ticket_status_graph = [];
            $legend_array = [];

            foreach ($ticket_status_data as $key => $t_m) {

                $legend_array[] = $t_m->status;

                $arr = array();

                $arr[] = Ticket::whereIn("plant_id", $plant_array)->where('status', $t_m->id)->whereYear('created_at', $yer)->whereMonth('created_at', $mon)->count();

                ${"file" . $key} = collect([
                    "value" => array_sum($arr),
                    "name" => $t_m->status,
                ]);

                $ticket_status_graph[] = ${"file" . $key};

            }

            $data['ticket_status_graph'] = $ticket_status_graph;
            $data['legend_array'] = $legend_array;

            return $data;
        }

        if ($time == 'year') {

            $date = $request->date;

            $ticket_status_graph = [];
            $legend_array = [];

            foreach ($ticket_status_data as $key => $t_m) {

                $legend_array[] = $t_m->status;

                $arr = array();

                $arr[] = Ticket::whereIn("plant_id", $plant_array)->where('status', $t_m->id)->whereYear('created_at', $date)->count();

                ${"file" . $key} = collect([
                    "value" => array_sum($arr),
                    "name" => $t_m->status,
                ]);

                $ticket_status_graph[] = ${"file" . $key};

            }

            $data['ticket_status_graph'] = $ticket_status_graph;
            $data['legend_array'] = $legend_array;

            return $data;
        }
    }

    public function ticketMediumGraph(Request $request)
    {

        $request_from = $request->request_from;
        $plant_array = array();
        // return $request->all();
        if ($request->plant_name) {

            $filter = json_decode($request->filter, true);
            $plants_name = json_decode($request->plant_name, true);
            $filter_arr = [];
            $plant_name = [];

            foreach ($filter as $key => $flt) {

                $filter_arr[$key] = $flt;
            }

            foreach ($plants_name as $pl_name) {

                $plant_name[] = $pl_name;
            }

            if (empty($plant_name)) {

                if (Auth::user()->roles == 1 || Auth::user()->roles == 2) {
                    $plant_names = Plant::pluck('id');
                    $plant_names = $plant_names->toArray();
                } else if (Auth::user()->roles == 3 || Auth::user()->roles == 4) {
                    $plant_names = Plant::where('company_id', Auth::user()->company_id)->pluck('id');
                    $plant_names = $plant_names->toArray();
                }

                $plant_name = $plant_names;
            }

            $plant_array = Plant::where($filter_arr)->whereIn('id', $plant_name)->pluck('id')->toArray();
        } else {

            $plant_array = Plant::pluck('id')->toArray();
        }
        $time = $request->time;
        $dates = strtotime($request->date);
        $ticket_medium_data = TicketSource::all();

        if ($time == 'month') {

            $date = date('Y-m', $dates);

            $explode_data = explode('-', $date);
            $mon = $explode_data[1];
            $yer = $explode_data[0];

            $dd = cal_days_in_month(CAL_GREGORIAN, $mon, $yer);

            $ticket_medium_graph = [];
            $month_array = [];
            $legend_array = [];

            foreach ($ticket_medium_data as $key => $t_m) {

                $legend_array[] = $t_m->name;

                $arr = array();

                for ($i = 1; $i <= $dd; $i++) {

                    if ($i < 10) {
                        $i = '0' . $i;
                    }

                    $arr[] = Ticket::whereIn("plant_id", $plant_array)->where('source', $t_m->id)->whereDate('created_at', $date . '-' . $i)->count();
                }

                ${"file" . $key} = collect([
                    "name" => $t_m->name,
                    "type" => 'line',
                    "showSymbol" => false,
                    "smooth" => true,
                    "data" => $arr,
                ]);

                $ticket_medium_graph[] = ${"file" . $key};

            }

            for ($i = 1; $i <= $dd; $i++) {
                $month_array[] = $i;
            }

            $data['ticket_medium_graph'] = $ticket_medium_graph;
            $data['month_array'] = $month_array;
            $data['legend_array'] = $legend_array;

            return $data;
        }

        if ($time == 'year') {

            $date = $request->date;

            $ticket_medium_graph = [];
            $month_array = [];
            $legend_array = [];

            foreach ($ticket_medium_data as $key => $t_m) {

                $legend_array[] = $t_m->name;

                $arr = array();

                for ($i = 1; $i <= 12; $i++) {

                    if ($i < 10) {
                        $i = '0' . $i;
                    }

                    $arr[] = Ticket::whereIn("plant_id", $plant_array)->where('source', $t_m->id)->whereYear('created_at', $date)->whereMonth('created_at', $i)->count();
                }

                ${"file" . $key} = collect([
                    "name" => $t_m->name,
                    "type" => 'line',
                    "showSymbol" => false,
                    "smooth" => true,
                    "data" => $arr,
                ]);

                $ticket_medium_graph[] = ${"file" . $key};

            }

            for ($i = 1; $i <= 12; $i++) {
                $month_array[] = substr(date('F', mktime(0, 0, 0, $i, 10)), 0, 3);
            }

            $data['ticket_medium_graph'] = $ticket_medium_graph;
            $data['month_array'] = $month_array;
            $data['legend_array'] = $legend_array;

            return $data;
        }
    }

    public function ticketApproachGraph(Request $request)
    {
        $request_from = $request->request_from;
        $plant_array = array();
        if ($request->plant_name) {

            $filter = json_decode($request->filter, true);
            $plants_name = json_decode($request->plant_name, true);
            $filter_arr = [];
            $plant_name = [];

            foreach ($filter as $key => $flt) {

                $filter_arr[$key] = $flt;
            }

            foreach ($plants_name as $pl_name) {

                $plant_name[] = $pl_name;
            }

            if (empty($plant_name)) {

                if (Auth::user()->roles == 1 || Auth::user()->roles == 2) {
                    $plant_names = Plant::pluck('id');
                    $plant_names = $plant_names->toArray();
                } else if (Auth::user()->roles == 3 || Auth::user()->roles == 4) {
                    $plant_names = Plant::where('company_id', Auth::user()->company_id)->pluck('id');
                    $plant_names = $plant_names->toArray();
                }

                $plant_name = $plant_names;
            }

            $plant_array = Plant::where($filter_arr)->whereIn('id', $plant_name)->pluck('id')->toArray();
        }
        // else if($request_from === 'plant_detail') {

        //     $plant_array = array($request->plant_id);
        // }

        else {

            $plant_array = Plant::pluck('id')->toArray();
        }


        $time = $request->time;
        $dates = strtotime($request->date);

        if ($time == 'day') {
            $date = date('Y-m-d', $dates);
            $pre_date = date('Y-m-d', strtotime(("-1 days"), $dates));
        } else if ($time == 'month') {
            $date = date('Y-m', $dates);
            $pre_date = date('Y-m', strtotime(("-1 months"), $dates));
        } else if ($time == 'year') {
            $date = $request->date;
            $pre_date = date('Y', strtotime(("-1 years"), $dates));
        }

        $today_log_time = [];
        $unique_time_arr = [];
        $total_log_data_arr = [];
        $approach_log_data_arr = [];
        $past_log_data_arr = [];

        $all_tickets = Ticket::where('status', '!=', 6)->whereIn('plant_id', $plant_array)->get(['created_at']);
        // return $all_tickets;

        if ($time == 'day') {

            $unique_time_arr = ['00:00', '03:00', '06:00', '09:00', '12:00', '15:00', '18:00', '21:00', '23:59'];

            $unique_time_arr_display = ['00:00-03:00', '03:00-06:00', '06:00-09:00', '09:00-12:00', '12:00-15:00', '15:00-18:00', '18:00-21:00', '21:00-23:59'];

            if ($unique_time_arr) {

                for ($i = 0; $i < (count($unique_time_arr) - 1); $i++) {

                    $total_ticket = Ticket::whereBetween('created_at', [$date . ' ' . $unique_time_arr[$i] . ':00', $date . ' ' . $unique_time_arr[$i + 1] . ':00'])->whereIn('plant_id', $plant_array)->count();

                    $approach_ticket_closed = Ticket::where('status', 6)->where('closed_approach_status', 1)->whereBetween('created_at', [$date . ' ' . $unique_time_arr[$i] . ':00', $date . ' ' . $unique_time_arr[$i + 1] . ':00'])->count();

                    $approach_ticket_not_closed = Ticket::where('status', '!=', 6)->whereRaw("closed_time > CURRENT_TIMESTAMP()")->whereBetween('created_at', [$date . ' ' . $unique_time_arr[$i] . ':00', $date . ' ' . $unique_time_arr[$i + 1] . ':00'])->count();

                    $past_ticket_closed = Ticket::where('status', 6)->where('closed_approach_status', 0)->whereBetween('created_at', [$date . ' ' . $unique_time_arr[$i] . ':00', $date . ' ' . $unique_time_arr[$i + 1] . ':00'])->count();

                    $past_ticket_not_closed = Ticket::where('status', '!=', 6)->whereRaw("closed_time < CURRENT_TIMESTAMP()")->whereBetween('created_at', [$date . ' ' . $unique_time_arr[$i] . ':00', $date . ' ' . $unique_time_arr[$i + 1] . ':00'])->count();

                    $total_log_data_arr[] = (int)$total_ticket;
                    $approach_log_data_arr[] = (int)$approach_ticket_closed + (int)$approach_ticket_not_closed;
                    $past_log_data_arr[] = (int)$past_ticket_closed + (int)$past_ticket_not_closed;

                }
            }
        } else if ($time == 'month') {

            $explode_data = explode('-', $date);
            $mon = $explode_data[1];
            $yer = $explode_data[0];

            $dd = cal_days_in_month(CAL_GREGORIAN, $mon, $yer);
            for ($i = 1; $i <= $dd; $i++) {

                if ($i < 10) {
                    $i = '0' . $i;
                }

                $total_ticket = Ticket::where('created_at', 'LIKE', $date . '-' . $i . '%')->whereIn('plant_id', $plant_array)->count();

                $approach_ticket_closed = Ticket::where('status', 6)->where('closed_approach_status', 1)->whereIn('plant_id', $plant_array)->where('created_at', 'LIKE', $date . '-' . $i . '%')->count();

                $approach_ticket_not_closed = Ticket::where('status', '!=', 6)->whereIn('plant_id', $plant_array)->whereRaw("closed_time > CURRENT_TIMESTAMP()")->where('created_at', 'LIKE', $date . '-' . $i . '%')->count();

                $past_ticket_closed = Ticket::where('status', 6)->whereIn('plant_id', $plant_array)->where('closed_approach_status', 0)->where('created_at', 'LIKE', $date . '-' . $i . '%')->count();

                $past_ticket_not_closed = Ticket::where('status', '!=', 6)->whereIn('plant_id', $plant_array)->whereRaw("closed_time < CURRENT_TIMESTAMP()")->where('created_at', 'LIKE', $date . '-' . $i . '%')->count();

                $total_log_data_arr[] = (object)[
                    "y" => (int)$total_ticket,
                    "x" => (int)$i,
                ];

                $approach_log_data_arr[] = (object)[
                    "y" => (int)$approach_ticket_closed + (int)$approach_ticket_not_closed,
                    "x" => (int)$i,
                ];

                $past_log_data_arr[] = (object)[
                    "y" => (int)$past_ticket_closed + (int)$past_ticket_not_closed,
                    "x" => (int)$i,
                ];
            }
        } else if ($time == 'year') {

            for ($i = 1; $i <= 12; $i++) {

                if ($i < 10) {
                    $i = '0' . $i;
                }

                $total_ticket = Ticket::whereIn('plant_id', $plant_array)->whereYear('created_at', $date)->whereMonth('created_at', $i)->count();

                $approach_ticket_closed = Ticket::where('status', 6)->whereIn('plant_id', $plant_array)->where('closed_approach_status', 1)->whereYear('created_at', $date)->whereMonth('created_at', $i)->count();

                $approach_ticket_not_closed = Ticket::where('status', '!=', 6)->whereIn('plant_id', $plant_array)->whereRaw("closed_time > CURRENT_TIMESTAMP()")->whereYear('created_at', $date)->whereMonth('created_at', $i)->count();

                $past_ticket_closed = Ticket::where('status', 6)->whereIn('plant_id', $plant_array)->where('closed_approach_status', 0)->whereYear('created_at', $date)->whereMonth('created_at', $i)->count();

                $past_ticket_not_closed = Ticket::where('status', '!=', 6)->whereIn('plant_id', $plant_array)->whereRaw("closed_time < CURRENT_TIMESTAMP()")->whereYear('created_at', $date)->whereMonth('created_at', $i)->count();

                $total_log_data_arr[] = (object)[
                    "y" => (int)$total_ticket,
                    "label" => substr(date('F', mktime(0, 0, 0, $i, 10)), 0, 3),
                    "tooltip" => date('F', mktime(0, 0, 0, $i, 10)),
                ];

                $approach_log_data_arr[] = (object)[
                    "y" => (int)$approach_ticket_closed + (int)$approach_ticket_not_closed,
                    "label" => substr(date('F', mktime(0, 0, 0, $i, 10)), 0, 3),
                    "tooltip" => date('F', mktime(0, 0, 0, $i, 10)),
                ];

                $past_log_data_arr[] = (object)[
                    "y" => (int)$past_ticket_closed + (int)$past_ticket_not_closed,
                    "label" => substr(date('F', mktime(0, 0, 0, $i, 10)), 0, 3),
                    "tooltip" => date('F', mktime(0, 0, 0, $i, 10)),
                ];

            }
        }

        if ($time == 'day') {

            $ticket_log['today_time'] = isset($unique_time_arr_display) && !empty($unique_time_arr_display) ? implode(',', $unique_time_arr_display) : '';
            $ticket_log['ticket_total'] = isset($total_log_data_arr) && !empty($total_log_data_arr) ? implode(',', $total_log_data_arr) : '';
            $ticket_log['ticket_approach'] = isset($approach_log_data_arr) && !empty($approach_log_data_arr) ? implode(',', $approach_log_data_arr) : '';
            $ticket_log['ticket_past'] = isset($past_log_data_arr) && !empty($past_log_data_arr) ? implode(',', $past_log_data_arr) : '';
            $ticket_log['max_total'] = isset($total_log_data_arr) && !empty($total_log_data_arr) ? max($total_log_data_arr) : 0;
            $ticket_log['tot_ticket'] = isset($total_log_data_arr) && !empty($total_log_data_arr) ? array_sum($total_log_data_arr) : 0;
            $ticket_log['tot_approach'] = isset($approach_log_data_arr) && !empty($approach_log_data_arr) ? array_sum($approach_log_data_arr) : 0;
            $ticket_log['tot_past'] = isset($past_log_data_arr) && !empty($past_log_data_arr) ? array_sum($past_log_data_arr) : 0;

            return $ticket_log;
        } else if ($time == 'month' || $time == 'year') {

            $ticket_log['tot_ticket'] = array_sum(array_column($total_log_data_arr, 'y'));
            $ticket_log['tot_approach'] = array_sum(array_column($approach_log_data_arr, 'y'));
            $ticket_log['tot_past'] = array_sum(array_column($past_log_data_arr, 'y'));
            $ticket_log['total_log_data'] = $total_log_data_arr;
            $ticket_log['approach_log_data'] = $approach_log_data_arr;
            $ticket_log['past_log_data'] = $past_log_data_arr;

            return $ticket_log;
        }
    }

    public function updateTicketFeedback(Request $request)
    {

        foreach ($request->all() as $key => $value) {

            if ($key !== '_token') {

                if ($value == 'N') {

                    $ticket = Ticket::where('id', $key)->update(['status' => 2]);

                    $ticket_history = new TicketHistory();

                    $ticket_history->ticket_id = $key;
                    $ticket_history->user_id = $request->user()->id;
                    $ticket_history->description = 'Ticket re-opened by user';

                    $ticket_history->save();

                    $ticket_attachment = TicketAttachment::create(['ticket_description_id' => $ticket_history->id]);
                } else if ($value == 'Y') {

                    $ticket = Ticket::where('id', $key)->update(['user_approved' => 'Y']);
                }
            }
        }

        return redirect()->back()->with('success', 'Your response saved successfully!');
    }

    public function updateTicketFeedbackAjax(Request $request)
    {

        try {

            if ($request->ticketFeedbackStatus == 'N') {

                $ticket = Ticket::where('id', $request->ticketID)->update(['status' => 2]);

                $ticket_history = new TicketHistory();

                $ticket_history->ticket_id = $request->ticketID;
                $ticket_history->user_id = $request->user()->id;
                $ticket_history->description = 'Ticket re-opened';

                $ticket_history->save();

                $ticket_attachment = TicketAttachment::create(['ticket_description_id' => $ticket_history->id]);
            } else if ($request->ticketFeedbackStatus == 'Y') {

                $ticket = Ticket::where('id', $request->ticketID)->update(['user_approved' => 'Y']);
            }

            return response()->json(['status' => 1, 'message' => 'Status updated successfully!']);
        } catch (Exception $e) {

            return response()->json(['status' => 0, 'message' => $e->getMessage()]);
        }
    }

    public function reOpenTicket($id)
    {

        if (Auth::user()->roles == 5 || Auth::user()->roles == 6) {

            return redirect()->back()->with('error', 'You have no permission to re-open the ticket!');
        }

        $ticket = Ticket::findOrFail($id);
        $ticket->status = 2;
        $ticket->updated_by = Auth::user()->id;
        $ticket->updated_at = date('Y-m-d H:i:s');
        $ticket->closed_approach_status = null;

        $ticket->save();

        $history['ticket_id'] = $id;
        $history['user_id'] = Auth::user()->id;
        $history['description'] = 'Ticket re-opened';
        $history['is_default'] = 0;
        $history['history_changes'] = null;
        $history_responce = TicketHistory::create($history);

        $plant_id = $ticket->plant_id;
        if ($plant_id) {

            $plant_user = PlantUser::where('plant_id', $plant_id)->get();
//            $ticket = Ticket::findOrFail($id);
            if ($plant_user) {

                foreach ($plant_user as $key => $usr) {
//                        $ticketss = Ticket::findOrFail($id);
                    $noti_app = new Notification();
                    $noti_app->user_id = $usr->user_id;
                    $noti_app->plant_id = $usr->plant_id;
                    $noti_app->ticket_id = $ticket->id;
                    $noti_app->description = "Your ticket # " . $ticket->id . " marked for re-opened";
                    $noti_app->title = "Ticket closed - Ticket # " . $ticket->id;
                    $noti_app->notification_type = 'Ticket';
                    $noti_app->schedule_date = date('Y-m-d H:i:s');
                    $noti_app->sent_status = 'N';
                    $noti_app->is_msg_app = 'Y';
                    $noti_app->is_notification_required = 'Y';

                    $noti_app->save();
                }
            }
        }
        $notification_controller = new CommunicationController();
        $notification_controller->send_comm_app_notification();
        return redirect()->route('admin.ticket.list');
    }

}
