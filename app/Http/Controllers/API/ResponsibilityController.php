<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Responsibility\{CreateRequest, UpdateRequest};
use App\Models\Responsibility;
use Illuminate\Http\{Request, Response};

class ResponsibilityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Response
     */
    public function fetchResponsibilities(Request $request)
    {
        try {
            $limit = $request->limit ?? 10;
            $name = $request->name;
            $role_id = $request->role_id;

            $responsibilities = Responsibility::withoutTrashed()
                ->when($name ?? false,
                    fn($query, $name) => $query
                        ->where('name', 'like', '%' . $name . '%')
                        ->orWhereHas('role',
                            fn($query) => $query->where('name', 'like', '%' . $name . '%')
                        )
                )
                ->when($role_id ?? false,
                    fn($query, $name) => $query->where('role_id', (int) $role_id)
                )
                ->with('role')
                ->paginate($limit);

            return ResponseFormatter::success($responsibilities, 'Found Responsibilities');
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
    public function createResponsibility(CreateRequest $request)
    {
        try {
            $validated = $request->validated();

            $responsibility = Responsibility::create([
                'name' => $validated['name'],
                'role_id' => $validated['role_id']
            ]);

            $responsibility->load('role');

            return ResponseFormatter::success($responsibility, 'Responsibility Created');
        } catch (\Exception $e) {
            return ResponseFormatter::error($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Responsibility $id
     * @return Response
     */
    public function deleteResponsibility(Responsibility $id)
    {
        try {
            $responsibility = $id->toArray();
            $id->delete();

            return ResponseFormatter::success($responsibility, 'Responsibility Deleted');
        } catch (\Exception $e) {
            return ResponseFormatter::error($e->getMessage(), $e->getCode());
        }
    }
}
