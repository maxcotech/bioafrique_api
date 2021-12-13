<?php

namespace App\Traits;

use App\Models\Category;

trait HasCategory
{

    protected function selectByVerboseLevel(&$query, $verbose = null)
    {
        if (isset($verbose)) {
            switch ($verbose) {
                case 1:
                    $query->select('id', 'category_title', 'category_level', 'parent_id', 'category_slug');
                    break;
                case 2:
                    $query->select('id', 'category_title', 'category_level', 'parent_id', 'category_icon', 'category_slug');
                    break;
                case 3:
                    $query->select('id', 'category_title', 'category_level', 'parent_id', 'display_title', 'category_icon', 'category_slug');
                    break;
                case 4:
                    $query->select('id', 'category_title', 'category_level', 'parent_id', 'display_title', 'category_icon', 'category_image', 'category_slug');
                    break;
                default:;
            }
        }
        return $query;
    }

    protected function getAllCategoryDescendants($category)
    {
        $categories = [];
        $sub_cat_query = Category::where('parent_id',$category->id);
        $query = $this->selectByVerboseLevel($sub_cat_query,1);
        $sub_cats = $query->get();
        if (isset($sub_cats) && count($sub_cats) > 0) {
            foreach($sub_cats as $sub_cat){
                array_push($categories,$sub_cat);
                $categories = array_merge($categories,$this->getAllCategoryDescendants($sub_cat));
            }
        }
        return $categories;
    }
}
