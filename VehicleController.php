<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App;

class VehicleController extends Controller
{
    public function getVehicleExpenses(Request $request)
    {
        // Get initial POST params
        $filter = [
            'search_name' => $request->input('search_name', false),
            'expense_type' => [],
            'min_cost' => $request->input('min_cost', false),
            'max_cost' => $request->input('max_cost', false),
            'min_creation_date' => $request->input('min_creation_date', false),
            'max_creation_date' => $request->input('max_creation_date', false)
        ];
        // Filter expense Type
        if ($request->input('filter_fuel', false) != false && $request->input('filter_fuel', false) != "false")
            $filter['expense_type'][] = 'fuel';
        if ($request->input('filter_insurance', false) != false && $request->input('filter_insurance', false) != "false")
            $filter['expense_type'][] = 'insurance';
        if ($request->input('filter_service', false) != false && $request->input('filter_service', false) != "false")
            $filter['expense_type'][] = 'service';
        // Unset false and empty filters
        foreach($filter as $key => $value)
            if ($value === false || $value === 'false' || $value === '')
                unset($filter[$key]);

        // Call the report function
        $VehicleReports = new App\VehicleReports();
        return $VehicleReports->getVehicleExpenses($filter, 
                                                $request->input('orderby', 'id'), 
                                                $request->input('sorting', 'ASC'));
    }
}