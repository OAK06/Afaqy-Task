# Afaqy Task

## Installation
The files are based on Laravel framework as there is a controller and a model file. You can run these files easily on any laravel project aslong as you set it up with the database supplied in the task email.
- Place the **VehicleReports.php** in /app
- Place the **VehicleController.php** in /app/Http/Controllers
- Add 
`Route::group(['middleware' => 'throttle:5,1'], function () {
	Route::post('getVehicleExpenses','VehicleController@getVehicleExpenses');
});`
to /routes/api.php

## API Details
**Post Request URL:** 
<project_url>/api/getVehicleExpenses

**Post Parameters:**
- search_name (string)
- filter_fuel (bool)
- filter_insurance (bool)
- filter_service (bool)
- min_cost (bool)
- max_cost (bool)
- min_creation_date (bool)
- max_creation_date (bool)
- orderby (string) Ex: (cost, created_at)
- sorting (string) Ex: (ASC, DESC)

**Example Request Params:**

    search_name:Garland
    filter_fuel:true
    filter_insurance:true
    filter_service:false
    min_cost:true
    max_cost:false
    min_creation_date:false
    max_creation_date:false
    orderby:cost
	sorting:desc
    
**Note:** I used Postman for testing.