<?php

namespace App\Services;

use App\DTOs\SearchInternshipDTO;
use App\Models\Internship;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class SearchService
{
    /**
     * Search and filter published internships.
     */
    public function searchInternships(SearchInternshipDTO $dto): LengthAwarePaginator
    {
        $query = Internship::published()->with('company');

        if ($dto->q) {
            $likeOperator = DB::connection()->getDriverName() === 'pgsql' ? 'ILIKE' : 'like';
            $search = $dto->q;

            $query->where(function ($q) use ($likeOperator, $search) {
                $q->where('title', $likeOperator, "%{$search}%")
                    ->orWhere('description', $likeOperator, "%{$search}%")
                    ->orWhereHas('company', function ($companyQuery) use ($likeOperator, $search) {
                        $companyQuery->where('name', $likeOperator, "%{$search}%");
                    });
            });
        }

        $likeOperator = DB::connection()->getDriverName() === 'pgsql' ? 'ILIKE' : 'like';

        if ($dto->location) {
            $query->where('location', $likeOperator, "%{$dto->location}%");
        }

        if ($dto->type) {
            $query->where('type', $dto->type);
        }

        if ($dto->isPaid !== null && $dto->isPaid !== '') {
            $query->where('is_paid', filter_var($dto->isPaid, FILTER_VALIDATE_BOOLEAN));
        }

        if ($dto->lat !== null && $dto->lng !== null && $dto->radius !== null) {
            $lat = (float) $dto->lat;
            $lng = (float) $dto->lng;
            $radius = (int) $dto->radius;

            // Haversine formula
            $query->whereNotNull('latitude')
                  ->whereNotNull('longitude')
                  ->select('internships.*')
                  ->selectRaw('( 6371 * acos( cos( radians(?) ) *
                           cos( radians( latitude ) )
                           * cos( radians( longitude ) - radians(?)
                           ) + sin( radians(?) ) *
                           sin( radians( latitude ) ) )
                         ) AS distance', [$lat, $lng, $lat])
                  ->whereRaw('( 6371 * acos( cos( radians(?) ) *
                           cos( radians( latitude ) )
                           * cos( radians( longitude ) - radians(?)
                           ) + sin( radians(?) ) *
                           sin( radians( latitude ) ) )
                         ) <= ?', [$lat, $lng, $lat, $radius]);
        }

        match ($dto->sort) {
            'oldest' => $query->orderBy('created_at', 'asc'),
            'deadline' => $query->orderBy('deadline_at', 'asc'),
            default => $query->orderBy('created_at', 'desc'),
        };

        return $query->paginate($dto->perPage)->withQueryString();
    }
}
