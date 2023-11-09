<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Team\{CreateRequest, UpdateRequest};
use App\Models\Team;
use Illuminate\Http\{Request, Response};
use Exception;

class TeamController extends Controller
{
    /**
     * Display a listing of the teams.
     *
     * @param  Request  $request
     * @return Response
     */
    public function fetchTeams(Request $request)
    {
        try {
            $limit = $request->limit ?? 10;
            $name = $request->name;
            $company_id = $request->company_id;

            $teams = Team::withoutTrashed()
                ->when($name ?? false,
                    fn($query, $name) => $query->where('name', 'like', '%' . $name . '%')
                        ->orWhereHas('company', fn($query) => $query->where('name', 'like', '%' . $name . '%'))
                )
                ->when($company_id ?? false, fn($query, $company_id) => $query->where('company_id', (int) $company_id))
                ->with('company:id,name,logo')
                ->paginate($limit);

            return ResponseFormatter::success($teams, 'Teams Found');
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
    public function createTeam(CreateRequest $request)
    {
        try {
            $validated = $request->validated();

            if (isset($request->icon)) {
                $uploadedFile = $request->file('icon')->store('public/company');
                $validated['icon'] = str_replace('public/', 'storage/', $uploadedFile);
            }

            $team = Team::create([
                'name' => $validated['name'],
                'company_id' => $validated['company_id'],
                'icon' => $validated['icon'] ?? null,
            ]);

            $team->load('company');

            return ResponseFormatter::success($team, 'Team Created');
        } catch (\Exception $e) {
            return ResponseFormatter::error($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Update the specified team in storage.
     *
     * @param  UpdateRequest  $request
     * @param  Team  $id
     * @return Response
     */
    public function updateTeam(UpdateRequest $request, Team $id)
    {
        try {
            if (!($id instanceof Team)) throw new Exception('Company not found', 404);

            $validated = $request->validated();
            $id->update($validated);

            $id->load('company');

            return ResponseFormatter::success($id, 'Team Updated');
        } catch (\Exception $e) {
            return ResponseFormatter::error($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Remove the specified team from storage.
     *
     * @param  Team  $id
     * @return Response
     */
    public function deleteTeam(Team $id)
    {
        try {
            if (!($id instanceof Team)) throw new Exception('Team not found', 404);

            $team = $id->toArray();
            $id->delete();

            return ResponseFormatter::success($team, 'Team Deleted');
        } catch (\Exception $e) {
            return ResponseFormatter::error($e->getMessage(), $e->getCode());
        }
    }
}
