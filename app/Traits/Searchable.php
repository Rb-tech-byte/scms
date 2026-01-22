<?php

namespace App\Traits;

trait Searchable
{
    /**
     * Scope a query to search for a specific term in specific fields.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $term
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $term, $columns = ['*'])
    {
        if (!$term) {
            return $query;
        }

        foreach ($columns as $column) {
            $query->orWhere($column, 'LIKE', '%' . $term . '%');
        }

        return $query;
    }
}
