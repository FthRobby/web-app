<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MstPangkat;
use Illuminate\Support\Facades\DB;

class MstPangkatController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = request()->get('search');
        $p = MstPangkat::paginate();
        $mstPangkat = DB::table('mst_pangkat')
            ->where('nama_pangkat', 'LIKE', '%' . $search . '%')->paginate($p->perPage());

        // dd($mstPangkat);
        return view('mst-pangkat.index', compact('mstPangkat'))
            ->with('i', (request()->input('page', 1) - 1) * $p->perPage());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $mstPangkat = new MstPangkat();
        return view('mst-pangkat.create', compact('mstPangkat'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //cek validasi
        request()->validate(MstPangkat::$rules);
        //mulai transaksi

        DB::beginTransaction();
        try {
            //simpan ke tabel kota
            DB::table('mst_pangkats')->insert([
                'nama_pangkat' => $request->nama_pangkat, 'pangkat_gol' => $request->pangkat_gol, 'created_at' => date('Y-m-d H:m:s'),
                'updated_at' => date('Y-m-d H:m:s')
            ]);
            //jika tidak ada kesalahan commit/simpan
            DB::commit();
            return redirect()->route('mst-pangkat.index')
                ->with('success', 'Master Tabel Pangkat created successfully.');
        } catch (\Throwable $e) {
            //jika terjadi kesalahan batalkan semua
            DB::rollback();
            return redirect()->route('mst-pangkat.index')->with(
                'success',
                'Penyimpanan dibatalkan semua, ada kesalahan......'
            );
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $mstPangkat = MstPangkat::find($id);
        return view('mst-pangkat.show', compact('mstPangakt'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $mstPangakt = MstPangkat::find($id);
        return view('mst-pangkat.edit', compact('mstPangkat'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        request()->validate(MstPangkat::$rules);
        //mulai transaksi
        DB::beginTransaction();
        try {
            DB::table('mst_pangkats')->where('id', $id)->update([
                'nama_pangkat' => $request->nama_pangkat,
                'pangkat_gol' => $request->pangkat_gol, 'updated_at' => date('Y-m-d H:m:s')
            ]);
            DB::commit();
            return redirect()->route('mst-pangkat.index')
                ->with('success', 'Tabel Pangkat updated successfully');
        } catch (\Throwable $e) {
            //jika terjadi kesalahan batalkan semua
            DB::rollback();
            return redirect()->route('mst-pangkat.index')->with(
                'success',
                'Tabel Pangkat Batal diubah, ada kesalahan'
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            //hapus rekaman tabel kota
            MstPangkat::find($id)->delete();
            DB::commit();
            return redirect()->route('mst-pangkat.index')
                ->with('success', 'Tabel Pangkat deleted successfully');
        } catch (\Throwable $e) {
            //jika terjadi kesalahan batalkan semua
            DB::rollback();
            return redirect()->route('mst-pangkat.index')
                ->with(
                    'success',
                    'Tabel Pangkat ada kesalahan hapus batal... '
                );
        }
    }
}
