<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function all(Request $request)
    {
        try {
            $id = $request->id;
            $limit = $request->limit ?? 10;
            $name = $request->name;

            if ($id) {
                $company = Company::withCount('users')->findOrfail($id);

                if (!$company) {
                    return ResponseFormatter::error('Company not found', 404);
                }

                return ResponseFormatter::success($company, 'Company Found');
            }

            $company = Company::when(!is_null($name), function ($query) use ($name) {
                return $query->where('name', 'like', '%' . $name . '%');
            })
            ->withCount('users')
            ->paginate($limit);

            return ResponseFormatter::success($company, 'Companies Found');
        } catch (\Throwable $th) {
            return ResponseFormatter::error($th->getMessage(), $th->getCode());
        }
    }
}
