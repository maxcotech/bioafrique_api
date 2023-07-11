<?php

namespace App\Traits;

use App\Http\Resources\CurrencyResource;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

trait HasProductFilters
{
    use HasResourceStatus, HasArrayOperations, HasRateConversion, HasCategory;
    use HasRoles, HasStore;

    protected function getStoreValidationRule($auth_type, $user)
    {
        $nullable_rule = "nullable|integer|exists:stores,id";
        if ($auth_type->type == User::auth_type && isset($user)) {
            $user_type = $user->user_type;
            if ($this->isStoreOwner($user_type) || $this->isStoreStaff($user_type)) {
                return $this->storeIdValidationRule();
            } else {
                return $nullable_rule;
            }
        }
        return $nullable_rule;
    }


    protected function filterByRating($query)
    {
        $rating = $this->request->query('rating', null);
        if (isset($rating) && $rating !== 0 && $rating !== "0") {
            $selected_ids = [];
            Product::where('product_status', $this->getResourceActiveId())
                ->chunkById(100, function ($products) use ($rating, &$selected_ids) {
                    foreach ($products as $product) {
                        $review_average = $product->reviews()
                            ->where('status', $this->getResourceActiveId())
                            ->avg('star_rating');
                        if ($review_average >= $rating) {
                            if (!in_array($product->id, $selected_ids)) {
                                array_push($selected_ids, $product->id);
                            }
                        }
                    }
                });
            return $query->whereIn('id', $selected_ids);
        }
        return $query;
    }

    protected function filterByProductStatus($query, $auth_type, $user)
    {
        if ($auth_type->type == User::auth_type && isset($user)) {
            $user_type = $user->user_type;
            if ($this->isStoreOwner($user_type) || $this->isStoreStaff($user_type) || $this->isSuperAdmin($user_type)) {
                $status = $this->request->query('status', null);
                if ($status != null) {
                    $query = $query->where('product_status', $status);
                }
            } else {
                $query = $query->where('product_status', $this->getResourceActiveId());
            }
        } else {
            $query = $query->where('product_status', $this->getResourceActiveId());
        }
        return $query;
    }

    protected function filterBySearchQuery($query)
    {
        $search = $this->request->query('query', null);
        if (isset($search)) {
            return $query->where('product_name', 'LIKE', "%$search%");
        }
        return $query;
    }

    protected function filterByLocation($query)
    {
        $country_id = $this->request->query('country', null);
        $state_id = $this->request->query('state', null);
        $city_id = $this->request->query('city', null);
        $store_query = null;
        if (isset($country_id)) {
            $store_query = Store::where('country_id', $country_id);
            if (isset($state_id)) {
                $store_query = $store_query->where('state_id', $state_id);
            }
            if (isset($city_id)) {
                $store_query = $store_query->where('city_id', $city_id);
            }
            $store_ids = json_decode(json_encode($store_query->pluck('id')), true);
            return $query->whereIn('store_id', $store_ids);
        }
        return $query;
    }

    protected function getProductFilterArray($category_id = null, $search_query = null)
    {
        $category_filters = [];
        $cat_select = ['id', 'category_title', 'display_title', 'category_slug', 'category_image', 'category_icon'];
        $query = Product::select('id', 'regular_price', 'sales_price', 'category_id', 'brand_id');
        if (isset($search_query)) {
            $query = $query->where('product_name', 'LIKE', "%$search_query%");
        }
        if (isset($category_id)) {
            $category = Category::find($category_id);
            $category_filters['main_category'] = $category;
            $category_filters['categories'] = $category->subCategories()
                ->where('status', $this->getResourceActiveId())->get($cat_select);
            if (isset($category)) {
                if (Category::where('parent_id', $category->id)->exists()) {
                    $sub_cats = $this->getAllCategoryDescendants($category);
                    $cat_ids = $this->extractUniqueValueList($sub_cats, 'id');
                    array_push($cat_ids, $category->id);
                    $query = $query->whereIn('category_id', $cat_ids);
                } else {
                    $query = $query->where('category_id', $category->id);
                }
            }
        } else {
            $category_filters['categories'] = Category::where('category_level', Category::MAIN_CATEGORY_LEVEL)
                ->where('status', $this->getResourceActiveId())->get($cat_select);
        }
        $products = $query->get();
        $cookie = $this->getUserByCookie();
        $user = Auth::user();
        $currency = $this->getUserCurrency($user, $cookie);
        $category_filters['brands'] = $this->getBrandFilters($products);
        $category_filters['price_range'] = $this->getPriceFilters($products);
        $category_filters['currency'] = (isset($currency)) ? new CurrencyResource($currency) : null;
        return $category_filters;
    }

    protected function getBrandFilters($products)
    {
        $brand_ids = $this->extractUniqueValueList($products, 'brand_id');
        return Brand::select('id', 'brand_name', 'brand_logo')
            ->whereIn('id', $brand_ids)->get();
    }

    protected function getPriceFilters($products)
    {
        $max_price = 0;
        $min_price = 0;
        foreach ($products as $product) {
            $price = 0;
            if ($product->sales_price != null && $product->sales_price !== 0) {
                $price = $product->sales_price;
            } else {
                $price = $product->regular_price;
            }
            if ($price > $max_price) $max_price = $price;
            if ($min_price == 0) {
                $min_price = $price;
            } else {
                if ($price < $min_price && $price > 0) $min_price = $price;
            }
        }
        return [
            'min_price' => $min_price,
            'max_price' => $max_price
        ];
    }


    protected function selectFields($query)
    {
        $withFields = [
            'variations:id,regular_price,sales_price,variation_name,product_id,variation_image,amount_in_stock',
            'store:id,city_id,country_id,state_id,store_name',
            'store.city:id,city_name',
            'store.state:id,state_name',
            'store.country:id,country_name'
        ];
        $query = $query->with($withFields);
        return $query;
    }

    protected function filterByBrandAndStore($query)
    {
        if ($this->request->query('brand') !== null) {
            $query->where('brand_id', $this->request->query('brand'));
        }
        if ($this->request->query('store') !== null) {
            $query->where('store_id', $this->request->query('store'));
        }
        return $query;
    }


    protected function filterByPrice($query)
    {
        if ($this->request->query('min_price') != null && $this->request->query('max_price') != null) {
            $real_max = $this->userToBaseCurrency($this->request->query('max_price'));
            $real_min = $this->userToBaseCurrency($this->request->query('min_price'));
            $query->where(function ($query) use ($real_max, $real_min) {
                $query->whereBetween('sales_price', [$real_min, $real_max])
                    ->orWhereBetween('regular_price', [$real_max, $real_min]);
            });
        }
        return $query;
    }
}
