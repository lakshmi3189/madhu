<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

 /**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Api Documentation",
 *     description="Api Documentation",
 *     @OA\Contact(
 *         name="xyz",
 *         email=""
 *     ),     
 * ),
 * @OA\Server(
 *     url="/api/",
 * ),
 */

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;    
}
