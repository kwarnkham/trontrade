<?php

namespace App\Http\Controllers;

use App\Constants\ResponseStatus;
use App\Models\Agent;
use Illuminate\Http\Request;

class AgentController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'unique:agents,name'],
            'ip' => ['required', 'ip'],
            'remark' => ['string']
        ]);

        $agent = Agent::make($data['name'], $data['ip'], $data['remark']);

        return response()->json($agent, ResponseStatus::CREATED);
    }

    public function toggleBlock(Request $request, Agent $agent)
    {
        $agent->status = $agent->status == 1 ? 2 : 1;
        $agent->save();
        return response()->json($agent);
    }

    public function index(Request $request)
    {
        return response()->json(Agent::whereNotIn('id', [1, 2])->paginate((int)$request->per_page ?? 15));
    }

    public function resetAgentKey(Request $request, Agent $agent)
    {
        if (!$agent->resetKey()) abort(ResponseStatus::SERVER_ERROR);
        return response()->json($agent);
    }
}
