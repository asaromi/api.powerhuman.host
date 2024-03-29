<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\{Company, Employee, Responsibility};
use App\Http\Requests\Role\{CreateRequest, UpdateRequest};
use App\Models\Role;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\{Request, Response};

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Response
     */
    public function fetchRoles(Request $request)
    {
        try {
            $limit = $request->limit ?? 10;
            $name = $request->name;
            $company_id = $request->company_id;

            $roles = Role::withoutTrashed()
                ->when($company_id ?? false,
                    fn($query, $company_id) => $query->where('company_id', (int) $company_id)
                )
                ->when($name ?? false,
                    fn($query, $name) => $query
                        ->where('name', 'like', '%' . $name . '%')
                        ->orWhereHas('company',
                            fn($query) => $query->where('name', 'like', '%' . $name . '%')
                        )
                )
                ->with(['responsibilities:id,name,role_id', 'company:id,name,logo'])
                ->paginate($limit);

            return ResponseFormatter::success($roles, 'Found Roles');
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
    public function createRole(CreateRequest $request)
    {
        try {
            $is_exists_company = Company::withoutTrashed()->where('id', (int) $request->company_id)->exists();
            if (!$is_exists_company) throw new \Exception('Company not found', 404);

            $validated = $request->validated();
            $role = Role::create([
                'name' => $validated['name'],
                'company_id' => $validated['company_id'],
            ]);

            $role->load('company');

            return ResponseFormatter::success($role, 'Role created', 201);
        } catch (\Exception $e) {
            return ResponseFormatter::error($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param Role $id
     * @return Response
     */
    public function updateRole(UpdateRequest $request, Role $id)
    {
        try {
            if ($company_id = (int) $request->company_id) {
                $is_exists_company = Company::withoutTrashed()->where('id', $company_id)->exists();
                if(!$is_exists_company) throw new \Exception('Company not found', 404);
            }

            $validated = $request->validated();
            $id->update($validated);

            $id->load(['responsibilities', 'company']);

            return ResponseFormatter::success($id->toArray(), 'Role Updated');
        } catch (\Exception $e) {
            return ResponseFormatter::error($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Role $id
     * @return Response
     */
    public function deleteRole(Role $id)
    {
        // using db transaction to make sure that all functionalities are succeed (it will be commit the changes to db)
        // or rolling back the transaction when has failed functionality
        DB::beginTransaction();
        try {
            $role = $id->load(['responsibilities:id,role_id', 'employees:id,role_id']);

            $responsibility_ids = $role->responsibilities->pluck('id');
            Responsibility::withoutTrashed()->whereIn('id', $responsibility_ids)->delete();

            $employees_id = $role->employees->pluck('id');
            Employee::withoutTrashed()->whereIn('id', $employees_id)->delete();

            $id->delete();

            DB::commit();
            return ResponseFormatter::success($id, 'Role Deleted');
        } catch (\Exception $e) {
            DB::rollBack();
            return ResponseFormatter::error($e->getMessage(), $e->getCode());
        }
    }
}
