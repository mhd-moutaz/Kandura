<?php
// database/seeders/CitySeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\City;

class CitySeeder extends Seeder
{
    public function run()
    {
        $cities = [
            // سوريا
            ['name' => ['en' => 'Damascus', 'ar' => 'دمشق']],
            ['name' => ['en' => 'Aleppo', 'ar' => 'حلب']],
            ['name' => ['en' => 'Homs', 'ar' => 'حمص']],
            ['name' => ['en' => 'Latakia', 'ar' => 'اللاذقية']],
            ['name' => ['en' => 'Hama', 'ar' => 'حماة']],
            ['name' => ['en' => 'Al-Raqqah', 'ar' => 'الرقة']],
            ['name' => ['en' => 'Deir ez-Zor', 'ar' => 'دير الزور']],
            ['name' => ['en' => 'Al-Hasakah', 'ar' => 'الحسكة']],
            ['name' => ['en' => 'Qamishli', 'ar' => 'القامشلي']],
            ['name' => ['en' => 'Tartus', 'ar' => 'طرطوس']],
            ['name' => ['en' => 'As-Suwayda', 'ar' => 'السويداء']],
            ['name' => ['en' => 'Daraa', 'ar' => 'درعا']],
            ['name' => ['en' => 'Idlib', 'ar' => 'إدلب']],
            ['name' => ['en' => 'Golan', 'ar' => 'الجولان']],


            // السعودية
            ['name' => ['en' => 'Riyadh', 'ar' => 'الرياض']],
            ['name' => ['en' => 'Jeddah', 'ar' => 'جدة']],
            ['name' => ['en' => 'Mecca', 'ar' => 'مكة المكرمة']],
            ['name' => ['en' => 'Medina', 'ar' => 'المدينة المنورة']],
            ['name' => ['en' => 'Dammam', 'ar' => 'الدمام']],
            ['name' => ['en' => 'Taif', 'ar' => 'الطائف']],
            ['name' => ['en' => 'Tabuk', 'ar' => 'تبوك']],
            ['name' => ['en' => 'Abha', 'ar' => 'أبها']],

            // مصر
            ['name' => ['en' => 'Cairo', 'ar' => 'القاهرة']],
            ['name' => ['en' => 'Alexandria', 'ar' => 'الإسكندرية']],
            ['name' => ['en' => 'Giza', 'ar' => 'الجيزة']],
            ['name' => ['en' => 'Luxor', 'ar' => 'الأقصر']],
            ['name' => ['en' => 'Aswan', 'ar' => 'أسوان']],

            // الإمارات
            ['name' => ['en' => 'Dubai', 'ar' => 'دبي']],
            ['name' => ['en' => 'Abu Dhabi', 'ar' => 'أبو ظبي']],
            ['name' => ['en' => 'Sharjah', 'ar' => 'الشارقة']],

            // الأردن
            ['name' => ['en' => 'Amman', 'ar' => 'عمان']],
            ['name' => ['en' => 'Irbid', 'ar' => 'إربد']],
            ['name' => ['en' => 'Zarqa', 'ar' => 'الزرقاء']],

            // لبنان
            ['name' => ['en' => 'Beirut', 'ar' => 'بيروت']],
            ['name' => ['en' => 'Tripoli', 'ar' => 'طرابلس']],
            ['name' => ['en' => 'Sidon', 'ar' => 'صيدا']],

            // قطر
            ['name' => ['en' => 'Doha', 'ar' => 'الدوحة']],
            ['name' => ['en' => 'Al Rayyan', 'ar' => 'الريان']],

            // الكويت
            ['name' => ['en' => 'Kuwait City', 'ar' => 'مدينة الكويت']],
            ['name' => ['en' => 'Hawalli', 'ar' => 'حولي']],

            // عمان
            ['name' => ['en' => 'Muscat', 'ar' => 'مسقط']],
            ['name' => ['en' => 'Salalah', 'ar' => 'صلالة']],

            // البحرين
            ['name' => ['en' => 'Manama', 'ar' => 'المنامة']],
            ['name' => ['en' => 'Riffa', 'ar' => 'الرفاع']],

            // المغرب
            ['name' => ['en' => 'Casablanca', 'ar' => 'الدار البيضاء']],
            ['name' => ['en' => 'Rabat', 'ar' => 'الرباط']],

            // الجزائر
            ['name' => ['en' => 'Algiers', 'ar' => 'الجزائر']],
            ['name' => ['en' => 'Oran', 'ar' => 'وهران']],

            // تونس
            ['name' => ['en' => 'Tunis', 'ar' => 'تونس']],
            ['name' => ['en' => 'Sfax', 'ar' => 'صفاقس']],

            // فلسطين
            ['name' => ['en' => 'Jerusalem', 'ar' => 'القدس']],
            ['name' => ['en' => 'Gaza', 'ar' => 'غزة']],
            ['name' => ['en' => 'Ramallah', 'ar' => 'رام الله']],
            ['name' => ['en' => 'Hebron', 'ar' => 'الخليل']],
            ['name' => ['en' => 'Nablus', 'ar' => 'نابلس']],

            // اليمن
            ['name' => ['en' => 'Sanaa', 'ar' => 'صنعاء']],
            ['name' => ['en' => 'Aden', 'ar' => 'عدن']],
            ['name' => ['en' => 'Taiz', 'ar' => 'تعز']],

            // السودان
            ['name' => ['en' => 'Khartoum', 'ar' => 'الخرطوم']],
            ['name' => ['en' => 'Omdurman', 'ar' => 'أم درمان']],

            // العراق
            ['name' => ['en' => 'Baghdad', 'ar' => 'بغداد']],
            ['name' => ['en' => 'Basra', 'ar' => 'البصرة']],
            ['name' => ['en' => 'Mosul', 'ar' => 'الموصل']],
            ['name' => ['en' => 'Erbil', 'ar' => 'أربيل']],

            // ليبيا
            ['name' => ['en' => 'Tripoli', 'ar' => 'طرابلس']],
            ['name' => ['en' => 'Benghazi', 'ar' => 'بنغازي']],

            // موريتانيا
            ['name' => ['en' => 'Nouakchott', 'ar' => 'نواكشوط']],

            // جيبوتي
            ['name' => ['en' => 'Djibouti', 'ar' => 'جيبوتي']],

            // الصومال
            ['name' => ['en' => 'Mogadishu', 'ar' => 'مقديشو']],

            // جزر القمر
            ['name' => ['en' => 'Moroni', 'ar' => 'موروني']],
        ];
        
        foreach ($cities as $city) {
            City::create($city);
        }


    }
}
