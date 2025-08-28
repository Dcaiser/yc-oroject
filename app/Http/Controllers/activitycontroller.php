<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    /**
     * Tampilkan semua aktivitas.
     */
    public function index()
    {
        $activities = Activity::latest()->paginate(10); // pakai pagination biar rapi
        return view('activities.index', compact('activities'));
    }

    /**
     * Simpan aktivitas baru (bisa dipanggil dari controller lain).
     */
    public function store($user, $action, $model = null, $record_id = null)
    {
        Activity::create([
            'user' => $user,
            'action' => $action,
            'model' => $model,
            'record_id' => $record_id,
        ]);
    }

    /**
     * Hapus semua aktivitas.
     */
    public function clear()
    {
        Activity::truncate();
        return redirect()->route('activities.index')->with('success', 'Semua aktivitas berhasil dihapus!');
    }
}
