<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponses;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    use ApiResponses, AuthorizesRequests;

    protected $policyClass;
    public function include(string $relationship) : bool {
        $param = request()->get('include');
         if(!isset($param)) {
            return false;
         }
         $inculudeValues = explode(',', strtolower($param));
         return in_array(strtolower($relationship), $inculudeValues);
    }

    public function isAble(string $ability, string | null $targetModel) {
        return $this->authorize($ability, [$targetModel, $this->policyClass]);
    }
}