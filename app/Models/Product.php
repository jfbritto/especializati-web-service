<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'category_id', 'description', 'image'];

    public function getResults($data, $total)
    {
        if(!isset($data['filter']) && !isset($data['name']) && !isset($data['description']))
            return $this->with(['category'])->paginate($total);

        return $this->with(['category'])->where(function ($query) use ($data) {

            if(isset($data['filter'])) {
                $filter = $data['filter'];
                $query->where('name', $filter);
                $query->orwhere('description', 'LIKE', "%{$filter}%");
            }

            if(isset($data['name']))
                $query->where('name', $data['name']);

            if(isset($data['description'])) {
                $description = $data['description'];
                $query->where('description', 'LIKE', "%{$description}%");
            }

        })//->toSql();
            ->paginate($total);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
