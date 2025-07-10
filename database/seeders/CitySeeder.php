<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cities = [
            // Province 1 (Koshi)
            ['name' => 'Biratnagar', 'province' => 'Koshi', 'district' => 'Morang', 'latitude' => 26.4525, 'longitude' => 87.2718],
            ['name' => 'Dharan', 'province' => 'Koshi', 'district' => 'Sunsari', 'latitude' => 26.8147, 'longitude' => 87.2789],
            ['name' => 'Itahari', 'province' => 'Koshi', 'district' => 'Sunsari', 'latitude' => 26.6650, 'longitude' => 87.2750],
            ['name' => 'Damak', 'province' => 'Koshi', 'district' => 'Jhapa', 'latitude' => 26.6667, 'longitude' => 87.7000],
            ['name' => 'Birtamod', 'province' => 'Koshi', 'district' => 'Jhapa', 'latitude' => 26.6833, 'longitude' => 87.9167],

            // Madhesh Province
            ['name' => 'Janakpur', 'province' => 'Madhesh', 'district' => 'Dhanusha', 'latitude' => 26.7288, 'longitude' => 85.9244],
            ['name' => 'Birgunj', 'province' => 'Madhesh', 'district' => 'Parsa', 'latitude' => 27.0167, 'longitude' => 84.8667],
            ['name' => 'Kalaiya', 'province' => 'Madhesh', 'district' => 'Bara', 'latitude' => 27.0333, 'longitude' => 85.0000],
            ['name' => 'Gaur', 'province' => 'Madhesh', 'district' => 'Rautahat', 'latitude' => 26.7667, 'longitude' => 85.2667],
            ['name' => 'Rajbiraj', 'province' => 'Madhesh', 'district' => 'Saptari', 'latitude' => 26.5400, 'longitude' => 86.7300],

            // Bagmati Province
            ['name' => 'Kathmandu', 'province' => 'Bagmati', 'district' => 'Kathmandu', 'latitude' => 27.7172, 'longitude' => 85.3240],
            ['name' => 'Lalitpur', 'province' => 'Bagmati', 'district' => 'Lalitpur', 'latitude' => 27.6667, 'longitude' => 85.3167],
            ['name' => 'Bhaktapur', 'province' => 'Bagmati', 'district' => 'Bhaktapur', 'latitude' => 27.6710, 'longitude' => 85.4298],
            ['name' => 'Hetauda', 'province' => 'Bagmati', 'district' => 'Makwanpur', 'latitude' => 27.4167, 'longitude' => 85.0333],
            ['name' => 'Bharatpur', 'province' => 'Bagmati', 'district' => 'Chitwan', 'latitude' => 27.6833, 'longitude' => 84.4333],

            // Gandaki Province
            ['name' => 'Pokhara', 'province' => 'Gandaki', 'district' => 'Kaski', 'latitude' => 28.2096, 'longitude' => 83.9856],
            ['name' => 'Baglung', 'province' => 'Gandaki', 'district' => 'Baglung', 'latitude' => 28.2667, 'longitude' => 83.5833],
            ['name' => 'Gorkha', 'province' => 'Gandaki', 'district' => 'Gorkha', 'latitude' => 28.0000, 'longitude' => 84.6333],
            ['name' => 'Syangja', 'province' => 'Gandaki', 'district' => 'Syangja', 'latitude' => 28.0833, 'longitude' => 83.8667],
            ['name' => 'Besisahar', 'province' => 'Gandaki', 'district' => 'Lamjung', 'latitude' => 28.2333, 'longitude' => 84.4167],

            // Lumbini Province
            ['name' => 'Butwal', 'province' => 'Lumbini', 'district' => 'Rupandehi', 'latitude' => 27.7000, 'longitude' => 83.4500],
            ['name' => 'Siddharthanagar', 'province' => 'Lumbini', 'district' => 'Rupandehi', 'latitude' => 27.5036, 'longitude' => 83.4506],
            ['name' => 'Tansen', 'province' => 'Lumbini', 'district' => 'Palpa', 'latitude' => 27.8667, 'longitude' => 83.5500],
            ['name' => 'Ghorahi', 'province' => 'Lumbini', 'district' => 'Dang', 'latitude' => 28.0333, 'longitude' => 82.5000],
            ['name' => 'Tulsipur', 'province' => 'Lumbini', 'district' => 'Dang', 'latitude' => 28.1333, 'longitude' => 82.2833],

            // Karnali Province
            ['name' => 'Birendranagar', 'province' => 'Karnali', 'district' => 'Surkhet', 'latitude' => 28.6000, 'longitude' => 81.6167],
            ['name' => 'Jumla', 'province' => 'Karnali', 'district' => 'Jumla', 'latitude' => 29.2742, 'longitude' => 82.1836],
            ['name' => 'Dunai', 'province' => 'Karnali', 'district' => 'Dolpa', 'latitude' => 28.9667, 'longitude' => 82.9000],
            ['name' => 'Manma', 'province' => 'Karnali', 'district' => 'Kalikot', 'latitude' => 29.4167, 'longitude' => 81.4167],

            // Sudurpashchim Province
            ['name' => 'Dhangadhi', 'province' => 'Sudurpashchim', 'district' => 'Kailali', 'latitude' => 28.7000, 'longitude' => 80.6000],
            ['name' => 'Mahendranagar', 'province' => 'Sudurpashchim', 'district' => 'Kanchanpur', 'latitude' => 28.9644, 'longitude' => 80.1519],
            ['name' => 'Tikapur', 'province' => 'Sudurpashchim', 'district' => 'Kailali', 'latitude' => 28.5167, 'longitude' => 81.1167],
            ['name' => 'Dipayal', 'province' => 'Sudurpashchim', 'district' => 'Doti', 'latitude' => 29.2667, 'longitude' => 80.9500],
        ];

        foreach ($cities as $city) {
            City::create($city);
        }
    }
}
