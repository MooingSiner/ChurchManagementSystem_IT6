<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class MemberController extends Controller
{
    protected function loadMinistries(Collection $members): Collection
    {
        $memberIds = $members->pluck('member_id')->filter()->values();

        $ministriesByMember = DB::table('members_ministries')
            ->join('ministries', 'members_ministries.ministry_id', '=', 'ministries.ministry_id')
            ->whereIn('members_ministries.member_id', $memberIds)
            ->select(
                'members_ministries.member_id',
                'ministries.ministry_id',
                'ministries.ministry_name',
                'members_ministries.date_joined',
                'members_ministries.status'
            )
            ->get()
            ->groupBy('member_id');

        return $members->map(function ($member) use ($ministriesByMember) {
            $member->ministries = collect($ministriesByMember->get($member->member_id, []))->map(function ($ministry) {
                $ministry->pivot = (object) [
                    'date_joined' => $ministry->date_joined,
                    'status' => $ministry->status,
                ];

                return $ministry;
            })->values();

            return $member;
        });
    }

    protected function membersQuery(Request $request)
    {
        $search = trim((string) $request->query('search', $request->query('member_search', '')));
        $ministryId = $request->query('ministry_id');
        $gender = $request->query('gender');
        $city = $request->query('city');
        $province = $request->query('province');

        $membersQuery = DB::table('members')
            ->leftJoin('members_ministries', 'members.member_id', '=', 'members_ministries.member_id')
            ->leftJoin('ministries', 'members_ministries.ministry_id', '=', 'ministries.ministry_id')
            ->select('members.*')
            ->distinct();

        if ($request->boolean('only_archived')) {
            $membersQuery->where('is_archived', true);
        } elseif (! $request->boolean('include_archived')) {
            $membersQuery->where('is_archived', false);
        }

        return $membersQuery
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('member_fname', 'like', "%{$search}%")
                        ->orWhere('member_mname', 'like', "%{$search}%")
                        ->orWhere('member_lname', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone_number', 'like', "%{$search}%")
                        ->orWhere('street', 'like', "%{$search}%")
                        ->orWhere('city', 'like', "%{$search}%")
                        ->orWhere('province', 'like', "%{$search}%")
                        ->orWhere('ministries.ministry_name', 'like', "%{$search}%");
                });
            })
            ->when($ministryId, fn ($query) => $query->where('members_ministries.ministry_id', $ministryId))
            ->when($gender, fn ($query) => $query->where('gender', $gender))
            ->when($city, fn ($query) => $query->where('city', 'like', "%{$city}%"))
            ->when($province, fn ($query) => $query->where('province', 'like', "%{$province}%"));
    }

    public function index(Request $request)
    {
        $sorts = [
            'first_name' => 'member_fname',
            'last_name' => 'member_lname',
            'created_at' => 'created_at',
            'archived_at' => 'archived_at',
        ];
        $sortBy = $sorts[$request->query('sort_by', 'first_name')] ?? 'member_fname';
        $sortDirection = $request->query('sort_direction') === 'desc' ? 'desc' : 'asc';

        $membersQuery = $this->membersQuery($request)
            ->orderBy($sortBy, $sortDirection)
            ->orderBy('member_lname');

        if ($request->filled('per_page')) {
            $perPage = min(max((int) $request->integer('per_page', 15), 1), 100);
            $members = $membersQuery->paginate($perPage)->withQueryString();
            $members->setCollection($this->loadMinistries($members->getCollection()));

            return response()->json([
                'success' => true,
                'data' => $members->items(),
                'meta' => [
                    'current_page' => $members->currentPage(),
                    'per_page' => $members->perPage(),
                    'total' => $members->total(),
                    'last_page' => $members->lastPage(),
                ],
            ]);
        }

        $members = $this->loadMinistries($membersQuery->get());

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

        $memberId = DB::transaction(function () use ($validatedData) {
            $memberId = DB::table('members')->insertGetId([
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
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if (! empty($validatedData['ministry_id'])) {
                DB::table('members_ministries')->insert([
                    'member_id' => $memberId,
                    'ministry_id' => $validatedData['ministry_id'],
                    'date_joined' => today(),
                    'status' => $validatedData['ministry_status'] ?? 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            return $memberId;
        });

        $member = $this->loadMinistries(collect([
            DB::table('members')->where('member_id', $memberId)->first(),
        ]))->first();

        return response()->json([
            'success' => true,
            'message' => 'Member created successfully.',
            'data' => $member,
        ], 201);
    }

    public function show($id)
    {
        $member = DB::table('members')->where('member_id', $id)->firstOrFail();
        $member = $this->loadMinistries(collect([$member]))->first();

        return response()->json([
            'success' => true,
            'data' => $member,
        ]);
    }

    public function update(Request $request, $id)
    {
        $existingPivotDate = DB::table('members_ministries')->where('member_id', $id)->value('date_joined');
        DB::table('members')->where('member_id', $id)->firstOrFail();

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

        DB::transaction(function () use ($validatedData, $id, $existingPivotDate) {
            DB::table('members')
                ->where('member_id', $id)
                ->update([
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
                    'updated_at' => now(),
                ]);

            DB::table('members_ministries')->where('member_id', $id)->delete();

            if (! empty($validatedData['ministry_id'])) {
                DB::table('members_ministries')->insert([
                    'member_id' => $id,
                    'ministry_id' => $validatedData['ministry_id'],
                    'date_joined' => $existingPivotDate ?? today(),
                    'status' => $validatedData['ministry_status'] ?? 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });

        $member = $this->loadMinistries(collect([
            DB::table('members')->where('member_id', $id)->first(),
        ]))->first();

        return response()->json([
            'success' => true,
            'message' => 'Member updated successfully.',
            'data' => $member,
        ]);
    }

    public function destroy(Request $request, $id)
    {
        DB::table('members')
            ->where('member_id', $id)
            ->update([
                'is_archived' => true,
                'archived_at' => now(),
                'updated_at' => now(),
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Member archived successfully.',
        ]);
    }

    public function restore($id)
    {
        DB::table('members')
            ->where('member_id', $id)
            ->update([
                'is_archived' => false,
                'archived_at' => null,
                'updated_at' => now(),
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Member restored successfully.',
        ]);
    }
}
