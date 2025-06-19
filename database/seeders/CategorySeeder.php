<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Điện tử',
                'description' => 'Các sản phẩm điện tử như điện thoại, máy tính, tablet',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Thời trang',
                'description' => 'Quần áo, giày dép, phụ kiện thời trang',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Nhà cửa & Đời sống',
                'description' => 'Đồ gia dụng, nội thất, trang trí nhà cửa',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Sách & Văn phòng phẩm',
                'description' => 'Sách, bút, giấy, dụng cụ học tập',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Thể thao & Giải trí',
                'description' => 'Dụng cụ thể thao, đồ chơi, game',
                'sort_order' => 5,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['name' => $category['name']],
                $category
            );
        }
    }
}
