<?php

namespace App\Services;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;

class CategoryService
{
    public function __construct(
        private readonly CategoryRepository $categoryRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function findAllAsNestedTree(): array
    {
        $categories = $this->categoryRepository->findAllAsNestedTree();

        return $this->buildNestedTree($categories);
    }

    public function buildNestedTree(array $categories, ?Category $parent = null, array $visited = []): array
    {
        $branch = [];

        foreach ($categories as $category) {
            if (in_array($category->getId(), $visited, true)) {
                continue;
            }

            if ($category->getParent() === $parent) {
                $visited[] = $category->getId();

                $children = $this->buildNestedTree($categories, $category, $visited);
                $branch[] = [
                    'category' => $category,
                    'children' => $children,
                ];
            }
        }

        return $branch;
    }

    public function flattenTree(array $tree, int $level = 0): array
    {
        $choices = [];

        foreach ($tree as $node) {
            $category = $node['category'];
            $prefix = str_repeat('— ', $level);
            $choices[$prefix . $category->getName()] = $category;

            if (!empty($node['children'])) {
                $choices += $this->flattenTree($node['children'], $level + 1);
            }
        }

        return $choices;
    }

    public function findAllExcludingCategoryAndDescendants(?Category $category): array
    {
        if (!$category) {
            return $this->categoryRepository->findAll();
        }

        $excludeIds = $this->getDescendantIds($category);
        $excludeIds[] = $category->getId();

        return $this->categoryRepository->findAllExcludingDescendants($excludeIds);
    }

    private function getDescendantIds(Category $category): array
    {
        $ids = [];

        foreach ($category->getChildren() as $child) {
            $ids[] = $child->getId();
            $ids = array_merge($ids, $this->getDescendantIds($child));
        }

        return $ids;
    }

    public function tryDelete(Category $category): array
    {
        $errorMessage = match (true) {
            $category->getBooks()->count() > 0 => 'Cannot delete category while being used in books.',
            $category->getChildren()->count() > 0 => 'Cannot delete category because it has subcategories.',
            default => null,
        };

        if ($errorMessage !== null) {
            return [
                'success' => false,
                'message' => $errorMessage,
            ];
        }

        $this->entityManager->remove($category);
        $this->entityManager->flush();

        return [
            'success' => true,
            'message' => 'Category successfully deleted.',
        ];
    }
}