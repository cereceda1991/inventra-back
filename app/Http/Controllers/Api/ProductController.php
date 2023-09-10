<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    
    public function index()
    {
        $perPage = 50; // Número de productos por página
        $products = Product::paginate($perPage);
    
        $response = [
            'status' => 'success',
            'message' => 'Products found!',
            'data' => [
                'products' => $products->items(),
                'currentPage' => $products->currentPage(),
                'perPage' => $products->perPage(),
                'totalPages' => $products->lastPage(),
                'totalCount' => $products->total(),
            ],
        ];
    
        return response()->json($response, Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        // Definir las reglas de validación
        $rules = [
            'SKU' => 'required|unique:products',
            'description' => 'required',
            'category' => 'required',
            'unit' => 'required',
            'stock'=>'required',
            'image_url' => 'required|url',
            'price' => 'required|numeric',
        ];
    
        // Validar los datos del formulario
        $validator = Validator::make($request->all(), $rules);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], Response::HTTP_BAD_REQUEST);
        }
    
        // Crear un nuevo producto y llenarlo con los datos de la solicitud
        $product = new Product($request->all());
        $product->save();

        $response = [
                    'status' => 'success',
                    'message' => 'Product created successfully!',
                    'data' => $product,
                ];
    
        return response()->json($response, Response::HTTP_CREATED);

    }

    public function show($id)
    {
    // Buscar el producto por su ID
    $product = Product::find($id);

    if (!$product) {
        // Si el producto no se encuentra, retornar una respuesta de error
        $response = [
            'status' => 'error',
            'message' => 'Product not found.',
        ];

        return response()->json($response, Response::HTTP_NOT_FOUND);
    }

    // Si se encuentra el producto, retornar una respuesta exitosa
    $response = [
        'status' => 'success',
        'message' => 'Product found!',
        'data' => ['product' => $product],
    ];

    return response()->json($response, Response::HTTP_OK);
    }
    
    public function update(Request $request, $id)
    {
    try {
        // Buscar el producto por su ID
        $product = Product::find($id);

        if (!$product) {
            // Si el producto no se encuentra, retornar una respuesta de error
            $response = [
                'status' => 'error',
                'message' => 'Product not found.',
            ];

            return response()->json($response, Response::HTTP_NOT_FOUND);
        }

        // Verificar si el campo SKU está presente en la solicitud
        if ($request->has('SKU')) {
            // Si el SKU está presente, valida su unicidad
            $validator = Validator::make($request->all(), [
                'SKU' => [
                    'required',
                    Rule::unique('products')->ignore($product->id),
                ],
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], Response::HTTP_BAD_REQUEST);
            }

            // Actualizar el campo SKU solo si es único
            $product->SKU = $request->SKU;
        }

        // Validar otros campos y actualizarlos si es necesario
        $validationRules = [
            'description' => 'required',
            'category' => 'required',
            'unit' => 'required',
            'stock' => 'required',
            'image_url' => 'required|url',
            'price' => 'required|numeric',
            'stock_min' => 'nullable|numeric',
            'stock_max' => 'nullable|numeric',
        ];

        $validator = Validator::make($request->all(), $validationRules);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], Response::HTTP_BAD_REQUEST);
        }

        // Actualizar otros campos del producto
        $product->fill($request->except('SKU'));
        $product->save();

        // Retornar una respuesta exitosa
        $response = [
            'status' => 'success',
            'message' => 'Product has been successfully updated.',
            'data' => ['product' => $product],
        ];

        return response()->json($response, Response::HTTP_OK);
    } catch (\Exception $e) {
        return response()->json(['message' => $e->getMessage(), 'type' => 'error'], 500);
    }
    }
    
    public function destroy($id)
    {
        $product = product::findOrFail($id);
        $product->delete();
    
        return response()->json(['message' => 'product deleted'], Response::HTTP_OK);
    }
}
