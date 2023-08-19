<?php

/**
 * | Created On-23-05-2023 
 * | Author - Anshu Kumar
 * | Routes Specified for the Master Crud Operations
 */

/**
 * | Created On-23-05-2023 
 * | Author - Lakshmi Kumari
 * | Routes Specified for the Master Crud Operations
 * | Code Status : Open
 */

use App\Http\Controllers\API\Master\DiscountGroupController;                                //M_API_1
use App\Http\Controllers\API\Master\FeeHeadTypeController;                                  //M_API_2
use App\Http\Controllers\API\Master\FeeHeadController;                                      //M_API_3
use App\Http\Controllers\API\Master\ClassFeeMasterController;                               //M_API_4
use App\Http\Controllers\API\Master\FeeDefinitionController;                                //M_API_5
use App\Http\Controllers\API\Master\DiscountGroupMapController;                             //M_API_6
use App\Http\Controllers\API\Master\UserTypeController;                                     //M_API_7   //JSON
use App\Http\Controllers\API\Master\FeeDemandController;                                    //M_API_8
use App\Http\Controllers\API\Master\MiscellaneousCategoryController;                        //M_API_9
use App\Http\Controllers\API\Master\MiscellaneousSubCategoryController;                     //M_API_10
use App\Http\Controllers\API\Master\CountryController;                                      //M_API_11
use App\Http\Controllers\API\Master\StateController;                                        //M_API_12
use App\Http\Controllers\API\Master\TeachingTitleController;                                //M_API_13
use App\Http\Controllers\API\Master\DepartmentController;                                   //M_API_14
use App\Http\Controllers\API\Master\SectionController;                                      //M_API_15
use App\Http\Controllers\API\Master\BankController;                                         //M_API_16
use App\Http\Controllers\API\Master\ClassMasterController;                                  //M_API_17
use App\Http\Controllers\API\Master\CityController;                                         //M_API_18
use App\Http\Controllers\API\Master\EmploymentTypeController;                               //M_API_19
use App\Http\Controllers\API\Master\MonthController;                                        //M_API_20   //JSON                      
use App\Http\Controllers\API\Transport\BusFeeFineController;                                //M_API_21
use App\Http\Controllers\API\Transport\DriverController;                                    //M_API_22
use App\Http\Controllers\API\Transport\VehicleTypeController;                               //M_API_23
use App\Http\Controllers\API\Transport\VehicleController;                                   //M_API_24
use App\Http\Controllers\API\Transport\PickupController;                                    //M_API_25
use App\Http\Controllers\API\Transport\RouteController;                                     //M_API_26
use App\Http\Controllers\API\Master\MiscellaneousController;                                //M_API_27   //JSON
use App\Http\Controllers\API\Master\ExtracurricularController;                              //M_API_28
use App\Http\Controllers\API\Transport\VehicleInchargeController;                           //M_API_29
use App\Http\Controllers\API\Transport\DropPointController;                                 //M_API_30
use App\Http\Controllers\API\Master\FinancialYearController;                                //M_API_31  //JSON
use App\Http\Controllers\API\Master\IconController;                                         //M_API_32  //JSON
use App\Http\Controllers\API\Master\MenuController;                                         //M_API_33
use App\Http\Controllers\API\Master\SubjectController;                                      //M_API_34
use App\Http\Controllers\API\Master\SubMenuController;                                      //M_API_35
use App\Http\Controllers\API\Master\SectionGroupMapController;                              //M_API_36
use App\Http\Controllers\API\Master\SubjectGroupMapController;                              //M_API_37
use App\Http\Controllers\API\Master\RoleController;                                         //M_API_38
use App\Http\Controllers\API\Master\MenuGroupMapController;                                 //M_API_39



// =================================Protected Routes Start====================================================================
Route::middleware('auth:sanctum')->group(function () {

    Route::controller(DiscountGroupController::class)->group(function () {
        Route::post('discount-group/crud/store', 'store');                              // Store                    M_API_1.1
        Route::post('discount-group/crud/edit', 'edit');                                // Edit                     M_API_1.2
        Route::post('discount-group/crud/show', 'show');                                // Get by Id                M_API_1.3
        Route::post('discount-group/crud/retrieve-all', 'retrieveAll');                 // Get all Records          M_API_1.4
        Route::post('discount-group/crud/active-all', 'activeAll');                     // Get all active Records   M_API_1.5
        Route::post('discount-group/crud/search', 'search');                            // Search                   M_API_1.6
        Route::post('discount-group/crud/delete', 'delete');                            // Delete                   M_API_1.7
    });

    Route::controller(DiscountGroupMapController::class)->group(function () {
        Route::post('discount-group-map/crud/store', 'store');                          // Store                    M_API_6.1
        Route::post('discount-group-map/crud/edit', 'edit');                            // Edit                     M_API_6.2
        Route::post('discount-group-map/crud/show', 'show');                            // Get Record By id         M_API_6.3
        Route::post('discount-group-map/crud/retrieve-all', 'retrieveAll');             // Get All Records          M_API_6.4
        Route::post('discount-group-map/crud/active-all', 'activeAll');                 // Get All Active Records   M_API_6.5
        Route::post('discount-group-map/crud/search', 'search');                        // Search                   M_API_6.6
        Route::post('discount-group-map/crud/delete', 'delete');                        // Delete                   M_API_6.7
    });

    Route::controller(FeeHeadTypeController::class)->group(function () {
        Route::post('feehead-type/crud/store', 'store');                                // Store                    M_API_2.1
        Route::post('feehead-type/crud/edit', 'edit');                                  // Update                   M_API_2.2
        Route::post('feehead-type/crud/show', 'show');                                  // Get Records by Id        M_API_2.3
        Route::post('feehead-type/crud/retrieve-all', 'retrieveAll');                   // Get all records          M_API_2.4
        Route::post('feehead-type/crud/delete', 'delete');                              // Delete                   M_API_2.5
        Route::post('feehead-type/crud/active-all', 'activeAll');                       // Get All Active Records   M_API_2.6
        Route::post('feehead-type/crud/search', 'search');                              // Search                   M_API_2.7
    });

    Route::controller(FeeHeadController::class)->group(function () {
        Route::post('fee-head/crud/store', 'store');                                    // Store                    M_API_3.1
        Route::post('fee-head/crud/edit', 'edit');                                      // Update                   M_API_3.2
        Route::post('fee-head/crud/show', 'show');                                      // Get Records by Id        M_API_3.3      
        Route::post('fee-head/crud/retrieve-all', 'retrieveAll');                       // Get all records          M_API_3.4
        Route::post('fee-head/crud/delete', 'delete');                                  // delete                   M_API_3.5
        Route::post('fee-head/crud/active-all', 'activeAll');                           // Get all active records   M_API_3.6
        Route::post('fee-head/crud/search', 'search');                                  // Search                   M_API_3.7
    });

    Route::controller(ClassFeeMasterController::class)->group(function () {
        Route::post('classfee-master/crud/store', 'store');                             // Store                    M_API_4.1
        Route::post('classfee-master/crud/edit', 'edit');                               // Update                   M_API_4.2
        Route::post('classfee-master/crud/show', 'show');                               // Get by Id                M_API_4.3
        Route::post('classfee-master/crud/retrieve-all', 'retrieveAll');                // Get all records          M_API_4.4
        Route::post('classfee-master/crud/delete', 'delete');                           // delete                   M_API_4.5
        Route::post('classfee-master/crud/showByClass', 'showByClass');                 // Get by class Id          M_API_4.6
        Route::post('classfee-master/crud/active-all', 'activeAll');                    // Get All Active Records   M_API_4.7
        Route::post('classfee-master/crud/search', 'search');                           // Search                   M_API_4.8
    });

    Route::controller(FeeDefinitionController::class)->group(function () {
        Route::post('fee-definition/crud/store', 'store');                              // Store                    M_API_5.1
        Route::post('fee-definition/crud/edit', 'edit');                                // Edit                     M_API_5.2
        Route::post('fee-definition/crud/show', 'show');                                // Get by Id                M_API_5.3
        Route::post('fee-definition/crud/retrieve-all', 'retrieveAll');                 // Get all records          M_API_5.4
        Route::post('fee-definition/crud/delete', 'delete');                            // Delete                   M_API_5.5
        Route::post('fee-definition/crud/active-all', 'activeAll');                     // Get all active records   M_API_5.6
        Route::post('fee-definition/crud/search', 'search');                            // Search                   M_API_5.7
    });

    Route::controller(FeeDemandController::class)->group(function () {
        Route::post('fee-demand/generate', 'generateDemand');                           // Generate Fee Demand      M_API_8.1
        Route::post('fee-demand/retrieve-all', 'retrieveAll');                          // Get All Demand Data      M_API_8.2
        Route::post('fy-wise-demand/generate', 'generateFyWiseDemand');                 // Generate Demand fy wise for all std  M_API_8.3
        Route::post('fee-collection/show', 'show');                                     // Fee Collection
        // Route::post('generate-student-demand', 'generateDemand');                    // Testing                        
        // Route::post('/fee-demand/addDemand','addDemand');                            // Testing                    
        Route::post('/fee-demand', 'readDemand');                                       // Testing 
    });

    Route::controller(MiscellaneousCategoryController::class)->group(function () {
        Route::post('miscellaneous-category/crud/store', 'store');                      // Store                    M_API_9.1
        Route::post('miscellaneous-category/crud/edit', 'edit');                        // Edit                     M_API_9.2
        Route::post('miscellaneous-category/crud/show', 'show');                        // Get All Records By ID    M_API_9.3
        Route::post('miscellaneous-category/crud/retrieve-all', 'retrieveAll');         // Get All Records          M_API_9.4
        Route::post('miscellaneous-category/crud/delete', 'delete');                    // Delete                   M_API_9.5
        Route::post('miscellaneous-category/crud/active-all', 'activeAll');             // Get All Active Records   M_API_9.6
        Route::post('miscellaneous-category/crud/search', 'search');                    // Search                   M_API_9.7
    });

    Route::controller(MiscellaneousSubCategoryController::class)->group(function () {
        Route::post('miscellaneous-sub-category/crud/store', 'store');                  // Store                    M_API_10.1
        Route::post('miscellaneous-sub-category/crud/edit', 'edit');                    // Edit                     M_API_10.2
        Route::post('miscellaneous-sub-category/crud/show', 'show');                    // Get Records By ID        M_API_10.3
        Route::post('miscellaneous-sub-category/crud/retrieve-all', 'retrieveAll');     // Get All Records          M_API_10.4
        Route::post('miscellaneous-sub-category/crud/delete', 'delete');                // Delete                   M_API_10.5
        Route::post('miscellaneous-sub-category/crud/active-all', 'activeAll');         // Get All Active Records   M_API_10.6
        Route::post('miscellaneous-sub-category/crud/search', 'search');                // Search                   M_API_10.7
    });

    Route::controller(CountryController::class)->group(function () {
        Route::post('country/crud/store', 'store');                                     //Store                     M_API_11.1
        Route::post('country/crud/edit', 'edit');                                       //Update                    M_API_11.2
        Route::post('country/crud/show', 'show');                                       //Get by Id                 M_API_11.3
        Route::post('country/crud/retrieve-all', 'retrieveAll');                        //Get all records           M_API_11.4
        Route::post('country/crud/delete', 'delete');                                   //delete                    M_API_11.5
        Route::post('country/crud/active-all', 'activeAll');                            // Get All Active Records   M_API_11.6
        Route::post('country/crud/search', 'search');                                   // Search                   M_API_11.7
    });

    Route::controller(StateController::class)->group(function () {
        Route::post('state/crud/store', 'store');                                       //Store                     M_API_12.1
        Route::post('state/crud/edit', 'edit');                                         //Update                    M_API_12.2
        Route::post('state/crud/show', 'show');                                         //Get by Id                 M_API_12.3
        Route::post('state/crud/retrieve-all', 'retrieveAll');                          //Get all records           M_API_12.4
        Route::post('state/crud/delete', 'delete');                                     //delete                    M_API_12.5
        Route::post('state/crud/active-all', 'activeAll');                              // Get All Active Records   M_API_12.6
        Route::post('state/crud/search', 'search');                                     // Search                   M_API_12.7
    });

    Route::controller(TeachingTitleController::class)->group(function () {
        Route::post('teaching-title/crud/store', 'store');                              // Store                    M_API_13.1
        Route::post('teaching-title/crud/edit', 'edit');                                // Update                   M_API_13.2
        Route::post('teaching-title/crud/show', 'show');                                // Get by Id                M_API_13.3
        Route::post('teaching-title/crud/retrieve-all', 'retrieveAll');                 // Get all records          M_API_13.4
        Route::post('teaching-title/crud/delete', 'delete');                            // delete                   M_API_13.5
        Route::post('teaching-title/crud/active-all', 'activeAll');                     // Get all active           M_API_13.6
        Route::post('teaching-title/crud/search', 'search');                            // search                   M_API_13.7
    });

    Route::controller(DepartmentController::class)->group(function () {
        Route::post('department/crud/store', 'store');                                  // Store                    M_API_14.1                   
        Route::post('department/crud/edit', 'edit');                                    // Update                   M_API_14.2
        Route::post('department/crud/show', 'show');                                    // Get by Id                M_API_14.3
        Route::post('department/crud/retrieve-all', 'retrieveAll');                     // Get all records          M_API_14.4
        Route::post('department/crud/delete', 'delete');                                // delete                   M_API_14.5
        Route::post('department/crud/active-all', 'activeAll');                         // Get all active           M_API_14.6
        Route::post('department/crud/search', 'search');                                // Search                   M_API_14.7
    });

    Route::controller(SectionController::class)->group(function () {
        Route::post('section/crud/store', 'store');                                     // Add Record               M_API_15.1              
        Route::post('section/crud/edit', 'edit');                                       // Edit Record              M_API_15.2
        Route::post('section/crud/show', 'show');                                       // Show by id               M_API_15.3
        Route::post('section/crud/retrieve-all', 'retrieveAll');                        // Get all Records          M_API_15.4
        Route::post('section/crud/delete', 'delete');                                   // delete                   M_API_15.5
        Route::post('section/crud/active-all', 'activeAll');                            // Get all active           M_API_15.6
        Route::post('section/crud/search', 'search');                                   // search                   M_API_15.7
    });

    Route::controller(BankController::class)->group(function () {
        Route::post('bank/crud/store', 'store');                                        // Add Record               M_API_16.1
        Route::post('bank/crud/edit', 'edit');                                          // Edit Record              M_API_16.2
        Route::post('bank/crud/show', 'show');                                          // Show Record              M_API_16.3
        Route::post('bank/crud/retrieve-all', 'retrieveAll');                           // Fetch all Records        M_API_16.4
        Route::post('bank/crud/delete', 'delete');                                      // Delete                   M_API_16.5
        Route::post('bank/crud/active-all', 'activeAll');                               // Get all active           M_API_16.6
        Route::post('bank/crud/search', 'search');                                      // search                   M_API_16.7
    });

    Route::controller(ClassMasterController::class)->group(function () {
        Route::post('class/crud/store', 'store');                                       // Add Record               M_API_17.1
        Route::post('class/crud/edit', 'edit');                                         // Edit Record              M_API_17.2
        Route::post('class/crud/show', 'show');                                         // Show Record              M_API_17.3
        Route::post('class/crud/retrieve-all', 'retrieveAll');                          // Fetch all Records        M_API_17.4
        Route::post('class/crud/delete', 'delete');                                     // Delete Record            M_API_17.5
        Route::post('class/crud/active-all', 'activeAll');                              // Get all active           M_API_17.6
        Route::post('class/crud/search', 'search');                                     // Search                   M_API_17.7
    });

    Route::controller(CityController::class)->group(function () {
        Route::post('city/crud/store', 'store');                                        // Add Record               M_API_18.1
        Route::post('city/crud/edit', 'edit');                                          // Edit Record              M_API_18.2
        Route::post('city/crud/show', 'show');                                          // Show Record              M_API_18.3
        Route::post('city/crud/retrieve-all', 'retrieveAll');                           // Fetch all Records        M_API_18.4
        Route::post('city/crud/delete', 'delete');                                      // Delete Record            M_API_18.5
        Route::post('city/crud/active-all', 'activeAll');                               // Get all                  M_API_18.6
        Route::post('city/crud/search', 'search');                                      // Seacrh                   M_API_18.7
    });

    Route::controller(EmploymentTypeController::class)->group(function () {
        Route::post('employment-type/crud/store', 'store');                             // Add Record               M_API_19.1
        Route::post('employment-type/crud/edit', 'edit');                               // Edit Record              M_API_19.2
        Route::post('employment-type/crud/show', 'show');                               // Show Record              M_API_19.3
        Route::post('employment-type/crud/retrieve-all', 'retrieveAll');                // Fetch all Records        M_API_19.4
        Route::post('employment-type/crud/delete', 'delete');                           // Deactive Record          M_API_19.5
        Route::post('employment-type/crud/active-all', 'activeAll');                    // Get all active           M_API_19.6
        Route::post('employment-type/crud/search', 'search');                           // Search                   M_API_19.7
    });

    Route::controller(MonthController::class)->group(function () {
        Route::post('month/retrieve-all', 'retrieveAll');                               // Get all                  M_API_20.1              
    });

    Route::controller(FinancialYearController::class)->group(function () {
        Route::post('financial-year/retrieve-all', 'retrieveAll');                      // Get all                  M_API_31.1
    });

    Route::controller(IconController::class)->group(function () {
        Route::post('icon/retrieve-all', 'retrieveAll');                                // Get all                  M_API_32.1
    });

    Route::controller(UserTypeController::class)->group(function () {
        Route::post('user-type/retrieve-all', 'retrieveAll');                           // Get all                  M_API_7.1
        Route::post('user-type/active-all', 'activeAll');                               // Get all active           M_API_7.2
        Route::post('user-type/store', 'store');                                        // store                    M_API_7.3
        Route::post('user-type/edit', 'edit');                                          // edit                     M_API_7.4
        Route::post('user-type/show', 'show');                                          // show                     M_API_7.5
        Route::post('user-type/delete', 'delete');                                      // delete                   M_API_7.6
        Route::post('user-type/search', 'search');                                      // search                   M_API_7.7
    });

    Route::controller(BusFeeFineController::class)->group(function () {
        Route::post('busfee-fine/crud/store', 'store');                                 // Store                    M_API_21.1
        Route::post('busfee-fine/crud/edit', 'edit');                                   // Update                   M_API_21.2
        Route::post('busfee-fine/crud/show', 'show');                                   // Get by Id                M_API_21.3
        Route::post('busfee-fine/crud/retrieve-all', 'retrieveAll');                    // Get all records          M_API_21.4
        Route::post('busfee-fine/crud/delete', 'delete');                               // Delete                   M_API_21.5
        Route::post('busfee-fine/crud/active-all', 'activeAll');                        // Get all active           M_API_21.6
        Route::post('busfee-fine/crud/search', 'search');                               // Search                   M_API_21.7
    });

    Route::controller(DriverController::class)->group(function () {
        Route::post('driver/crud/store', 'store');                                     // Store                    M_API_22.1
        Route::post('driver/crud/edit', 'edit');                                       // Update                   M_API_22.2
        Route::post('driver/crud/show', 'show');                                       // Get by Id                M_API_22.3
        Route::post('driver/crud/retrieve-all', 'retrieveAll');                         // Get all records          M_API_22.4
        Route::post('driver/crud/delete', 'delete');                                    // delete                   M_API_22.5
        Route::post('driver/crud/active-all', 'activeAll');                             // Get all active           M_API_22.6
        Route::post('driver/crud/search', 'search');                                    // search                   M_API_22.7
    });

    Route::controller(VehicleTypeController::class)->group(function () {
        Route::post('vehicle-type-name/crud/store', 'store');                            // Add Record              M_API_23.1
        Route::post('vehicle-type-name/crud/edit', 'edit');                              // Edit Record             M_API_23.2
        Route::post('vehicle-type-name/crud/show', 'show');                              // Show Record             M_API_23.3
        Route::post('vehicle-type-name/crud/retrieve-all', 'retrieveAll');               // Fetch all Records       M_API_23.4
        Route::post('vehicle-type-name/crud/delete', 'delete');                          // Deactive Record         M_API_23.5
        Route::post('vehicle-type-name/crud/active-all', 'activeAll');                   // Get all active          M_API_23.6
        Route::post('vehicle-type-name/crud/search', 'search');                          // search                  M_API_23.7
    });

    Route::controller(VehicleController::class)->group(function () {
        Route::post('vehicle/crud/store', 'store');                                      // Add Record              M_API_24.1
        Route::post('vehicle/crud/edit', 'edit');                                        // Edit Record             M_API_24.2
        Route::post('vehicle/crud/show', 'show');                                        // Show Record             M_API_24.3
        Route::post('vehicle/crud/retrieve-all', 'retrieveAll');                         // Fetch all Records       M_API_24.4
        Route::post('vehicle/crud/delete', 'delete');                                    // Deactive Record         M_API_24.5
        Route::post('vehicle/crud/active-all', 'activeAll');                             // Get all records         M_API_24.6
        Route::post('vehicle/crud/search', 'search');                                    // Search                  M_API_24.7
    });

    Route::controller(PickupController::class)->group(function () {
        Route::post('pickup-point/crud/store', 'store');                                  // Add Record             M_API_25.1
        Route::post('pickup-point/crud/edit', 'edit');                                    // Edit Record            M_API_25.2
        Route::post('pickup-point/crud/show', 'show');                                    // Show Record            M_API_25.3
        Route::post('pickup-point/crud/retrieve-all', 'retrieveAll');                     // Fetch all Records      M_API_25.4
        Route::post('pickup-point/crud/delete', 'delete');                                // Deactive Record        M_API_25.5
        Route::post('pickup-point/crud/active-all', 'activeAll');                         // Get all active         M_API_25.6
        Route::post('pickup-point/crud/search', 'search');                                // Search                 M_API_25.7
    });

    Route::controller(RouteController::class)->group(function () {
        Route::post('route/crud/store', 'store');                                        // Add Record              M_API_26.1
        Route::post('route/crud/edit', 'edit');                                          // Edit Record             M_API_26.2
        Route::post('route/crud/show', 'show');                                          // Show Record             M_API_26.3
        Route::post('route/crud/retrieve-all', 'retrieveAll');                           // Fetch all Records       M_API_26.4
        Route::post('route/crud/delete', 'delete');                                      // Deactive Record         M_API_26.5
        Route::post('route/crud/active-all', 'activeAll');                               // Get all active          M_API_26.6
        Route::post('route/crud/search', 'search');                                      // search                  M_API_26.7
    });

    Route::controller(VehicleInchargeController::class)->group(function () {
        Route::post('vehicle-incharge/crud/store', 'store');                            // Add Record               M_API_29.1
        Route::post('vehicle-incharge/crud/edit', 'edit');                              // Edit Record              M_API_29.2
        Route::post('vehicle-incharge/crud/show', 'show');                              // Show Record              M_API_29.3
        Route::post('vehicle-incharge/crud/retrieve-all', 'retrieveAll');               // Fetch all Records        M_API_29.4
        Route::post('vehicle-incharge/crud/delete', 'delete');                          // Deactive Record          M_API_29.5
        Route::post('vehicle-incharge/crud/active-all', 'activeAll');                   // Get all active           M_API_29.6
        Route::post('vehicle-incharge/crud/search', 'search');                          // Search                   M_API_29.7
    });

    Route::controller(ExtracurricularController::class)->group(function () {
        Route::post('extra-curricular/crud/store', 'store');                            // Add Record               M_API_28.1
        Route::post('extra-curricular/crud/edit', 'edit');                              // Edit Record              M_API_28.2
        Route::post('extra-curricular/crud/show', 'show');                              // Show Record              M_API_28.3
        Route::post('extra-curricular/crud/retrieve-all', 'retrieveAll');               // Fetch all Records        M_API_28.4
        Route::post('extra-curricular/crud/delete', 'delete');                          // Deactive Record          M_API_28.5
        Route::post('extra-curricular/crud/active-all', 'activeAll');                   // Get all active           M_API_28.6
        Route::post('extra-curricular/crud/search', 'search');                          // search                   M_API_28.7
    });

    Route::controller(DropPointController::class)->group(function () {
        Route::post('drop-point/crud/store', 'store');                                  // Add Record               M_API_30.1
        Route::post('drop-point/crud/edit', 'edit');                                    // Edit Record              M_API_30.2
        Route::post('drop-point/crud/show', 'show');                                    // Show Record              M_API_30.3
        Route::post('drop-point/crud/retrieve-all', 'retrieveAll');                     // Fetch all Records        M_API_30.4
        Route::post('drop-point/crud/delete', 'delete');                                // Deactive Record          M_API_30.5
        Route::post('drop-point/crud/active-all', 'activeAll');                         // Get all                  M_API_30.6
        Route::post('drop-point/crud/search', 'search');                                // Search                   M_API_30.7
    });

    Route::controller(SubjectController::class)->group(function () {
        Route::post('subject/crud/store', 'store');                                     // Add Record               M_API_34.1
        Route::post('subject/crud/edit', 'edit');                                       // Edit Record              M_API_34.2
        Route::post('subject/crud/show', 'show');                                       // Show Record              M_API_34.3
        Route::post('subject/crud/retrieve-all', 'retrieveAll');                        // Fetch all Records        M_API_34.4
        Route::post('subject/crud/delete', 'delete');                                   // Deactive Record          M_API_34.5
        Route::post('subject/crud/active-all', 'activeAll');                            // Get all active           M_API_34.6
        Route::post('subject/crud/search', 'search');                                   // Search                   M_API_34.7
    });

    Route::controller(MenuController::class)->group(function () {
        Route::post('menu/crud/store', 'store');                                        // Add Record               M_API_33.1
        Route::post('menu/crud/edit', 'edit');                                          // Edit Record              M_API_33.2
        Route::post('menu/crud/show', 'show');                                          // Show Record              M_API_33.3
        Route::post('menu/crud/retrieve-all', 'retrieveAll');                           // Fetch all Records        M_API_33.4
        Route::post('menu/crud/delete', 'delete');                                      // Deactive Record          M_API_33.5
        Route::post('menu/crud/active-all', 'activeAll');                               // Get active all           M_API_33.6
        Route::post('menu/crud/search', 'search');                                      // Search                   M_API_33.7
    });

    Route::controller(SubMenuController::class)->group(function () {
        Route::post('sub-menu/crud/store', 'store');                                    // Add Record               M_API_35.1
        Route::post('sub-menu/crud/edit', 'edit');                                      // Edit Record              M_API_35.2
        Route::post('sub-menu/crud/show', 'show');                                      // Show Record              M_API_35.3
        Route::post('sub-menu/crud/retrieve-all', 'retrieveAll');                       // Fetch all Records        M_API_35.4
        Route::post('sub-menu/crud/delete', 'delete');                                  // Deactive Record          M_API_35.5
        Route::post('sub-menu/crud/active-all', 'activeAll');
        Route::post('sub-menu/crud/search', 'search');
        Route::post('sub-menu/show', 'showAll');
    });

    Route::controller(MiscellaneousController::class)->group(function () {
        Route::post('miscellaneous/retrieve-all', 'retrieveAll');                       // Get all data             M_API_27.1
    });

    Route::controller(SectionGroupMapController::class)->group(function () {
        Route::post('section-group-map/crud/store', 'store');                           // Add Record               M_API_36.1
        Route::post('section-group-map/crud/edit', 'edit');                             // Edit Record              M_API_36.2
        Route::post('section-group-map/crud/show', 'show');                             // Show Record              M_API_36.3
        Route::post('section-group-map/crud/retrieve-all', 'retrieveAll');              // Fetch all Records        M_API_36.4
        Route::post('section-group-map/crud/delete', 'delete');                         // Deactive Record          M_API_36.5
        Route::post('section-group-map/crud/active-all', 'activeAll');                  // Get all active           M_API_36.6
        Route::post('section-group-map/crud/search', 'search');                         // Search                   M_API_36.7
        Route::post('section-group-map/section', 'showByClassId');                      // show by id               M_API_36.8
    });

    Route::controller(SubjectGroupMapController::class)->group(function () {
        Route::post('subject-group-map/crud/store', 'store');                           // Add Record               M_API_37.1
        Route::post('subject-group-map/crud/edit', 'edit');                             // Edit Record              M_API_37.2
        Route::post('subject-group-map/crud/show', 'show');                             // Show Record              M_API_37.3
        Route::post('subject-group-map/crud/retrieve-all', 'retrieveAll');              // Fetch all Records        M_API_37.4
        Route::post('subject-group-map/crud/delete', 'delete');                         // Deactive Record          M_API_37.5
        Route::post('subject-group-map/crud/active-all', 'activeAll');                  // Get all active           M_API_37.6
        Route::post('subject-group-map/crud/search', 'search');                         // Search                   M_API_37.7
        Route::post('subject-group-map/subject', 'showByClassId');                      // Get by Id                M_API_37.8
        Route::post('subject/crud/active-all', 'getAllSubject');       
    });

    Route::controller(RoleController::class)->group(function () {
        Route::post('role/crud/store', 'store');                                        // Store                    M_API_38.1
        Route::post('role/crud/edit', 'edit');                                          // Update                   M_API_38.2
        Route::post('role/crud/show', 'show');                                          // Get by Id                M_API_38.3
        Route::post('role/crud/retrieve-all', 'retrieveAll');                           // Get all records          M_API_38.4
        Route::post('role/crud/delete', 'delete');                                      // delete                   M_API_38.5
        Route::post('role/crud/active-all', 'activeAll');                               // Get active all           M_API_38.6
        Route::post('role/crud/search', 'search');                                      // Search                   M_API_38.7
    });

    Route::controller(MenuGroupMapController::class)->group(function () {
        Route::post('menu-group-map/crud/store', 'store');                              // Add Record               M_API_39.1
        Route::post('menu-group-map/crud/edit', 'edit');                                // Edit Record              M_API_39.2
        Route::post('menu-group-map/crud/show', 'show');                                // Show Record              M_API_39.3
        Route::post('menu-group-map/crud/retrieve-all', 'retrieveAll');                 // Fetch all Records        M_API_39.4
        Route::post('menu-group-map/crud/delete', 'delete');                            // Deactive Record          M_API_39.5
        Route::post('menu-group-map/crud/active-all', 'activeAll');                     // active                   M_API_39.6
        Route::post('menu-group-map/crud/search', 'search');                            // search                   M_API_39.7
    });
});
// =============================================Protected Routes End===========================================================

// ==============================================Public Routes Start===========================================================
//for student online registration 
Route::controller(MiscellaneousController::class)->group(function () {
    Route::post('miscellaneous/online-registration/retrieve-all', 'retrieveAll');       //Get all data              M_API_27.2
});

Route::controller(ClassMasterController::class)->group(function () {
    Route::post('class', 'activeForAll');                              // Get all active                               M_API_17.8
});

Route::controller(SectionController::class)->group(function () {
    Route::post('section', 'activeAll');                              // Get all active                             M_API_15.8
});

Route::controller(CountryController::class)->group(function () {
    Route::post('country', 'activeAll');                              // Get all active                             M_API_11.8
});

Route::controller(StateController::class)->group(function () {
    Route::post('state', 'activeAll');                                    // get all                                M_API_12.8
});

Route::controller(CityController::class)->group(function () {
    Route::post('city', 'activeAll');                                    // get all                                 M_API_18.8
});

Route::controller(BankController::class)->group(function () {
    Route::post('bank', 'activeAll');                                    // get all                                 M_API_16.8
});

Route::controller(RouteController::class)->group(function () {
    Route::post('route', 'activeForAll');                                    // get all                                M_API_26.8
});

Route::controller(PickupController::class)->group(function () {
    Route::post('pickup-point', 'activeForAll');                                    // get all                         M_API_25.8
});


// ==============================================Public Routes End=============================================================


    

   






























// Route::controller(FeeDemandController::class)->group(function() {
//     Route::post('generate-student-demand','generateDemand');
//     // Route::post('/fee-demand/addDemand','addDemand');                           //Add  
//     // Route::post('/fee-demand/readDemand','readDemand');                           //Add  
    
// });