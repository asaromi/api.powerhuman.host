<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Company\{CreateRequest, UpdateRequest};
use App\Models\{Company, User};
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CompanyController extends Controller
{
    public function fetchCompanies(Request $request): Response
    {
        try {
            $limit = $request->limit ?? 10;
            $name = $request->name ?? false;

            $companies = Company::withoutTrashed()
                ->when($name, fn($query, $name) => $query->where('name', 'like', '%' . $name . '%'))
                ->whereHas('users', fn($query) => $query->where('name', $name))
                ->withCount('users')
                ->paginate($limit);

            return ResponseFormatter::success($companies, 'Found Companies');
        } catch (\Throwable $th) {
            return ResponseFormatter::error($th->getMessage(), $th->getCode());
        }
    }

    public function fetchCompany(Company $id): Response
    {
        try {
            if (!($id instanceof Company)) {
                throw new Exception('Company not found', 404);
            } else {
                $company = $id;
            }

            return ResponseFormatter::success($company, 'Company Found');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), $e->getCode());
        }
    }

    public function createCompany(CreateRequest $request): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
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

    public function updateCompany(UpdateRequest $request, Company $id)
    {
        try {
            $validated = $request->validated();

            if (array_key_exists('logo', $validated)) {
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
