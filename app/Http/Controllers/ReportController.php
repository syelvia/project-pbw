<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
//use PDF;
//use Excel;
use App\Models\Room;
use App\Models\Transaction;
use App\Models\Worker;
use App\Models\Job;
use App\Exports\ReportExport;
use App\Models\MonthlyPresence;
use App\Models\MinPresence;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function generateReport(Request $request)
    {
        $floor = $request->input('floor');
        $type = $request->input('type');
        $month = $request->input('month');
        $jobName = $request->input('job_name');

        // Detailed Income Report Data
        $incomeData = Transaction::with('room')
            ->whereHas('room', function ($query) use ($floor, $type, $month) {
                if ($floor) {
                    $query->where('name_room', 'like', $floor . '%');
                }
                if ($type) {
                    $query->where('type_room', $type);
                }
                if ($month) {
                    $query->whereMonth('checkOut_date', $month);
                }
            })
            ->get(['name_room', 'checkIn_date', 'checkOut_date', 'payment']);

        $income = $incomeData->sum('payment');

        // Detailed Payroll Report Data
        $payrollData = Worker::with('job')
            ->whereHas('job', function ($query) use ($jobName) {
                if ($jobName) {
                    $query->where('name_job', $jobName);
                }
            })
            ->get(['name_worker', 'id_job']);

        $payroll = 0;

        foreach ($payrollData as $worker) {
            $presenceCount = MonthlyPresence::where('id_worker', $worker->id)
                ->where('no_month', $month)
                ->where('status_pres', true)
                ->count();

            $minPresence = MinPresence::where('id_worker', $worker->id)
                ->where('no_month', $month)
                ->first();

            $daysInMonth = Carbon::createFromDate(null, $month, 1)->daysInMonth;

            if ($minPresence && $presenceCount < $minPresence->min_pres) {
                $wagePercentage = $presenceCount / $daysInMonth;
                $adjustedWage = $worker->job->wage_job * $wagePercentage;
            } else {
                $adjustedWage = $worker->job->wage_job;
            }

            $payroll += $adjustedWage;
        }

        // Profit
        $profit = $income - $payroll;

        $data = [
            'income' => $income,
            'incomeData' => $incomeData,
            'payroll' => $payroll,
            'payrollData' => $payrollData,
            'profit' => $profit,
        ];

        // Return PDF
        if ($request->input('format') == 'pdf') {
            $pdf = PDF::loadView('reports.report', $data);
            return $pdf->download('report.pdf');
        }

        // Return XML
        //if ($request->input('format') == 'xml') {
        //    return response()->xml($data);
        //}
//
        //// Return Excel
        //if ($request->input('format') == 'excel') {
        //    return Excel::download(new ReportExport($data), 'report.xlsx');
        //}

        return view('reports.report', $data);
        //return response()->json($data);
    }
}