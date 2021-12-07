<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; 
use Illuminate\Support\Facades\Validator;
use App\Models\Menu; 

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::all(); 

        if(count($menus)> 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $menus
            ], 200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ], 400); 

    }

    public function show($id)
    {
        $menu = Menu::find($id); 

        if(!is_null($menu)) {
            return response([
                'message' => 'Retrieve Menu Success',
                'data' => $menu
            ], 200);
        } 

        return response([
            'message' => 'Menu Not Found',
            'data' => null
        ], 404); 
    }

    public function store(Request $request)
    {
        $storeData = $request->all(); 
        $validate = Validator::make($storeData, [
            'nama_menu' => 'required|max:60|unique:menus',
            'kategori' => 'required',
            'harga' =>'required|numeric',
            'status' => 'required',
            'foto' => 'required'
        ]);

        if($validate->fails())
            return response(['message' => $validate->errors()], 400);
        
            $menu = Menu::create($storeData);
            return response([
                'message' => 'Add Menu Success',
                'data' => $menu
            ], 200); 
    }

    public function destroy($id)
    {
        $menu = Menu::find($id); 
        
        if (is_null($menu)) {
            return response([
                'message' =>'Menu Not Found',
                'data' => null
            ], 404);
        }

        if($menu->delete()) {
            return response([
                'message' =>'Delete Menu Success',
                'data' => $menu
            ], 200); 
        } 

        return response([
            'message' => 'Delete Menu Failed',
            'data' => null,
        ], 400); 

    }

    public function update(Request $request, $id)
    {
        $menu = Menu::find($id); 
        if (is_null($menu)) {
            return response([
                'message' =>'Menu Not Found',
                'data' => null
            ], 404);
        }

    
        $updateData = $request->all(); 
        $validate = Validator::make($updateData, [
            'nama_makanan' => ['max:60', 'required', Rule::unique('menus')->ignore($menu)],
            'kategori' => 'required',
            'harga' =>'required|numeric',
            'status' => 'required',
            'foto' => 'required'
        ]); 

        if($validate->fails())
            return response(['message' => $validate->errors()], 400); 
        
        $menu->nama_makanan = $updateData['nama_makanan'];
        $menu->kategori = $updateData['kategori'];
        $menu->harga = $updateData['harga'];
        $menu->status = $updateData['status'];
        $menu->foto = $updateData['foto'];

        if($menu->save()) {
            return response([
                'message' => 'Update Menu Success',
                'data' =>$menu
            ], 200);
        } 
        return response([
            'message' => 'Update Menu Failed',
            'data' => null,
        ], 400); 
    }
}
