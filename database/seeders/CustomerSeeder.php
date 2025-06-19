<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Customer;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = [
            [
                'customer_code' => 'KH001',
                'name' => 'Nguyễn Văn An',
                'email' => 'nguyenvanan@example.com',
                'phone' => '0123456789',
                'address' => '123 Đường ABC, Quận 1, TP.HCM',
                'birth_date' => '1990-01-15',
                'gender' => 'male',
            ],
            [
                'customer_code' => 'KH002',
                'name' => 'Trần Thị Bình',
                'email' => 'tranthibinh@example.com',
                'phone' => '0987654321',
                'address' => '456 Đường XYZ, Quận 2, TP.HCM',
                'birth_date' => '1985-05-20',
                'gender' => 'female',
            ],
            [
                'customer_code' => 'KH003',
                'name' => 'Lê Văn Cường',
                'email' => 'levancuong@example.com',
                'phone' => '0369852147',
                'address' => '789 Đường DEF, Quận 3, TP.HCM',
                'birth_date' => '1988-12-10',
                'gender' => 'male',
            ],
            [
                'customer_code' => 'KH004',
                'name' => 'Phạm Thị Dung',
                'email' => 'phamthidung@example.com',
                'phone' => '0523698741',
                'address' => '321 Đường GHI, Quận 4, TP.HCM',
                'birth_date' => '1992-08-25',
                'gender' => 'female',
            ],
            [
                'customer_code' => 'KH005',
                'name' => 'Hoàng Văn Em',
                'email' => 'hoangvanem@example.com',
                'phone' => '0147852369',
                'address' => '654 Đường JKL, Quận 5, TP.HCM',
                'birth_date' => '1987-03-30',
                'gender' => 'male',
            ],
        ];

        foreach ($customers as $customer) {
            Customer::firstOrCreate(
                ['customer_code' => $customer['customer_code']],
                $customer
            );
        }
    }
}
