<?php

namespace App\Http\Controllers;

use App\Applicant;
use App\AttendanceSummary;
use App\Branch;
use App\Designation;
use App\Employee;
use App\Job;
use App\Leave;
use App\Mail\FeedbackMail;
use App\Mail\Reminder;
use App\Traits\MetaTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mail;
use Session;

class DashboardController extends Controller
{
    use MetaTrait;

    public function index()
    {
        $hubs = \App\Hub::active()->orderBy('name')->get();

        $items = \App\Item::with('stocks')->get();
        $lowStockItems = 0;
        $unitsOnHand = 0;
        $stockValue = 0;

        foreach ($items as $item) {
            $quantity = (int) $item->stocks->sum('quantity');
            $unitsOnHand += $quantity;
            $stockValue += $quantity * (float) $item->unit_cost;
            if ($quantity <= (int) $item->reorder_level) {
                $lowStockItems++;
            }
        }

        $hubStock = $hubs->map(function ($hub) {
            return [
                'label' => $hub->name,
                'quantity' => (int) \App\InventoryStock::where('hub_id', $hub->id)->sum('quantity'),
            ];
        });

        $orderStatuses = collect(\App\Order::$statuses)->map(function ($label, $status) {
            return [
                'label' => $label,
                'total' => \App\Order::where('status', $status)->count(),
            ];
        })->values();

        $stats = [
            'total_employees' => \App\Employee::count(),
            'active_employees' => \App\Employee::where('is_active', true)->count(),
            'units_on_hand' => $unitsOnHand,
            'stock_value' => $stockValue,
            'low_stock' => $lowStockItems,
            'pending_pos' => \App\PurchaseOrder::where('status', \App\PurchaseOrder::STATUS_PENDING)->count(),
            'open_orders' => \App\Order::whereIn('status', [\App\Order::STATUS_PENDING_HANDOFF, \App\Order::STATUS_IN_TRANSIT])->count(),
            'overdue_loans' => \App\EquipmentLoan::overdue()->count(),
            'open_maintenance' => \App\MaintenanceLog::outstanding()->count(),
            'in_transit_transfers' => \App\StockTransfer::where('status', \App\StockTransfer::STATUS_IN_TRANSIT)->count(),
        ];

        $recentOrders = \App\Order::with(['client', 'hub'])->latest()->take(5)->get();
        $dueMaintenance = \App\Item::dueForService(14)->orderBy('next_service_at')->take(5)->get();
        $recentActivity = \App\AccessAuditLog::with(['actor', 'employee'])->latest()->take(8)->get();
        $recentMovements = \App\StockMovement::with(['item', 'hub'])->latest()->take(6)->get();

        $this->meta['title'] = 'Operations Dashboard';

        return view('admin.dashboard.index', $this->metaResponse())->with([
            'stats' => $stats,
            'hubs' => $hubs,
            'hubStock' => $hubStock,
            'orderStatuses' => $orderStatuses,
            'recentOrders' => $recentOrders,
            'dueMaintenance' => $dueMaintenance,
            'recentActivity' => $recentActivity,
            'recentMovements' => $recentMovements,
        ]);
    }

    //    Help
    public function contact()
    {
        return view('help.contact_us', $this->metaResponse());
    }

    /**
     * @return $this
     */
    public function create()
    {
        return view('applicant.create')->with('jobs', Job::all());
    }

    /**
     * @param $id
     * @return $this
     */
    public function singleApplicant($id)
    {
        $applicant = Applicant::find($id);

        return view('admin.applicants.single')->with('applicant', $applicant);
    }

    public function findjob(Request $request)
    {
        $data = Job::select('title', 'id')->where('category_id', $request->id)->take(20)->get();

        return response()->json($data);
    }

    public function store(Request $request)
    {
        // dd($request);
        $this->validate($request, [
            'name'       => 'required',
            'fname'      => 'required',
            'avatar'     => 'required|image',
            'city'       => 'required',
            'cv'         => 'required|mimes:doc,docx,pdf,txt',
            'job_status' => 'required',
        ]);

        $avatar = $request->avatar;
        $avatar_new_name = time().$avatar->getClientOriginalName();
        $avatar->move('uploads/applicants/image', $avatar_new_name);

        $cv = $request->cv;
        $cv_new_name = time().$cv->getClientOriginalName();
        $cv->move('uploads/applicants/cv', $cv_new_name);

        $applicant = Applicant::create([
            'name'       => $request->name,
            'fname'      => $request->fname,
            'email'      => $request->email,
            'avatar'     => 'uploads/applicants/image/'.$avatar_new_name,
            'cv'         => 'uploads/applicants/cv/'.$cv_new_name,
            'city'       => $request->city,
            'job_status' => $request->job_status,
            'job_id'     => $request->job_id,
            // 'job_position_id'=>$request->job_position_id,
            'recruited' => 0,
        ]);

        /*Mail::to($request->email)->send(new Reminder);*/
        Session::flash('success', 'application is submitted succesfully');

        return redirect()->back();
    }

    public function single_Cat_Job($id)
    {
        $applicants = \DB::table('Applicants')->where('category_id', $id)->get();

        return view('admin.applicants.singleCategoryJobs')->with('applicants', $applicants);
    }

    // public function singleApplicant($id)
    // {
    //     return view('admin.applicants.singleapplicant')->with('applicants',Applicant::find($id));
    // }

    public function destroy($id)
    {
        $applicant = Applicant::find($id);
        $applicant->delete();
        Session::flash('sucess', 'Applicant deleted successfully.');

        return redirect()->back();
    }

    public function trashed()
    {
        $this->meta['title'] = 'Trash Applicants';
        $applicants = Applicant::onlyTrashed()->get();

        return view('admin.applicants.trashed', $this->metaResponse())->with('applicants', $applicants);
    }

    public function kill($id)
    {
        $applicant = Applicant::withTrashed()->where('id', $id)->first();
        $applicant->forceDelete();

        Session::flash('success', 'Applicant Deleted permanently.');

        return redirect()->back();
    }

    public function restore($id)
    {
        $applicant = Applicant::withTrashed()->where('id', $id)->first();
        $applicant->restore();
        Session::flash('success', 'Successfully Restored the applicant');

        return redirect()->back();
    }

    public function hire($id)
    {
        $applicant = Applicant::find($id);
        $applicant->recruited = 1;
        $applicant->save();

        return redirect()->back();
    }

    public function retire($id)
    {
        $applicant = Applicant::find($id);
        $applicant->recruited = 0;
        $applicant->save();

        return redirect()->back();
    }

    public function hiredApplicants()
    {
        $this->meta['title'] = 'Hired Applicants';
        $applicants = Applicant::where('recruited', 1)->take(10)->get();

        return view('admin.applicants.hiredApplicants', $this->metaResponse())->with('applicants', $applicants);
    }

    public function contact_us(Request $request)
    {
        $data = ['name' => "$request->name", 'messages' => "$request->message", 'email' => "$request->email"];

        try {
            Mail::to($request->email)->send(new FeedbackMail($request->name, $request->message, Auth::user()->official_email));
            Session::flash('success', 'Email Sent To the HR');
        } catch (\Exception $e) {
            Session::flash('error', 'Email Not Send Please Set Email Configuration In .env File');
        }

        return redirect()->back();
    }
}
