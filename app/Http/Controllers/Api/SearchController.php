<?php

namespace App\Http\Controllers\Api;

use App\Actions\Search\SearchResource;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\SearchHistory;
use App\Traits\HasAuthStatus;
use App\Traits\HasHttpResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    use HasHttpResponse, HasAuthStatus;

    public function index(Request $request, $search_type)
    {
        return (new SearchResource($request, $search_type))->execute();
    }

    public function generalSearch(Request $request)
    {
        try {
            $query = $request->query("query", "");
            $limit = $request->query("limit", 20);
            $skip = $request->query('skip', 0);
            $data = [
                'products' => [],
                'categories' => []
            ];
            if (isset($query) && $query !== "") {
                $data['products'] = Product::where('product_name', 'LIKE', "%$query%")
                    ->select('product_name', 'id', 'product_slug')
                    ->skip($skip)->take($limit)->get();
                $data['categories'] = Category::where(function ($builder) use ($query) {
                    return $builder->where('category_title', 'LIKE', "%$query%")
                        ->orWhere('display_title', 'LIKE', "%$query%");
                })
                    ->select('category_title', 'category_slug', 'category_icon', 'id')
                    ->skip($skip)->take($limit)->get();
            }
            return $this->successWithData($data);
        } catch (\Exception $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function saveSearch(Request $request)
    {
        try {
            $query = $request->query;
            $auth_type_obj = $this->getUserAuthTypeObject();
            if (isset($query) && isset($auth_type_obj)) {
                SearchHistory::updateOrCreate(['query' => $query], [
                    'auth_type' => $auth_type_obj['type'],
                    'user_id' => $auth_type_obj['id']
                ]);
            }
            return $this->successMessage('Search query saved.');
        } catch (\Exception $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function fetchSearchHistory(Request $request)
    {
        try {
            $limit = $request->query('limit', 30);
            $skip = $request->query('skip', 0);
            $auth_type_obj = $this->getUserAuthTypeObject();
            $data = [];
            if (isset($auth_type_obj)) {
                $data = SearchHistory::where('auth_type', $auth_type_obj['type'])
                    ->select('query', 'id')
                    ->where('user_id', $auth_type_obj['id'])
                    ->orderBy('updated_at', 'desc')
                    ->skip($skip)->take($limit)->get();
            }
            return $this->successWithData($data);
        } catch (\Exception $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function deleteSearchHistory(Request $request)
    {
        try {
            $auth_type_obj = $this->getUserAuthTypeObject();
            $id = $request->query('id');
            if (isset($auth_type_obj)) {
                $builder = SearchHistory::where('auth_type', $auth_type_obj['type'])
                    ->where('user_id', $auth_type_obj['type']);
                if (isset($id)) {
                    $builder = $builder->where('id', $id);
                }
                $builder->delete();
                return $this->successMessage("search history deleted");
            }
            return $this->internalError("Failed to delete history");
        } catch (\Exception $e) {
            return $this->internalError($e->getMessage());
        }
    }
}
