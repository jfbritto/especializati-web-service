<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Http\Requests\StoreUpdateProductFormRequest;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    private $product;
    private $totalPage = 10;
    private $path = 'products';

    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    public function index(Request $request)
    {
        $products = $this->product->getResults($request->all(), $this->totalPage);

        return response()->json($products, 200);
    }

    public function store(StoreUpdateProductFormRequest $request)
    {
        $data = $request->all();

        if($request->hasFIle('image') && $request->file('image')->isValid()) {
            $name = md5(date('YmdHis').$request->name.rand(1,100));
            $extension = $request->image->extension();

            $nameFile = $name.'.'.$extension;
            $data['image'] = $nameFile;
            
            $upload = $request->image->storeAs($this->path, $nameFile);
            
            if(!$upload)
                return response()->json(['error' => 'Fail_Upload'], 500);
        }

        $product = $this->product->create($data);
        
        return response()->json($product, 201);
    }

    public function show($id)
    {
        if(!$product = $this->product->with(['category'])->find($id))
            return response()->json(['error' => 'Not found'], 404);
        
        return response()->json($product, 200);
    }

    public function update(StoreUpdateProductFormRequest $request, $id)
    {
        if(!$product = $this->product->find($id))
            return response()->json(['error' => 'Not found'], 404);

        $data = $request->all();

        if($request->hasFIle('image') && $request->file('image')->isValid()) {

            if($product->image) {
                $path = $this->path."/".$product->image;
                if(Storage::exists($path))
                    Storage::delete($path);
            }

            $name = md5(date('YmdHis').$request->name.rand(1,100));
            $extension = $request->image->extension();

            $nameFile = $name.'.'.$extension;
            $data['image'] = $nameFile;
            
            $upload = $request->image->storeAs('products', $nameFile);
            
            if(!$upload)
                return response()->json(['error' => 'Fail_Upload'], 500);
        }

        $product->update($data);

        return response()->json($product, 200);
    }

    public function destroy($id)
    {
        if(!$product = $this->product->find($id))
            return response()->json(['error' => 'Not found'], 404);
        
        if($product->image) {
            $path = $this->path."/".$product->image;
            if(Storage::exists($path))
                Storage::delete($path);
        }

        $product->delete();

        return response()->json(['success' => true], 204);
    }
}
