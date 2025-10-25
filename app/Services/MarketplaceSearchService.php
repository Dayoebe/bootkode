<?php 

// app/Services/MarketplaceSearchService.php
namespace App\Services;

use App\Models\Marketplace\MarketplaceItem;
use Illuminate\Database\Eloquent\Builder;

class MarketplaceSearchService
{
    public function search(array $filters = []): Builder
    {
        $query = MarketplaceItem::published()
            ->with(['vendor', 'reviews' => fn($q) => $q->approved()->latest()->limit(3)]);

        // Text search
        if (!empty($filters['search'])) {
            $searchTerm = $filters['search'];
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%")
                  ->orWhere('keywords', 'like', "%{$searchTerm}%")
                  ->orWhereJsonContains('tags', $searchTerm);
            });
        }

        // Type filter
        if (!empty($filters['type'])) {
            $query->byType($filters['type']);
        }

        // Category filter
        if (!empty($filters['categories'])) {
            $categories = is_array($filters['categories']) ? $filters['categories'] : [$filters['categories']];
            $query->where(function ($q) use ($categories) {
                foreach ($categories as $category) {
                    $q->orWhereJsonContains('categories', $category);
                }
            });
        }

        // Price range
        if (isset($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }
        if (isset($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }

        // Rating filter
        if (!empty($filters['min_rating'])) {
            $query->where('average_rating', '>=', $filters['min_rating']);
        }

        // Vendor filter
        if (!empty($filters['vendor_id'])) {
            $query->byVendor($filters['vendor_id']);
        }

        // Featured items
        if (!empty($filters['featured_only'])) {
            $query->featured();
        }

        // Sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';

        switch ($sortBy) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'rating':
                $query->orderBy('average_rating', 'desc');
                break;
            case 'sales':
                $query->orderBy('sales_count', 'desc');
                break;
            case 'views':
                $query->orderBy('views_count', 'desc');
                break;
            default:
                $query->orderBy($sortBy, $sortOrder);
        }

        return $query;
    }

    public function getPopularSearches(int $limit = 10): array
    {
        // This would typically come from a search analytics table
        // For now, return some common searches
        return [
            'Laravel', 'React', 'Python', 'JavaScript', 'Vue.js',
            'Node.js', 'PHP', 'Web Development', 'Mobile App',
            'UI/UX Design', 'Database Design', 'API Development'
        ];
    }

    public function getSuggestions(string $term, int $limit = 5): array
    {
        $suggestions = MarketplaceItem::published()
            ->where('title', 'like', "%{$term}%")
            ->limit($limit)
            ->pluck('title')
            ->toArray();

        // Add category suggestions
        $categories = $this->getCategories();
        foreach ($categories as $key => $label) {
            if (stripos($label, $term) !== false) {
                $suggestions[] = $label;
            }
        }

        return array_unique(array_slice($suggestions, 0, $limit));
    }

    protected function getCategories(): array
    {
        return [
            'programming' => 'Programming',
            'web-development' => 'Web Development',
            'mobile-development' => 'Mobile Development',
            'data-science' => 'Data Science',
            'machine-learning' => 'Machine Learning',
            'design' => 'Design',
            'ui-ux' => 'UI/UX Design',
            'business' => 'Business',
            'marketing' => 'Marketing',
            'devops' => 'DevOps',
            'cybersecurity' => 'Cybersecurity',
            'game-development' => 'Game Development',
        ];
    }
}
