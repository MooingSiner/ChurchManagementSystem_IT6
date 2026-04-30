<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Members;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MemberController extends Controller
{
    public function index()
    {
        $membersQuery = Members::with(['ministries']);

        if (request()->boolean('only_archived')) {
            $membersQuery->where('is_archived', true);
        } elseif (! request()->boolean('include_archived')) {
            $membersQuery->where('is_archived', false);
        }

        $members = $membersQuery
            ->orderBy('member_fname')
            ->orderBy('member_lname')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $members,
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'member_fname' => 'required|string|max:255',
            'member_mname' => 'nullable|string|max:255',
            'member_lname' => 'required|string|max:255',
            'gender' => 'required|string|max:10',
            'birth_date' => 'required|date',
            'email' => 'required|email|unique:members,email',
            'phone_number' => 'required|string|max:20|unique:members,phone_number',
            'street' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'ministry_id' => 'nullable|integer|exists:ministries,ministry_id',
            'ministry_status' => ['nullable', Rule::in(['active', 'inactive', 'left'])],
        ]);

        $member = Members::create([
            'member_fname' => $validatedData['member_fname'],
            'member_mname' => $validatedData['member_mname'] ?? null,
            'member_lname' => $validatedData['member_lname'],
            'gender' => $validatedData['gender'],
            'birth_date' => $validatedData['birth_date'],
            'email' => $validatedData['email'],
            'phone_number' => $validatedData['phone_number'],
            'street' => $validatedData['street'] ?? null,
            'city' => $validatedData['city'] ?? null,
            'province' => $validatedData['province'] ?? null,
        ]);

       

        if (!empty($validatedData['ministry_id'])) {
            $member->ministries()->attach($validatedData['ministry_id'], [
                'date_joined' => today(),
                'status' => $validatedData['ministry_status'] ?? 'active',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Member created successfully.',
            'data' => $member->load(['ministries']),
        ], 201);
    }

    public function show($id)
    {
        $member = Members::with(['ministries'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $member,
        ]);
    }

    public function update(Request $request, $id)
    {
        $member = Members::findOrFail($id);

        $validatedData = $request->validate([
            'member_fname' => 'required|string|max:255',
            'member_mname' => 'nullable|string|max:255',
            'member_lname' => 'required|string|max:255',
            'gender' => 'required|string|max:10',
            'birth_date' => 'required|date',
            'email' => 'required|email|unique:members,email,' . $id . ',member_id',
            'phone_number' => 'required|string|max:20|unique:members,phone_number,' . $id . ',member_id',
            'street' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'ministry_id' => 'nullable|integer|exists:ministries,ministry_id',
            'ministry_status' => ['nullable', Rule::in(['active', 'inactive', 'left'])],
        ]);

        $member->update([
            'member_fname' => $validatedData['member_fname'],
            'member_mname' => $validatedData['member_mname'] ?? null,
            'member_lname' => $validatedData['member_lname'],
            'gender' => $validatedData['gender'],
            'birth_date' => $validatedData['birth_date'],
            'email' => $validatedData['email'],
            'phone_number' => $validatedData['phone_number'],
            'street' => $validatedData['street'] ?? null,
            'city' => $validatedData['city'] ?? null,
            'province' => $validatedData['province'] ?? null,
        ]);

    

        if (!empty($validatedData['ministry_id'])) {
            $existingMinistry = $member->ministries()
                ->where('ministries.ministry_id', $validatedData['ministry_id'])
                ->first();

            $member->ministries()->sync([
                $validatedData['ministry_id'] => [
                    'date_joined' => $existingMinistry?->pivot?->date_joined ?? today(),
                    'status' => $validatedData['ministry_status'] ?? 'active',
                ],
            ]);
        } else {
            $member->ministries()->detach();
        }

        return response()->json([
            'success' => true,
            'message' => 'Member updated successfully.',
            'data' => $member->load(['ministries']),
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $member = Members::findOrFail($id);

        $member->update([
            'is_archived' => true,
            'archived_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Member archived successfully.',
        ]);
    }

    public function restore($id)
    {
        $member = Members::findOrFail($id);

        $member->update([
            'is_archived' => false,
            'archived_at' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Member restored successfully.',
        ]);
    }
}
