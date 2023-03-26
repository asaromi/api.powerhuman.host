<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Company\{CreateRequest,UpdateRequest};
use App\Models\{Company,User};
use Exception;
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
                    throw new Exception('Company not found', 404);
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

    public function create(CreateRequest $request)
    {
        try {
            $validated = $request->validated();

            if (isset($request->logo)) {
                $uploadedFile = $request->file('logo')->store('public/company');
                $validated['logo'] = str_replace('public/', 'storage/', $uploadedFile);
            }

            $company = Company::create([
                'name' => $validated['name'],
                'logo' => $validated['logo'] ?? null,
            ]);

            $user = auth()->user();
            if ($user instanceof User) $user->companies()->attach($company->id);

            return ResponseFormatter::success($company, 'Company Created');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), $e->getCode());
        }
    }

    public function update(UpdateRequest $request, Company $id)
    {
        try {
            $validated = $request->validated();

            if (isset($validated['logo']) || $validated['logo']) {
                $uploadedFile = $request->file('logo')->store('public/company');
                $validated['logo'] = str_replace('public/', 'storage/', $uploadedFile);
            }

            if (!($id instanceof Company)) {
                throw new Exception('Company not found', 404);
            } else {
                $company = $id;
            }

            $company->update([
                'name' => $request->name,
                'logo' => $request->logo ?? null,
            ]);

            return ResponseFormatter::success($company, 'Company Updated');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), $e->getCode());
        }
    }

    
}
