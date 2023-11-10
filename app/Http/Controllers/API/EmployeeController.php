<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\{Employee, Role, Team};
use App\Http\Requests\Employee\{CreateRequest, UpdateRequest};
use Illuminate\Http\{Request, Response};

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  Request  $request
     * @return Response
     */
    public function fetchEmployees(Request $request)
    {
        try {
            $limit = $request->limit ?? 10;
            $name = $request->name;
            $team_id = $request->team_id;
            $role_id = $request->role_id;

            $employees = Employee::withoutTrashed()
                ->when($name ?? false,
                    fn($query, $name) => $query
                        ->where('name', 'like', '%' . $name . '%')
                        ->orWhereHas('team',
                            fn($query) => $query->where('name', 'like', '%' . $name . '%')
                        )
                        ->orWhereHas('role',
                            fn($query) => $query->where('name', 'like', '%' . $name . '%')
                        )
                )
                ->when($team_id ?? false, fn($query) => $query->where('team_id', (int) $team_id))
                ->when($role_id ?? false, fn($query) => $query->where('role_id', (int) $role_id))
                ->paginate($limit);

            return ResponseFormatter::success($employees);
        } catch (\Exception $e) {
            return ResponseFormatter::error($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CreateRequest  $request
     * @return Response
     */
    public function createEmployee(CreateRequest $request)
    {
        try {
            $validated = $request->validated();

            $is_exists_role = Role::withoutTrashed()->where('id', $validated['role_id'])->exists();
            if (!$is_exists_role) throw new \Exception('Role not found', 404);

            $is_exists_team = Team::withoutTrashed()->where('id', $validated['team_id'])->exists();
            if (!$is_exists_team) throw new \Exception('Team not found', 404);

            $employee = Employee::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'age' => $validated['age'] ?? null,
                'photo' => $validated['photo'] ?? null,
                'role_id' => $validated['role_id'],
                'team_id' => $validated['team_id']
            ]);

            $employee->load(['role', 'team']);

            return ResponseFormatter::success($employee, 'Employee Created');
        } catch (\Exception $e) {
            return ResponseFormatter::error($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateRequest  $request
     * @param  Employee  $id
     * @return Response
     */
    public function updateEmployee(UpdateRequest $request, Employee $id)
    {
        try {
            $validated = $request->validated();

            if (array_key_exists('role_id', $validated)) {
                $is_exists_role = Role::withoutTrashed()->where('id', (int) $request->role_id)->exists();
                if (!$is_exists_role) throw new \Exception('Role not found', 404);
            }

            if (array_key_exists('team_id', $validated)) {
                $is_exists_team = Team::withoutTrashed()->where('id', (int) $request->team_id)->exists();
                if (!$is_exists_team) throw new \Exception('Team not found', 404);
            }

            $id->update($validated);
            $id->load(['role', 'team']);

            return ResponseFormatter::success($id, 'Employee Updated');
        } catch (\Exception $e) {
            return ResponseFormatter::error($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Employee  $id
     * @return Response
     */
    public function deleteEmployee(Employee $id)
    {
        try {
            $employee = $id->toArray();
            $id->delete();

            return ResponseFormatter::success($employee, 'Employee Deleted');
        } catch (\Exception $e) {
            return ResponseFormatter::error($e->getMessage(), $e->getCode());
        }
    }
}
