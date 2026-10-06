import re

data = """
Exoticars: '16 Lamborghini Centenario Roadster, Gordon Murray T.33, McMurtry Spéirling, Pagani Zonda Cinque, 2024 Aston Martin Vantage GT3, X-Cceleron
Formula 1: McLaren Formula 1 Team, Scuderia Ferrari HP
Ferrari: SF90 Stradale, Dino 206 GT
HW Starting Grid: Instant Boost, '69 Dodge Charger Daytona, Track Ripper, Bugatti Bolide, Maserati Tipo 61, BMW M4 GT3
Drag Racers: Custom '72 Chevy LUV, Eggshelleracer
Nightspeed: '87 Buick Regal GNX|th, '20 Ford Mustang Shelby GT500, '18 Honda Civic Type R, '70 Dodge Hemi Challenger, Cupra E-Racer, 24/Seven, '20 Dodge Charger Hellcat
HW J-Imports: Honda Odyssey, '55 Nomad, Toyota Prius Custom, Nissan Skyline 2000 GT-R, '88 Mitsubishi Starion
HW Euro: Drift Box, BMW 635 CSi, VW ID. Buzz Bomb, Mercedes-Benz CLA HW
Factory Fresh: Porsche 911 Carrera T, Maserati Shamal, Ford Mustang GTD|sth, Custom '66 Toronado
Layin' Low: Bounce'N Bass, '59 Chevy Impala, '47 Chevy Fleetline
Rod Squad: Custom '41 Willys Coupe, Erikenstein Rod, Mid Mill
HW Mods: High-Tail Chaser, Vairstrike
Experimotors: Carbonator, Roller Toaster, Twin Tags, Feline Lucky
HW Fan Driven: Deora II, Rodger Dodger, Purple Passion, Bone Shaker
Compact Kings: Hako Type D
Torque: Ridge Crest GT
Truckin' Along: Drift-Ender|sth, '20 Jeep Gladiator, '83 Chevy Silverado, Ford F-150 Lightning Supertruck
HW Heavy Weights: 5 Alarm, Haulerback, Raijin Express
HW Dirt: El Segundo Rallye, Mazda Autozam, Duck N' Roll
Wagons: 8 Crate, Lil' Mad
Drop Tops: Pass 'N Go, '92 Dodge Viper RT/10|th, Fiat Beast of Turin
HW EV: Solar Reflex, Ford Mustang Mach-E 1400, Porsche Taycan Turbo S, Wattzup, Cadillac Celestiq, Custom '20 Honda e
Screen Time: Optimus Prime, HW K.I.T.T. Concept, Standard Kart
Batman: Batmobile (The Batman), Tooned Classic TV Series Batmobile
Tooned: Tooned Purple Passion, Deora Too'nd
X-Raycers: RD-06, X-Steam, Bullet Proof
HW Dream Garage: Mazda MX-5 Miata, La Liebre, 2 Jet Z|th
Then and Now: '20 Toyota GR Supra, Austin Mini Cooper S
HW All Drivers Welcome: HW Braille Racer – Twin Mill, Flippin Fast, Click Shift
Mattel: Barbie Dream Camper, Monster High Ghoul Mobile, Custom Otto|sth
Sweet Rides: Birthday Burner
"""

lines = [line.strip() for line in data.strip().split('\n')]

php_output = []
csv_output = []

for line in lines:
    series, cars_str = line.split(': ', 1)
    cars = [c.strip() for c in cars_str.split(',')]
    for car in cars:
        parts = car.split('|')
        name = parts[0].replace("'", "\\'")
        rarity = parts[1] if len(parts) > 1 else 'mainline'
        base_value = 50000
        if rarity == 'th': base_value = 350000
        elif rarity == 'sth': base_value = 750000
        
        php_output.append(f"            ['name' => '{name}', 'series' => '{series}', 'year' => 2026, 'rarity_tier' => '{rarity}', 'base_value' => {base_value}],")
        
        csv_name = parts[0]
        # if name has comma, wrap in quotes
        if ',' in csv_name: csv_name = f'"{csv_name}"'
        csv_output.append(f"{csv_name},{series},2026,{rarity},{base_value/10000:.2f}")

with open('seed_php.txt', 'w') as f:
    f.write('\n'.join(php_output) + '\n')

with open('seed_csv.txt', 'w') as f:
    f.write('\n'.join(csv_output) + '\n')

