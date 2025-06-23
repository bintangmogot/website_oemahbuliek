<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use Illuminate\Http\Request;
use App\Http\Requests\StoreShiftRequest;
use App\Http\Requests\UpdateShiftRequest;

class ShiftController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','role:admin']);
    }

    public function index()
    {
        $shifts = Shift::paginate(10);
        return view('dashboard.shift.index-shift', compact('shifts'));
    }

    public function show(Shift $shift)
    {
        return view('dashboard.shift.show-shift', compact('shift'));
    }

    public function create()
    {
        return view('dashboard.shift.create-shift');
    }

    public function store(StoreShiftRequest $request)
    {
        Shift::create($request->validated());
        return redirect()
               ->route('shift.index')
               ->with('success','Shift berhasil dibuat');
    }

    public function edit(Shift $shift)
    {
        return view('dashboard.shift.edit-shift', compact('shift'));
    }

    public function update(UpdateShiftRequest $request, Shift $shift)
    {
        $shift->update($request->validated());
        return back()->with('success','Shift berhasil diperbarui');
    }

    public function destroy(Shift $shift)
    {
        $shift->delete();
        return redirect()
               ->route('shift.index')
               ->with('success','Shift berhasil dihapus');
    }
}
