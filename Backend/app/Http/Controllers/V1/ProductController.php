<?php

namespace App\Http\Controllers\V1;

use App\Http\Requests\V1\StoreProductRequest;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    /**
     * Listar produtos
     *
     * Retorna uma lista de produtos cadastrados.
     * 
     * Quando `pagination=false`, retorna uma lista simples (máximo 20 produtos) sem paginação.
     * Quando não enviado ou `pagination=true`, retorna dados paginados (padrão: 15 por página).
     * 
     * É possível realizar uma busca utilizando o parâmetro `search`,
     * que será aplicado no campo `nome`.
     *
     * Exemplo:
     * - /products (paginado)
     * - /products?pagination=false (sem paginação, máx 20)
     * - /products?search=notebook
     * - /products?search=notebook&pagination=false
     *
     * @param Request $request
     * @queryParam pagination string Opcional. Se false, retorna dados sem paginação (máx 20). Default: true. Example: false
     * @queryParam search string Opcional. Termo para busca por nome do produto. Example: notebook
     * @queryParam page int Opcional. Página (apenas com pagination=true). Example: 1
     * @queryParam per_page int Opcional. Itens por página (apenas com pagination=true). Default: 15. Example: 15
     * 
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $search = trim(strtolower($request->input('search', '')));
        $pagination = $request->input('pagination', 'true') !== 'false';

        if ($pagination) {
            // Com paginação (comportamento original)
            $page = (int) $request->input('page', 1);
            $perPage = (int) $request->input('per_page', 15);
            $userId = auth()->id();

            $cacheKey = "produtos:user:{$userId}:page:{$page}:per_page:{$perPage}:search:{$search}";

            $products = Cache::remember($cacheKey, 60, function () use ($search, $perPage, $userId) {
                $query = Product::where('user_id', $userId);

                if ($search !== '') {
                    $query->whereLike('nome', "%{$search}%");
                }

                return $query->paginate($perPage);
            });

            // Adaptar saída para os campos esperados pelo frontend
            $products->getCollection()->transform(function (Product $product) {
                $data = $product->toArray();
                $data["created_at"] = Carbon::parse($product->created_at)->format('Y-m-d');
                $data["updated_at"] = Carbon::parse($product->updated_at)->format('Y-m-d');

                // Formatação monetária para exibição (R$ 100,00)
                $data["custo_medio"] = 'R$ ' . number_format((float) $product->custo_medio, 2, ',', '.');
                $data["preco_venda"] = 'R$ ' . number_format((float) $product->preco_venda, 2, ',', '.');

                return $data;
            });

            return $this->jsonResponse($products);
        }

        $userId = auth()->id();
        $cacheKey = "produtos:user:{$userId}:sem_paginacao:search:{$search}";

        $products = Cache::remember($cacheKey, 60, function () use ($search, $userId) {
            $query = Product::where('user_id', $userId);

            if ($search !== '') {
                $query->whereLike('nome', "%{$search}%");
            }

            return $query->limit(20)->get();
        });

        // Adaptar saída para os campos esperados pelo frontend
        $products->transform(function (Product $product) {
            return [
                'id' => $product->id,
                'nome' => $product->nome,
                'estoque' => $product->estoque,
                'custo_medio' => $product->custo_medio,
                'preco_venda' => $product->preco_venda,
            ];
        });

        return $this->jsonResponse(['data' => $products]);
    }

    /**
     * Criar produto
     *
     * Cria um novo produto no sistema.
     *
     * Regras:
     * - `nome` é obrigatório e deve ter ao menos 3 caracteres
     * - `preco_venda` é obrigatório e deve ser maior que zero
     * - O estoque inicial é definido como 0
     * - O custo médio é iniciado como 0
     *
     * @param StoreProductRequest $request
     *
     * @bodyParam nome string required Nome do produto. Example: "Notebook Dell".
     * @bodyParam preco_venda number required Preço de venda sugerido. Example: 250.00
     *
     * @return JsonResponse
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        try {
            $product = Product::create([
                'user_id' => auth()->id(),
                'nome' => $request->input('nome'),
                'estoque' => 0,
                'custo_medio' => 0,
                'preco_venda' => $request->input('preco_venda'),
            ]);

            // Limpa cache para garantir que a lista de produtos seja atualizada
            Cache::flush();

            return $this->jsonResponse($product, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            Log::error("Error creating product: " . $e->getMessage());

            return $this->errorResponse('Erro ao criar produto', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Exibir produto
     *
     * Retorna os detalhes de um produto específico com base no seu ID.
     *
     * Caso o produto não seja encontrado, será retornado erro 404.
     *
     * @param string $productId
     * @urlParam productId string required ID do produto. Example: 12
     * 
     * @return JsonResponse
     */
    public function show(string $productId): JsonResponse
    {
        $userId = auth()->id();
        $product = Cache::remember("produto:user:{$userId}:{$productId}", 60, function () use ($productId, $userId) {
            return Product::where('user_id', $userId)->find($productId);
        });

        if (empty($product)) {
            return $this->errorResponse("Product not found", Response::HTTP_NOT_FOUND);
        }

        return $this->jsonResponse($product);
    }

    /**
     * Atualizar produto
     *
     * Atualiza os dados de um produto existente.
     *
     * Os campos que podem ser atualizados são:
     * - `nome`
     * - `preco_venda`
     *
     * Caso o produto não seja encontrado, será retornado erro 404.
     *
     * @param string $productId
     * @param Request $request
     *
     * @urlParam productId string required ID do produto. Example: 12
     *
     * @bodyParam nome string Nome do produto. Example: "Notebook Dell Inspiron".
     * @bodyParam preco_venda number Preço de venda sugerido. Example: 350.00
     *
     * @return JsonResponse
     */
    public function update(string $productId, Request $request): JsonResponse
    {
        $product = Product::where('user_id', auth()->id())->find($productId);

        if (empty($product)) {
            return $this->errorResponse("Product not found", Response::HTTP_NOT_FOUND);
        }

        $product->update([
            'nome' => $request->input('nome', $product->nome),
            'preco_venda' => $request->input('preco_venda', $product->preco_venda),
        ]);

        // Limpa cache para garantir que os dados atualizados sejam retornados
        Cache::flush();

        return $this->jsonResponse($product);
    }

    /**
     * Remover produto
     *
     * Remove um produto do sistema com base no seu ID.
     *
     * Caso o produto não exista, será retornado erro 404.
     *
     * @param string $productId
     * 
     * @urlParam productId string required ID do produto. Example: 12
     * 
     * @return JsonResponse
     */
    public function destroy(string $productId): JsonResponse
    {
        $product = Product::where('user_id', auth()->id())->find($productId);

        if (empty($product)) {
            return $this->errorResponse("Product not found", Response::HTTP_NOT_FOUND);
        }

        $product->delete();

        // Limpa cache para garantir que a lista de produtos seja atualizada
        Cache::flush();

        return $this->jsonResponse([], Response::HTTP_NO_CONTENT);
    }
}
