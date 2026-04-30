<?php

namespace App\Http\Controllers;

use App\Models\Members;
use App\Models\Ministry;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Exception;

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

    public function member()
    {
        $members = Members::with(['ministries'])
            ->where('is_archived', false)
            ->orderBy('member_lname')
            ->orderBy('member_fname')
            ->paginate(6, ['*'], 'members_page')
            ->withQueryString();

        $archivedMembers = Members::with(['ministries'])
            ->where('is_archived', true)
            ->orderByDesc('archived_at')
            ->paginate(6, ['*'], 'archived_page')
            ->withQueryString();

        $ministries = Ministry::all();

        return view('members', compact('members', 'archivedMembers', 'ministries'));
    }

    public function index()
{
    $members = Members::with(['ministries'])
        ->where('is_archived', false)
        ->orderBy('member_lname')
        ->orderBy('member_fname')
        ->paginate(6, ['*'], 'members_page')
        ->withQueryString();

    $archivedMembers = Members::with(['ministries'])
        ->where('is_archived', true)
        ->orderByDesc('archived_at')
        ->paginate(6, ['*'], 'archived_page')
        ->withQueryString();

    $ministries = Ministry::all();

    return view('members', compact('members', 'archivedMembers', 'ministries'));
}

    public function create()
    {
        $ministries = Ministry::all();
        return view('members.create', compact('ministries'));
    }

    public function store(Request $request)
    {
    try{
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

        return redirect()->back()->with('success', 'Member added successfully');
        }catch(Exception $e) {
        return redirect()->back()
            ->withInput()
            ->with('error', $this->memberErrorMessage($e, 'add'));
    }
    }

    public function show($id)
    {
        $member = Members::with(['ministries'])->findOrFail($id);
        return view('members.show', compact('member'));
    }

    public function edit($id)
    {
        $member = Members::with(['ministries'])->findOrFail($id);
        $ministries = Ministry::all();

        return view('members.edit', compact('member', 'ministries'));
    }

    public function update(Request $request, $id)
    {
        try{
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

        return redirect()->back()->with('success', 'Member updated successfully');
        }catch (Exception $e) {
        return redirect()->back()
            ->withInput()
            ->with('error', $this->memberErrorMessage($e, 'update'));
    }
    }

    public function destroy($id)
{
    try{
    $member = Members::findOrFail($id);

    $member->update([
        'is_archived' => true,
        'archived_at' => now(),
    ]);

    return redirect()->back()
        ->with('error', 'Member archived successfully!');
    }catch(Exception $e) {
        return redirect()->back()
            ->withInput()
            ->with('error', $this->memberErrorMessage($e, 'archive'));
    }
}
    
public function restore($id)
{
    try{
    $member = Members::findOrFail($id);

    $member->update([
        'is_archived' => false,
        'archived_at' => null,
    ]);

    return redirect()->back()
        ->with('success', 'Member restored successfully!');
    }catch(Exception $e) {
        return redirect()->back()
            ->withInput()
            ->with('error', $this->memberErrorMessage($e, 'restore'));
    }
}
    
}
