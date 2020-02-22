<?php
namespace App;

use Illuminate\Support\Facades\DB;

class VehicleReports
{
	function getVehicleExpenses($filter = [], $orderby = 'id', $sorting = 'ASC') 
	{
		// Building Query
		$sql = "SELECT vehi.id, 
					vehi.name, 
					vehi.plate_number, 
					cost.type,";
		// Applying Max and Min filters
		if (isset($filter['min_cost']))
			$sql .= " MIN(cost.cost) AS cost,";
		elseif (isset($filter['max_cost']))
			$sql .= " MAX(cost.cost) AS cost,";
		else
			$sql .= " cost.cost,";
		if (isset($filter['min_creation_date']))
			$sql .= " MIN(cost.created_at) AS created_at";
		elseif (isset($filter['max_creation_date']))
			$sql .= " MAX(cost.created_at) AS created_at";
		else
			$sql .= " cost.created_at";
		$sql .= " FROM (
					SELECT id, 
						name, 
						plate_number
					FROM vehicles ";
		// Search by name
		if (!empty($filter))
			if (isset($filter['search_name']))
				$sql .= " WHERE name LIKE :search_name ";
		$sql .= " ) vehi JOIN ( ";
		// Preparing subqueries seperately to optimize the Final Query based on filters
		$expense['fuel'] = " SELECT vehicle_id, 
								'fuel' AS type, 
								cost, 
								entry_date AS created_at
							FROM fuel_entries ";
		$expense['insurance'] = " SELECT vehicle_id, 
									'insurance' AS type, 
									amount AS cost, 
									contract_date AS created_at
								FROM insurance_payments ";
		$expense['service'] = " SELECT vehicle_id, 
									'service' AS type, 
									total AS cost, 
									created_at
								FROM services ";
		// Unset subqueries from the $expense array based on the $filter['expense_type']
		if (!empty($filter) && isset($filter['expense_type']))
			foreach ($expense as $type => $subquery)
				if (!in_array($type, $filter['expense_type']))
					unset($expense[$type]);
		// Implode the $expense array with a UNION into the Query.
		$sql .= implode(' UNION ', $expense) 
			." ) cost ON cost.vehicle_id = vehi.id ";
		// Managing the GOUP BY Based on the used filters
		if (!empty($filter)) {
			if (isset($filter['min_cost']) 
				|| isset($filter['max_cost']) 
				|| isset($filter['min_creation_date']) 
				|| isset($filter['max_creation_date'])) {
				$sql .= " GROUP BY id, name, plate_number, type ";
				if (!isset($filter['min_cost']) && !isset($filter['max_cost']))
					$sql .= ", cost";
				if (!isset($filter['min_creation_date']) && !isset($filter['max_creation_date']))
					$sql .= ", created_at";
			}
		}
		// Secure ORDER BY inputs
		if (in_array(strtolower($orderby), ['id', 'name', 'plate_number', 'cost', 'created_at']) 
			&& in_array(strtolower($sorting), ['asc', 'desc']))
			$sql .= " ORDER BY ".$orderby." ".$sorting;

		// Parameterized inputs to the Query to avoid SQL injection
		$params = [];
		if (isset($filter['search_name']))
			$params['search_name'] = '%'.$filter['search_name'].'%';
		// Run Query		
		$result = DB::select(DB::raw($sql), $params);
        // Return JSON Result
        return json_encode($result);
	}
}