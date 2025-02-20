<?php

namespace App\Http\Controllers;

use App\Models\Informasi;
use App\Models\JenisBantuan;
use Illuminate\Http\Request;
use \Cviebrock\EloquentSluggable\Services\SlugService;
use Illuminate\Support\Facades\Http;

class DashboardInfoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view("dashboard.informasi.index", [
            "informasi" => Informasi::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $response = Http::get('https://api.binderbyte.com/wilayah/provinsi?api_key=c21f5d686f436e800025b6154f433108667c89cd2bd8e84e852ddd5f808e7e31');
        $data = $response->json();

        return view("dashboard.informasi.create", [
            "jenisBantuan" => JenisBantuan::all(),
            "dataProvinsi" => $data["value"],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            "judul_informasi" => "required|min:5",
            "jmlh_bantuan" => "required",
            "jenisBantuan_id" => "required",
            "provinsi" => "required",
            "kabupaten" => "required",
            "kecamatan" => "required",
            "desa" => "required",
            "deskripsi" => "required|min:20",
        ]);

        $validatedData["provinsi"] = substr($validatedData["provinsi"], 2);
        $validatedData["kabupaten"] = substr($validatedData["kabupaten"], 4);
        $validatedData["kecamatan"] = substr($validatedData["kecamatan"], 6);
        $validatedData["desa"] = substr($validatedData["desa"], 10);
        $validatedData["slug"] = strtoupper(substr(md5(time()), 0, 5));

        Informasi::create($validatedData);
        return redirect("/dashboard/informasi")->with("success", "Bantuan berhasil ditambahkan");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Informasi  $informasi
     * @return \Illuminate\Http\Response
     */
    public function show(Informasi $informasi)
    {
        return view("dashboard.informasi.detail", [
            "informasi" => $informasi,
            "jenisBantuan" => JenisBantuan::where("id", $informasi->jenisBantuan_id)->get()
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Informasi  $informasi
     * @return \Illuminate\Http\Response
     */
    public function edit(Informasi $informasi)
    {
        $response = Http::get('https://api.binderbyte.com/wilayah/provinsi?api_key=c21f5d686f436e800025b6154f433108667c89cd2bd8e84e852ddd5f808e7e31');
        $data = $response->json();

        return view("dashboard.informasi.edit", [
            "informasi" => $informasi,
            "jenisBantuan" => JenisBantuan::all(),
            "dataProvinsi" => $data["value"],
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Informasi  $informasi
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Informasi $informasi)
    {
        $validatedData = $request->validate([
            "judul_informasi" => "required|min:5",
            "jmlh_bantuan" => "required",
            "jenisBantuan_id" => "required",
            "provinsi" => "required",
            "kabupaten" => "required",
            "kecamatan" => "required",
            "desa" => "required",
            "deskripsi" => "required|min:20",
        ]);

        $validatedData["provinsi"] = substr($validatedData["provinsi"], 2);
        $validatedData["kabupaten"] = substr($validatedData["kabupaten"], 4);
        $validatedData["kecamatan"] = substr($validatedData["kecamatan"], 6);
        $validatedData["desa"] = substr($validatedData["desa"], 10);
        $validatedData["slug"] = $informasi->slug;

        Informasi::where("id", $informasi->id)->update($validatedData);
        return redirect("/dashboard/informasi")->with("successUpdate", "Bantuan berhasil diperbaharui");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Informasi  $informasi
     * @return \Illuminate\Http\Response
     */
    public function destroy(Informasi $informasi)
    {
        Informasi::destroy($informasi->id);
        return redirect("/dashboard/informasi")->with("success", "Bantuan berhasil dihapus");
    }
}
