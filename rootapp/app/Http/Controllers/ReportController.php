<?php

namespace App\Http\Controllers;

use App\Services\ReportManager;

use App\Villa;
use Carbon\Carbon;
use Http\Datasource\ActiveVillaDataSource;
use Illuminate\Http\Request;

use PDF;

class ReportController extends Controller
{
    public function index() {

        $reportList = ReportManager::getReportList();
        $params = ReportManager::getParameter();

        //return compact('reportList','lookups'); 
        return view('reports.index',compact('reportList','params'));
    }

    public function apiLookups($reportId) {
        $report = ReportManager::get($reportId,[]);
        return $report->getLookups();
    }

    public function show($reportId,Request $request) {

        
        $inputs = $request->all();

        $report = ReportManager::get($reportId,$inputs);
        $datasource = $report->execute();
        $template = $report->getTemplateSource();


        if($report->isApi()) {
            return $datasource;
        }
        else if($report->isPdfRender()) {
            
            PDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif']);
            $dompdf = PDF::loadView('reports.modules.'.$template, compact('datasource'));

            return $dompdf->stream();
        }
        else {
            return view('reports.modules.'.$template, compact('datasource'));
        }
    }

    public function apiShow($reportId,Request $request) {
        $inputs = $request->all();
        $report = ReportManager::get($reportId,$inputs);
        $datasource = $report->execute();
        $template = $report->getTemplateSource();


        return $datasource;
    }

    

}
