<?php

namespace Modules\Classes\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Classes\Models\Room;

class RoomController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Room::query();

        if ($request->has('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->has('building')) {
            $query->where('building', $request->input('building'));
        }

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%");
        }

        $rooms = $query->paginate($request->input('per_page', 25));

        return response()->json([
            'data' => $rooms->items(),
            'pagination' => [
                'total' => $rooms->total(),
                'per_page' => $rooms->perPage(),
                'current_page' => $rooms->currentPage(),
            ],
        ]);
    }

    public function show(Room $room): JsonResponse
    {
        $room->load('classes');

        return response()->json([
            'data' => $room,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:rooms',
            'building' => 'nullable|string|max:100',
            'capacity' => 'required|integer|min:1',
            'type' => 'required|string|in:classroom,lab,auditorium,library',
            'description' => 'nullable|string',
        ]);

        $room = Room::create($validated);

        return response()->json([
            'message' => 'Salle créée avec succès',
            'data' => $room,
        ], 201);
    }

    public function update(Request $request, Room $room): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:100|unique:rooms,name,' . $room->id,
            'building' => 'nullable|string|max:100',
            'capacity' => 'sometimes|integer|min:1',
            'type' => 'sometimes|string|in:classroom,lab,auditorium,library',
            'description' => 'nullable|string',
        ]);

        $room->update($validated);

        return response()->json([
            'message' => 'Salle mise à jour avec succès',
            'data' => $room->fresh(),
        ]);
    }

    public function destroy(Room $room): JsonResponse
    {
        $room->delete();

        return response()->json([
            'message' => 'Salle supprimée avec succès',
        ]);
    }
}
