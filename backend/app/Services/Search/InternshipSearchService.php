<?php

namespace App\Services\Search;

use App\Models\Internship;

class InternshipSearchService
{
    public function search(array $filters = [])
    {
        $query = Internship::published()->with(['company']);

        // Full-Text Search Layer (PostgreSQL)
        if (! empty($filters['q'])) {
            $searchTerm = $filters['q'];
            $query->where(function ($q) use ($searchTerm) {
                if (\DB::connection()->getDriverName() === 'sqlite') {
                    $q->where('title', 'like', "%{$searchTerm}%")
                        ->orWhere('description', 'like', "%{$searchTerm}%")
                        ->orWhereHas('company', function ($cq) use ($searchTerm) {
                            $cq->where('name', 'like', "%{$searchTerm}%");
                        });
                } else {
                    $q->whereRaw("to_tsvector('english', title || ' ' || description) @@ plainto_tsquery('english', ?)", [$searchTerm])
                        ->orWhere('title', 'ILIKE', "%{$searchTerm}%")
                        ->orWhereHas('company', function ($cq) use ($searchTerm) {
                            $cq->where('name', 'ILIKE', "%{$searchTerm}%");
                        });
                }
            });
        }

        // Filters
        if (! empty($filters['location'])) {
            $likeOperator = \DB::connection()->getDriverName() === 'sqlite' ? 'like' : 'ILIKE';
            $query->where('location', $likeOperator, "%{$filters['location']}%");
        }

        if (! empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (! empty($filters['is_paid'])) {
            $query->where('is_paid', $filters['is_paid']);
        }

        if (! empty($filters['tags'])) {
            $tags = is_array($filters['tags']) ? $filters['tags'] : explode(',', $filters['tags']);
            foreach ($tags as $tag) {
                $query->whereJsonContains('tags', $tag);
            }
        }

        // Sorting
        $sort = $filters['sort'] ?? 'latest';
        switch ($sort) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'deadline':
                $query->orderBy('deadline_at', 'asc');
                break;
            case 'latest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        return $query->paginate($filters['per_page'] ?? 12);
    }
}
