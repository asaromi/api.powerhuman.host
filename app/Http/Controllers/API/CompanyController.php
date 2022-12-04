<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Company;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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

    public function create(Request $request)
    {
        $detail_error = null;
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email|unique:companies',
                'logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'website' => 'required'
            ]);

            if ($validator->fails()) {
                $detail_error = $validator->errors();
                throw new Exception('Bad Request', 400);
            }

            $company = Company::create([
                'name' => $request->name,
                'email' => $request->email,
                'logo' => $request->logo,
                'website' => $request->website
            ]);

            return ResponseFormatter::success($company, 'Company Created');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), $e->getCode(), $detail_error);
        }
    }
}
