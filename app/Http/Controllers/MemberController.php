<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class MemberController extends Controller
{
    protected function memberErrorMessage(Exception $e, string $action): string
    {
        if ($e instanceof QueryException) {
            return match (true) {
                str_contains(strtolower($e->getMessage()), 'email') => 'That email is already in use. Use a different email address or update the existing member record.',
                str_contains(strtolower($e->getMessage()), 'phone') => 'That phone number is already in use. Check the number or update the existing member record instead.',
                default => "Could not {$action} the member because the record conflicts with existing data. Review the form and try again.",
            };
        }

        if ($e instanceof ModelNotFoundException) {
            return 'That member record could not be found. Refresh the page and try again.';
        }

        return "Could not {$action} the member right now. Refresh the page and try again.";
    }

    protected function ministryOptions()
    {
        return DB::table('ministries')
            ->orderBy('ministry_name')
            ->get();
    }

    protected function loadMinistriesForMembers(Collection $members): Collection
    {
        $memberIds = $members->pluck('member_id')->filter()->values();

        $ministriesByMember = DB::table('vw_members_full')
            ->whereIn('member_id', $memberIds)
            ->whereNotNull('ministry_id')
            ->select(
                'member_id',
                'ministry_id',
                'ministry_name',
                'date_joined',
                'ministry_status as status'
            )
            ->orderBy('ministry_name')
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

    protected function filteredMembers(Request $request, bool $archived)
    {
        $search = trim((string) $request->query('member_search', ''));
        $ministryId = $request->query('ministry_id');
        $gender = $request->query('gender');

        return DB::table('vw_members_full')
            ->select(
                'member_id',
                'member_fname',
                'member_mname',
                'member_lname',
                'gender',
                'birth_date',
                'email',
                'phone_number',
                'street',
                'city',
                'province',
                'is_archived',
                'archived_at',
                'created_at',
                'updated_at'
            )
            ->distinct()
            ->where('is_archived', $archived)
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
                        ->orWhere('ministry_name', 'like', "%{$search}%");
                });
            })
            ->when($ministryId, function ($query) use ($ministryId) {
                $query->where('ministry_id', $ministryId);
            })
            ->when($gender, function ($query) use ($gender) {
                $query->where('gender', $gender);
            });
    }

    public function member()
    {
        return $this->index(request());
    }

    public function index(Request $request)
    {
        $members = $this->filteredMembers($request, false)
            ->orderBy('member_lname')
            ->orderBy('member_fname')
            ->paginate(6, ['*'], 'members_page')
            ->withQueryString();
        $members->setCollection($this->loadMinistriesForMembers($members->getCollection()));

        $archivedMembers = $this->filteredMembers($request, true)
            ->orderByDesc('archived_at')
            ->paginate(6, ['*'], 'archived_page')
            ->withQueryString();
        $archivedMembers->setCollection($this->loadMinistriesForMembers($archivedMembers->getCollection()));

        $ministries = $this->ministryOptions();

        return view('members', compact('members', 'archivedMembers', 'ministries'));
    }

    public function create()
    {
        $ministries = $this->ministryOptions();
        return view('members.create', compact('ministries'));
    }

    public function store(Request $request)
    {
        try {
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

            DB::select('CALL sp_create_member(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
                $validatedData['member_fname'],
                $validatedData['member_mname'] ?? null,
                $validatedData['member_lname'],
                $validatedData['gender'],
                $validatedData['birth_date'],
                $validatedData['email'],
                $validatedData['phone_number'],
                $validatedData['street'] ?? null,
                $validatedData['city'] ?? null,
                $validatedData['province'] ?? null,
                $validatedData['ministry_id'] ?? null,
                $validatedData['ministry_status'] ?? 'active',
            ]);

            return redirect()->back()->with('success', 'Member added successfully');
        } catch (Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $this->memberErrorMessage($e, 'add'));
        }
    }

    protected function memberWithMinistriesOrFail($id)
    {
        $member = DB::table('vw_members_full')
            ->select(
                'member_id',
                'member_fname',
                'member_mname',
                'member_lname',
                'gender',
                'birth_date',
                'email',
                'phone_number',
                'street',
                'city',
                'province',
                'is_archived',
                'archived_at',
                'created_at',
                'updated_at'
            )
            ->where('member_id', $id)
            ->first();

        if (! $member) {
            throw new ModelNotFoundException();
        }

        return $this->loadMinistriesForMembers(collect([$member]))->first();
    }

    public function show($id)
    {
        $member = $this->memberWithMinistriesOrFail($id);
        return view('members.show', compact('member'));
    }

    public function edit($id)
    {
        $member = $this->memberWithMinistriesOrFail($id);
        $ministries = $this->ministryOptions();

        return view('members.edit', compact('member', 'ministries'));
    }

    public function update(Request $request, $id)
    {
        try {
            $member = DB::table('vw_members_full')->where('member_id', $id)->first();

            if (! $member) {
                throw new ModelNotFoundException();
            }

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

            DB::select('CALL sp_update_member(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
                $id,
                $validatedData['member_fname'],
                $validatedData['member_mname'] ?? null,
                $validatedData['member_lname'],
                $validatedData['gender'],
                $validatedData['birth_date'],
                $validatedData['email'],
                $validatedData['phone_number'],
                $validatedData['street'] ?? null,
                $validatedData['city'] ?? null,
                $validatedData['province'] ?? null,
                $validatedData['ministry_id'] ?? null,
                $validatedData['ministry_status'] ?? 'active',
            ]);

            return redirect()->back()->with('success', 'Member updated successfully');
        } catch (Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $this->memberErrorMessage($e, 'update'));
        }
    }

    public function destroy($id)
    {
        try {
            $updated = DB::table('members')->where('member_id', $id)->exists();

            if (! $updated) {
                throw new ModelNotFoundException();
            }

            DB::statement('CALL sp_set_member_archive(?, ?)', [$id, 1]);

            return redirect()->back()
                ->with('error', 'Member archived successfully!');
        } catch (Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $this->memberErrorMessage($e, 'archive'));
        }
    }

    public function restore($id)
    {
        try {
            $updated = DB::table('members')->where('member_id', $id)->exists();

            if (! $updated) {
                throw new ModelNotFoundException();
            }

            DB::statement('CALL sp_set_member_archive(?, ?)', [$id, 0]);

            return redirect()->back()
                ->with('success', 'Member restored successfully!');
        } catch (Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $this->memberErrorMessage($e, 'restore'));
        }
    }
}
