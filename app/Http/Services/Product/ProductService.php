<?php 

namespace App\Http\Services\Product;

use \Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductService
{
    public function getAll(): LengthAwarePaginator
    {
        $query = Product::query()->orderBy('id', 'asc');

        return $query->paginate(Product::PAGINATE);
    }

    public function all(Request $request)
    {
        $limit = $request->integer('limit', 10);
        $offset = $request->integer('offset', 0);
        $search = $request->query('search');

        $query = Product::query()
            ->when($search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            });

        $total = $query->count();

        $products = $query
            ->skip($offset)
            ->take($limit)
            ->get();

        return response()->json([
            'total' => $total,
            'rows' => $products,
        ]);
    }

    public function getById(int $id): Product|null
    {
        $product = Product::find($id);

        if (! $product) {
            throw new NotFoundHttpException('Product not found!');
        }

        return $product;
    }

    public function create(array $data): Product
    {
        return Product::create($data);
    }

    public function update(UpdateProductRequest $request, int $id): Product|null
    {
        $product = $this->getById($id);
        $data = $request->validated();

        foreach ($data as $key => $value) {
            if ($value === null) {
                unset($data[$key]);
            }
        }

        $product->fill($data);
        $product->save();

        return $product;
    }

    public function delete(int $id): JsonResponse
    {
        $product = $this->getById($id);

        $product->delete();

        return response()->json(['message' => 'Product deleted successfully.'], 200);
    }
}