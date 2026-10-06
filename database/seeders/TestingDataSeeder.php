<?php

namespace Database\Seeders;

use App\Models\Casting;
use App\Models\Club;
use App\Models\CollectionItem;
use App\Models\Comment;
use App\Models\Listing;
use App\Models\MarketMultiplierHistory;
use App\Models\MarketSignal;
use App\Models\Post;
use App\Models\Rating;
use App\Models\Trade;
use App\Models\TradeItem;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TestingDataSeeder extends Seeder
{
    public function run(): void
    {
        // Pastikan DemoUsersSeeder sudah jalan (kalau belum, panggil otomatis)
        if (User::count() === 0) {
            $this->call(DemoUsersSeeder::class);
        }

        $users = User::where('email', '!=', 'superadmin@dcasttrade.com')->get();
        $club = Club::first();

        // 1. Generate Castings & Market Data
        $castingsData = [
            ['name' => 'Nissan Skyline GT-R (R34)', 'series' => 'HW J-Imports', 'year' => 2024, 'rarity_tier' => 'sth', 'base_value' => 750000],
            ['name' => 'Honda Civic Custom', 'series' => 'HW J-Imports', 'year' => 2023, 'rarity_tier' => 'premium', 'base_value' => 150000],
            ['name' => 'Datsun 510 Wagon', 'series' => 'HW Wagons', 'year' => 2022, 'rarity_tier' => 'rlc', 'base_value' => 1200000],
            ['name' => 'Volkswagen Drag Bus', 'series' => 'HW Art Cars', 'year' => 2021, 'rarity_tier' => 'th', 'base_value' => 350000],
            ['name' => 'Porsche 911 GT3 RS', 'series' => 'HW Exotics', 'year' => 2024, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => 'Custom 72 Chevy', 'series' => 'HW Trucks', 'year' => 2023, 'rarity_tier' => 'mainline', 'base_value' => 45000],
            ['name' => 'Mazda RX-7', 'series' => 'Nightburnerz', 'year' => 2022, 'rarity_tier' => 'premium', 'base_value' => 180000],
            ['name' => 'Ground FX', 'series' => 'Salt Flat Racers', 'year' => 2025, 'rarity_tier' => 'silver', 'base_value' => 60000],
            ['name' => 'Solar Reflex', 'series' => 'Salt Flat Racers', 'year' => 2025, 'rarity_tier' => 'silver', 'base_value' => 60000],
            ['name' => '\'41 Willys', 'series' => 'Salt Flat Racers', 'year' => 2025, 'rarity_tier' => 'silver', 'base_value' => 60000],
            ['name' => '\'32 Ford', 'series' => 'Salt Flat Racers', 'year' => 2025, 'rarity_tier' => 'silver', 'base_value' => 60000],
            ['name' => '\'88 Ford Thunderbird', 'series' => 'Salt Flat Racers', 'year' => 2025, 'rarity_tier' => 'silver', 'base_value' => 60000],
            ['name' => 'Volvo 850 Estate', 'series' => 'Neon Speeders', 'year' => 2026, 'rarity_tier' => 'premium', 'base_value' => 60000],
            ['name' => 'Custom Ford Maverick', 'series' => 'Neon Speeders', 'year' => 2026, 'rarity_tier' => 'premium', 'base_value' => 60000],
            ['name' => '\'62 Corvette', 'series' => 'Neon Speeders', 'year' => 2026, 'rarity_tier' => 'premium', 'base_value' => 60000],
            ['name' => '\'64 Chevy Nova Gasser', 'series' => 'Neon Speeders', 'year' => 2026, 'rarity_tier' => 'premium', 'base_value' => 60000],
            ['name' => '\'70 Dodge Hemi Challenger', 'series' => 'Neon Speeders', 'year' => 2026, 'rarity_tier' => 'premium', 'base_value' => 60000],
            ['name' => 'Volkswagen SP2', 'series' => 'Neon Speeders', 'year' => 2026, 'rarity_tier' => 'premium', 'base_value' => 60000],
            ['name' => '\'85 Honda CR-X', 'series' => 'Neon Speeders', 'year' => 2026, 'rarity_tier' => 'premium', 'base_value' => 60000],
            ['name' => '\'71 Porsche 911', 'series' => 'Neon Speeders', 'year' => 2026, 'rarity_tier' => 'premium', 'base_value' => 60000],
            ['name' => '2017 Nissan GT-R (R35)', 'series' => 'Neon Speeders', 'year' => 2026, 'rarity_tier' => 'premium', 'base_value' => 60000],
            ['name' => 'BMW M4', 'series' => 'Neon Speeders', 'year' => 2026, 'rarity_tier' => 'premium', 'base_value' => 60000],
            ['name' => '\'22 Ford Maverick Custom', 'series' => 'Neon Speeders', 'year' => 2026, 'rarity_tier' => 'premium', 'base_value' => 60000],
            ['name' => '2017 Camaro ZL1', 'series' => 'Neon Speeders', 'year' => 2026, 'rarity_tier' => 'premium', 'base_value' => 60000],
            ['name' => 'Aston Martin Vantage GTE', 'series' => 'Neon Speeders', 'year' => 2026, 'rarity_tier' => 'premium', 'base_value' => 60000],
            ['name' => 'Porsche Cayman S', 'series' => 'Neon Speeders', 'year' => 2026, 'rarity_tier' => 'premium', 'base_value' => 60000],
            ['name' => '2016 Subaru WRX STI', 'series' => 'Neon Speeders', 'year' => 2026, 'rarity_tier' => 'premium', 'base_value' => 60000],
            ['name' => '2004 Mazda Mazdaspeed Miata', 'series' => 'Neon Speeders', 'year' => 2026, 'rarity_tier' => 'premium', 'base_value' => 60000],
            ['name' => '\'16 Lamborghini Centenario Roadster', 'series' => 'Exoticars', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => 'Gordon Murray T.33', 'series' => 'Exoticars', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => 'McMurtry Spéirling', 'series' => 'Exoticars', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => 'Pagani Zonda Cinque', 'series' => 'Exoticars', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => '2024 Aston Martin Vantage GT3', 'series' => 'Exoticars', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => 'X-Cceleron', 'series' => 'Exoticars', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => 'McLaren Formula 1 Team', 'series' => 'Formula 1', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => 'Scuderia Ferrari HP', 'series' => 'Formula 1', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => 'SF90 Stradale', 'series' => 'Ferrari', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => 'Dino 206 GT', 'series' => 'Ferrari', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => 'Instant Boost', 'series' => 'HW Starting Grid', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => '\'69 Dodge Charger Daytona', 'series' => 'HW Starting Grid', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => 'Track Ripper', 'series' => 'HW Starting Grid', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => 'Bugatti Bolide', 'series' => 'HW Starting Grid', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => 'Maserati Tipo 61', 'series' => 'HW Starting Grid', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => 'BMW M4 GT3', 'series' => 'HW Starting Grid', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => 'Custom \'72 Chevy LUV', 'series' => 'Drag Racers', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => 'Eggshelleracer', 'series' => 'Drag Racers', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => '\'87 Buick Regal GNX', 'series' => 'Nightspeed', 'year' => 2026, 'rarity_tier' => 'th', 'base_value' => 350000],
            ['name' => '\'20 Ford Mustang Shelby GT500', 'series' => 'Nightspeed', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => '\'18 Honda Civic Type R', 'series' => 'Nightspeed', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => '\'70 Dodge Hemi Challenger', 'series' => 'Nightspeed', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => 'Cupra E-Racer', 'series' => 'Nightspeed', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => '24/Seven', 'series' => 'Nightspeed', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => '\'20 Dodge Charger Hellcat', 'series' => 'Nightspeed', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => 'Honda Odyssey', 'series' => 'HW J-Imports', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => '\'55 Nomad', 'series' => 'HW J-Imports', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => 'Toyota Prius Custom', 'series' => 'HW J-Imports', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => 'Nissan Skyline 2000 GT-R', 'series' => 'HW J-Imports', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => '\'88 Mitsubishi Starion', 'series' => 'HW J-Imports', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => 'Drift Box', 'series' => 'HW Euro', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => 'BMW 635 CSi', 'series' => 'HW Euro', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => 'VW ID. Buzz Bomb', 'series' => 'HW Euro', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => 'Mercedes-Benz CLA HW', 'series' => 'HW Euro', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => 'Porsche 911 Carrera T', 'series' => 'Factory Fresh', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => 'Maserati Shamal', 'series' => 'Factory Fresh', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => 'Ford Mustang GTD', 'series' => 'Factory Fresh', 'year' => 2026, 'rarity_tier' => 'sth', 'base_value' => 750000],
            ['name' => 'Custom \'66 Toronado', 'series' => 'Factory Fresh', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => 'Bounce\'N Bass', 'series' => 'Layin\' Low', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => '\'59 Chevy Impala', 'series' => 'Layin\' Low', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => '\'47 Chevy Fleetline', 'series' => 'Layin\' Low', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => 'Custom \'41 Willys Coupe', 'series' => 'Rod Squad', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => 'Erikenstein Rod', 'series' => 'Rod Squad', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => 'Mid Mill', 'series' => 'Rod Squad', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => 'High-Tail Chaser', 'series' => 'HW Mods', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => 'Vairstrike', 'series' => 'HW Mods', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => 'Carbonator', 'series' => 'Experimotors', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => 'Roller Toaster', 'series' => 'Experimotors', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => 'Twin Tags', 'series' => 'Experimotors', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => 'Feline Lucky', 'series' => 'Experimotors', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => 'Deora II', 'series' => 'HW Fan Driven', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => 'Rodger Dodger', 'series' => 'HW Fan Driven', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => 'Purple Passion', 'series' => 'HW Fan Driven', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => 'Bone Shaker', 'series' => 'HW Fan Driven', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => 'Hako Type D', 'series' => 'Compact Kings', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => 'Ridge Crest GT', 'series' => 'Torque', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => 'Drift-Ender', 'series' => 'Truckin\' Along', 'year' => 2026, 'rarity_tier' => 'sth', 'base_value' => 750000],
            ['name' => '\'20 Jeep Gladiator', 'series' => 'Truckin\' Along', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => '\'83 Chevy Silverado', 'series' => 'Truckin\' Along', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => 'Ford F-150 Lightning Supertruck', 'series' => 'Truckin\' Along', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => '5 Alarm', 'series' => 'HW Heavy Weights', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => 'Haulerback', 'series' => 'HW Heavy Weights', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => 'Raijin Express', 'series' => 'HW Heavy Weights', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => 'El Segundo Rallye', 'series' => 'HW Dirt', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => 'Mazda Autozam', 'series' => 'HW Dirt', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => 'Duck N\' Roll', 'series' => 'HW Dirt', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => '8 Crate', 'series' => 'Wagons', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => 'Lil\' Mad', 'series' => 'Wagons', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => 'Pass \'N Go', 'series' => 'Drop Tops', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => '\'92 Dodge Viper RT/10', 'series' => 'Drop Tops', 'year' => 2026, 'rarity_tier' => 'th', 'base_value' => 350000],
            ['name' => 'Fiat Beast of Turin', 'series' => 'Drop Tops', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => 'Solar Reflex', 'series' => 'HW EV', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => 'Ford Mustang Mach-E 1400', 'series' => 'HW EV', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => 'Porsche Taycan Turbo S', 'series' => 'HW EV', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => 'Wattzup', 'series' => 'HW EV', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => 'Cadillac Celestiq', 'series' => 'HW EV', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => 'Custom \'20 Honda e', 'series' => 'HW EV', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => 'Optimus Prime', 'series' => 'Screen Time', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => 'HW K.I.T.T. Concept', 'series' => 'Screen Time', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => 'Standard Kart', 'series' => 'Screen Time', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => 'Batmobile (The Batman)', 'series' => 'Batman', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => 'Tooned Classic TV Series Batmobile', 'series' => 'Batman', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => 'Tooned Purple Passion', 'series' => 'Tooned', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => 'Deora Too\'nd', 'series' => 'Tooned', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => 'RD-06', 'series' => 'X-Raycers', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => 'X-Steam', 'series' => 'X-Raycers', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => 'Bullet Proof', 'series' => 'X-Raycers', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => 'Mazda MX-5 Miata', 'series' => 'HW Dream Garage', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => 'La Liebre', 'series' => 'HW Dream Garage', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => '2 Jet Z', 'series' => 'HW Dream Garage', 'year' => 2026, 'rarity_tier' => 'th', 'base_value' => 350000],
            ['name' => '\'20 Toyota GR Supra', 'series' => 'Then and Now', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => 'Austin Mini Cooper S', 'series' => 'Then and Now', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => 'HW Braille Racer – Twin Mill', 'series' => 'HW All Drivers Welcome', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => 'Flippin Fast', 'series' => 'HW All Drivers Welcome', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => 'Click Shift', 'series' => 'HW All Drivers Welcome', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => 'Barbie Dream Camper', 'series' => 'Mattel', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => 'Monster High Ghoul Mobile', 'series' => 'Mattel', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
            ['name' => 'Custom Otto', 'series' => 'Mattel', 'year' => 2026, 'rarity_tier' => 'sth', 'base_value' => 750000],
            ['name' => 'Birthday Burner', 'series' => 'Sweet Rides', 'year' => 2026, 'rarity_tier' => 'mainline', 'base_value' => 50000],
        ];

        $castings = [];
        foreach ($castingsData as $data) {
            $casting = Casting::firstOrCreate(
                ['slug' => Str::slug($data['name'].'-'.$data['year'])],
                [
                    'name' => $data['name'],
                    'series' => $data['series'],
                    'year' => $data['year'],
                    'rarity_tier' => $data['rarity_tier'],
                    'base_value' => $data['base_value'],
                ]
            );
            $castings[] = $casting;

            $currentMultiplier = fake()->randomFloat(2, 0.8, 2.5);

            MarketSignal::firstOrCreate(
                ['casting_id' => $casting->id],
                [
                    'active_listing_count' => fake()->numberBetween(5, 50),
                    'wishlist_count' => fake()->numberBetween(10, 200),
                    'current_multiplier' => $currentMultiplier,
                    'target_multiplier' => $currentMultiplier + fake()->randomFloat(2, -0.2, 0.2),
                    'last_calculated_at' => now(),
                ]
            );

            // History 30 hari ke belakang untuk Chart
            if (MarketMultiplierHistory::where('casting_id', $casting->id)->count() === 0) {
                for ($i = 30; $i >= 0; $i--) {
                    MarketMultiplierHistory::create([
                        'casting_id' => $casting->id,
                        'multiplier' => max(0.5, $currentMultiplier + fake()->randomFloat(2, -0.3, 0.3)),
                        'recorded_at' => now()->subDays($i)->format('Y-m-d H:i:s'),
                    ]);
                }
            }
        }

        // 2. Collection Items & Listings
        $collectionItems = [];
        foreach ($users as $user) {
            $numItems = fake()->numberBetween(3, 5);
            for ($i = 0; $i < $numItems; $i++) {
                $casting = fake()->randomElement($castings);
                $condition = fake()->randomElement(['mint', 'loose', 'carded']);

                $item = CollectionItem::create([
                    'user_id' => $user->id,
                    'casting_id' => $casting->id,
                    'condition' => $condition,
                    'card_condition' => $condition === 'loose' ? null : fake()->randomElement(['perfect', 'creased', 'torn']),
                    'photos' => [],
                    'computed_value' => $casting->base_value,
                ]);
                $collectionItems[] = $item;

                // 60% probabilitas barang dipasang di market
                if (fake()->boolean(60)) {
                    Listing::create([
                        'user_id' => $user->id,
                        'collection_item_id' => $item->id,
                        'type' => 'for_trade',
                        'status' => 'active',
                        'notes' => fake()->sentence(),
                    ]);
                }
            }
        }

        // 3. Posts & Comments
        if ($club) {
            foreach (range(1, 12) as $i) {
                $post = Post::create([
                    'user_id' => $users->random()->id,
                    'club_id' => fake()->boolean(80) ? $club->id : null, // 80% post di club, 20% global
                    'content' => fake()->realText(100),
                    'created_at' => now()->subDays(fake()->numberBetween(0, 10)),
                ]);

                $numComments = fake()->numberBetween(0, 3);
                for ($c = 0; $c < $numComments; $c++) {
                    Comment::create([
                        'post_id' => $post->id,
                        'user_id' => $users->random()->id,
                        'content' => fake()->sentence(),
                        'created_at' => $post->created_at->addHours(fake()->numberBetween(1, 24)),
                    ]);
                }
            }
        }

        // 4. Trades
        // Transaksi 1: Completed (Selesai)
        $trade1 = Trade::create([
            'initiator_id' => $users[0]->id,
            'receiver_id' => $users[1]->id,
            'status' => 'completed',
            'accepted_initiator' => true,
            'accepted_receiver' => true,
            'created_at' => now()->subDays(5),
        ]);
        $initItem1 = CollectionItem::where('user_id', $users[0]->id)->first();
        $recItem1 = CollectionItem::where('user_id', $users[1]->id)->first();
        if ($initItem1) {
            TradeItem::create(['trade_id' => $trade1->id, 'collection_item_id' => $initItem1->id, 'user_id' => $users[0]->id]);
        }
        if ($recItem1) {
            TradeItem::create(['trade_id' => $trade1->id, 'collection_item_id' => $recItem1->id, 'user_id' => $users[1]->id]);
        }

        // Transaksi 2: Proposed (Baru diajukan)
        $trade2 = Trade::create([
            'initiator_id' => $users[2]->id,
            'receiver_id' => $users[3]->id,
            'status' => 'proposed',
            'created_at' => now()->subDays(1),
        ]);
        $initItem2 = CollectionItem::where('user_id', $users[2]->id)->first();
        if ($initItem2) {
            TradeItem::create(['trade_id' => $trade2->id, 'collection_item_id' => $initItem2->id, 'user_id' => $users[2]->id]);
        }

        // 5. Ratings
        // Rating dari initiator ke receiver
        Rating::create([
            'trade_id' => $trade1->id,
            'reviewer_id' => $users[0]->id,
            'reviewee_id' => $users[1]->id,
            'score' => fake()->numberBetween(4, 5),
            'comment' => fake()->sentence(),
        ]);

        // Rating dari receiver ke initiator
        Rating::create([
            'trade_id' => $trade1->id,
            'reviewer_id' => $users[1]->id,
            'reviewee_id' => $users[0]->id,
            'score' => fake()->numberBetween(4, 5),
            'comment' => fake()->sentence(),
        ]);
    }
}
