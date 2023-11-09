<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Company\{CreateRequest, UpdateRequest};
use App\Models\{Company, User};
use Exception;
use Illuminate\Http\{Request, Response};

class CompanyController extends Controller
{
    /**
     * Display a listing of the Companies.
     *
     * @param  Request  $request
     * @return Response
     */
    public function fetchCompanies(Request $request)
    {
        try {
            $limit = $request->limit ?? 10;
            $name = $request->name;
            $user_id = $request->user_id;

            $companies = Company::withoutTrashed()
                ->when($name ?? false,
                    fn($query, $name) => $query->where('name', 'like', '%' . $name . '%')
                        ->orWhereHas('users', fn($query) => $query->where('name', 'like', '%' . $name . '%'))
                )
                ->when($user_id ?? false,
                    fn($query, $user_id) => $query->whereHas('users',
                        fn($query) => $query->where('user_id', (int) $user_id)
                    )
                )
                ->with('users:id,name,email,profile_photo_path')
                ->paginate($limit);

            return ResponseFormatter::success($companies, 'Found Companies');
        } catch (\Exception $e) {
            return ResponseFormatter::error($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateRequest $request
     * @return Response
     */
    public function createCompany(CreateRequest $request)
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

    /**
     * Update the specified team in storage.
     *
     * @param  UpdateRequest  $request
     * @param  Company  $id
     * @return Response
     */
    public function updateCompany(UpdateRequest $request, Company $id)
    {
        try {
            if (!($id instanceof Company)) throw new Exception('Company not found', 404);

            $validated = $request->validated();
            if ($request->hasFile('logo')) {
                $uploadedFile = $request->file('logo')->store('public/company');
                $validated['logo'] = str_replace('public/', 'storage/', $uploadedFile);
            }

            $id->update([
                'name' => $request->name,
                'logo' => $request->logo ?? null,
            ]);

            $id->load('users');

            return ResponseFormatter::success($id, 'Company Updated');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), $e->getCode());
        }
    }
}
