<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CompanyController extends Controller
{    
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        $companies = Company::latest()->paginate(10);
        return view('companies.index', compact('companies'));
    }

    /**
     * create
     *
     * @return void
     */
    public function create()
    {
        return view('companies.create');
    }
    
    /**
     * store
     *
     * @param  mixed $request
     * @return void
     */
    public function store(Request $request)
    { 
        $this->validate($request, [
            'image'     => 'required|image|mimes:png,jpg,jpeg',
            'title'     => 'required',
            'content'   => 'required'
        ]);

        //upload image
        $image = $request->file('image');
        $image->storeAs('public/company', $image->hashName());

        $company = Company::create([
            'image'     => $image->hashName(),
            'title'     => $request->title,
            'content'   => $request->content
        ]);

        if($company){
            //redirect dengan pesan sukses
            return redirect()->route('companies.index')->with(['success' => 'Data Berhasil Disimpan!']);
        }else{
            //redirect dengan pesan error
            return redirect()->route('companies.index')->with(['error' => 'Data Gagal Disimpan!']);
        }
    }

    /**
     * edit
     *
     * @param  mixed $company
     * @return void
     */
    public function edit(Company $company)
    {
        return view('companies.edit', compact('company'));
    }


    /**
     * update
     *
     * @param  mixed $request
     * @param  mixed $company
     * @return void
     */
    public function update(Request $request, Company $company)
    {
        $this->validate($request, [
            'title'     => 'required',
            'content'   => 'required'
        ]);

        //get data Company by ID
        $company = Company::findOrFail($company->id);

        if($request->file('image') == "") {

            $company->update([
                'title'     => $request->title,
                'content'   => $request->content
            ]);

        } else {

        //hapus old image
        Storage::disk('local')->delete('public/company/'.$company->image);

            //upload new image
            $image = $request->file('image');
            $image->storeAs('public/company', $image->hashName());

            $company->update([
                'image'     => $image->hashName(),
                'title'     => $request->title,
                'content'   => $request->content
            ]);

        }

        if($company){
            //redirect dengan pesan sukses
            return redirect()->route('companies.index')->with(['success' => 'Data Berhasil Diupdate!']);
        }else{
            //redirect dengan pesan error
            return redirect()->route('companies.index')->with(['error' => 'Data Gagal Diupdate!']);
        }
    }

    /**
     * destroy
     *
     * @param  mixed $id
     * @return void
     */
    public function destroy($id)
    {
    $company = Company::findOrFail($id);
    Storage::disk('local')->delete('public/company/'.$company->image);
    $company->delete();

    if($company){
        //redirect dengan pesan sukses
        return redirect()->route('companies.index')->with(['success' => 'Data Berhasil Dihapus!']);
    }else{
        //redirect dengan pesan error
        return redirect()->route('companies.index')->with(['error' => 'Data Gagal Dihapus!']);
    }
    }
}
