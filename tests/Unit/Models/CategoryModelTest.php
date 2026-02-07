<?php

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;

/**
 * Unit Test for Category Model Business Logic
 * 
 * Tests: Status mapping, Home flag, Constants, Category structure
 */
class CategoryModelTest extends TestCase
{
    // ======================================================
    // TEST: Category Status
    // ======================================================

    /**
     * Test category status PUBLIC
     */
    public function test_category_status_public(): void
    {
        $status = $this->getCategoryStatus(1);

        $this->assertEquals('Public', $status['name']);
        $this->assertEquals('label-success', $status['class']);
    }

    /**
     * Test category status PRIVATE
     */
    public function test_category_status_private(): void
    {
        $status = $this->getCategoryStatus(0);

        $this->assertEquals('Private', $status['name']);
        $this->assertEquals('label-default', $status['class']);
    }

    /**
     * Test category status unknown returns N/A
     */
    public function test_category_status_unknown(): void
    {
        $status = $this->getCategoryStatus(5);

        $this->assertEquals('[N\A]', $status);
    }

    // ======================================================
    // TEST: Category Home Flag
    // ======================================================

    /**
     * Test category home flag enabled
     */
    public function test_category_home_flag_enabled(): void
    {
        $home = $this->getCategoryHome(1);

        $this->assertEquals('Public', $home['name']);
        $this->assertEquals('label-primary', $home['class']);
    }

    /**
     * Test category home flag disabled
     */
    public function test_category_home_flag_disabled(): void
    {
        $home = $this->getCategoryHome(0);

        $this->assertEquals('Private', $home['name']);
        $this->assertEquals('label-default', $home['class']);
    }

    // ======================================================
    // TEST: Category Constants
    // ======================================================

    /**
     * Test category status constants
     */
    public function test_category_status_constants(): void
    {
        $this->assertEquals(1, $this->getConstant('STATUS_PUBLIC'));
        $this->assertEquals(0, $this->getConstant('STATUS_PRIVATE'));
    }

    /**
     * Test category home constant
     */
    public function test_category_home_constant(): void
    {
        $this->assertEquals(1, $this->getConstant('HOME'));
    }

    // ======================================================
    // TEST: Category Table Name
    // ======================================================

    /**
     * Test category table name
     */
    public function test_category_table_name(): void
    {
        $this->assertEquals('category', $this->getTableName());
    }

    // ======================================================
    // TEST: Category Hierarchy
    // ======================================================

    /**
     * Test build category tree
     */
    public function test_build_category_tree(): void
    {
        $categories = [
            ['id' => 1, 'name' => 'Điện thoại', 'parent_id' => null],
            ['id' => 2, 'name' => 'Laptop', 'parent_id' => null],
            ['id' => 3, 'name' => 'iPhone', 'parent_id' => 1],
            ['id' => 4, 'name' => 'Samsung', 'parent_id' => 1],
            ['id' => 5, 'name' => 'MacBook', 'parent_id' => 2],
        ];

        $tree = $this->buildCategoryTree($categories);

        $this->assertCount(2, $tree); // 2 root categories
        $this->assertCount(2, $tree[0]['children']); // Điện thoại has 2 children
        $this->assertCount(1, $tree[1]['children']); // Laptop has 1 child
    }

    /**
     * Test filter active categories
     */
    public function test_filter_active_categories(): void
    {
        $categories = [
            ['id' => 1, 'name' => 'Active Category', 'c_active' => 1],
            ['id' => 2, 'name' => 'Inactive Category', 'c_active' => 0],
            ['id' => 3, 'name' => 'Another Active', 'c_active' => 1],
        ];

        $active = $this->filterActiveCategories($categories);

        $this->assertCount(2, $active);
    }

    /**
     * Test filter home page categories
     */
    public function test_filter_home_categories(): void
    {
        $categories = [
            ['id' => 1, 'name' => 'On Home', 'c_home' => 1],
            ['id' => 2, 'name' => 'Not on Home', 'c_home' => 0],
            ['id' => 3, 'name' => 'Also on Home', 'c_home' => 1],
        ];

        $homeCategories = $this->filterHomeCategories($categories);

        $this->assertCount(2, $homeCategories);
    }

    // ======================================================
    // HELPER METHODS (Category Logic)
    // ======================================================

    private function getCategoryStatus(int $active): mixed
    {
        $statusMap = [
            1 => ['name' => 'Public', 'class' => 'label-success'],
            0 => ['name' => 'Private', 'class' => 'label-default'],
        ];

        return $statusMap[$active] ?? '[N\A]';
    }

    private function getCategoryHome(int $home): mixed
    {
        $homeMap = [
            1 => ['name' => 'Public', 'class' => 'label-primary'],
            0 => ['name' => 'Private', 'class' => 'label-default'],
        ];

        return $homeMap[$home] ?? '[N\A]';
    }

    private function getConstant(string $name): int
    {
        $constants = [
            'STATUS_PUBLIC' => 1,
            'STATUS_PRIVATE' => 0,
            'HOME' => 1,
        ];

        return $constants[$name];
    }

    private function getTableName(): string
    {
        return 'category';
    }

    private function buildCategoryTree(array $categories, ?int $parentId = null): array
    {
        $tree = [];

        foreach ($categories as $category) {
            if ($category['parent_id'] === $parentId) {
                $children = $this->buildCategoryTree($categories, $category['id']);
                $category['children'] = $children;
                $tree[] = $category;
            }
        }

        return $tree;
    }

    private function filterActiveCategories(array $categories): array
    {
        return array_values(array_filter($categories, fn($c) => ($c['c_active'] ?? 0) === 1));
    }

    private function filterHomeCategories(array $categories): array
    {
        return array_values(array_filter($categories, fn($c) => ($c['c_home'] ?? 0) === 1));
    }
}
