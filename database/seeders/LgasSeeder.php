<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LgasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Mapping of state names to their LGAs
        // Note: This is a representative sample. Production would need all 774 LGAs
        $lgasData = [
            'Lagos' => [
                'Agege', 'Ajeromi-Ifelodun', 'Alimosho', 'Amuwo-Odofin', 'Apapa',
                'Badagry', 'Epe', 'Eti Osa', 'Ibeju-Lekki', 'Ifako-Ijaiye',
                'Ikeja', 'Ikorodu', 'Kosofe', 'Lagos Island', 'Lagos Mainland',
                'Mushin', 'Ojo', 'Oshodi-Isolo', 'Shomolu', 'Surulere'
            ],
            'Federal Capital Territory' => [
                'Abaji', 'Abuja Municipal', 'Bwari', 'Gwagwalada', 'Kuje', 'Kwali'
            ],
            'Kano' => [
                'Ajingi', 'Albasu', 'Bagwai', 'Bebeji', 'Bichi', 'Bunkure',
                'Dala', 'Dambatta', 'Dawakin Kudu', 'Dawakin Tofa', 'Doguwa',
                'Fagge', 'Gabasawa', 'Garko', 'Garun Mallam', 'Gaya', 'Gezawa',
                'Gwale', 'Gwarzo', 'Kabo', 'Kano Municipal', 'Karaye', 'Kibiya',
                'Kiru', 'Kumbotso', 'Kunchi', 'Kura', 'Madobi', 'Makoda',
                'Minjibir', 'Nasarawa', 'Rano', 'Rimin Gado', 'Rogo', 'Shanono',
                'Sumaila', 'Takai', 'Tarauni', 'Tofa', 'Tsanyawa', 'Tudun Wada',
                'Ungogo', 'Warawa', 'Wudil'
            ],
            'Rivers' => [
                'Abua/Odual', 'Ahoada East', 'Ahoada West', 'Akuku-Toru', 'Andoni',
                'Asari-Toru', 'Bonny', 'Degema', 'Eleme', 'Emohua', 'Etche',
                'Gokana', 'Ikwerre', 'Khana', 'Obio/Akpor', 'Ogba/Egbema/Ndoni',
                'Ogu/Bolo', 'Okrika', 'Omuma', 'Opobo/Nkoro', 'Oyigbo',
                'Port Harcourt', 'Tai'
            ],
            'Oyo' => [
                'Afijio', 'Akinyele', 'Atiba', 'Atisbo', 'Egbeda', 'Ibadan North',
                'Ibadan North-East', 'Ibadan North-West', 'Ibadan South-East',
                'Ibadan South-West', 'Ibarapa Central', 'Ibarapa East', 'Ibarapa North',
                'Ido', 'Irepo', 'Iseyin', 'Itesiwaju', 'Iwajowa', 'Kajola', 'Lagelu',
                'Ogbomosho North', 'Ogbomosho South', 'Ogo Oluwa', 'Olorunsogo',
                'Oluyole', 'Ona Ara', 'Orelope', 'Ori Ire', 'Oyo East', 'Oyo West',
                'Saki East', 'Saki West', 'Surulere'
            ],
            'Kaduna' => [
                'Birnin Gwari', 'Chikun', 'Giwa', 'Igabi', 'Ikara', 'Jaba', 'Jema\'a',
                'Kachia', 'Kaduna North', 'Kaduna South', 'Kagarko', 'Kajuru',
                'Kaura', 'Kauru', 'Kubau', 'Kudan', 'Lere', 'Makarfi', 'Sabon Gari',
                'Sanga', 'Soba', 'Zangon Kataf', 'Zaria'
            ],
            'Abia' => [
                'Aba North', 'Aba South', 'Arochukwu', 'Bende', 'Ikwuano', 'Isiala Ngwa North',
                'Isiala Ngwa South', 'Isuikwuato', 'Obi Ngwa', 'Ohafia', 'Osisioma',
                'Ugwunagbo', 'Ukwa East', 'Ukwa West', 'Umuahia North', 'Umuahia South', 'Umu Nneochi'
            ],
            'Anambra' => [
                'Aguata', 'Anambra East', 'Anambra West', 'Anaocha', 'Awka North', 'Awka South',
                'Ayamelum', 'Dunukofia', 'Ekwusigo', 'Idemili North', 'Idemili South', 'Ihiala',
                'Njikoka', 'Nnewi North', 'Nnewi South', 'Ogbaru', 'Onitsha North', 'Onitsha South',
                'Orumba North', 'Orumba South', 'Oyi'
            ],
        ];

        foreach ($lgasData as $stateName => $lgas) {
            $state = DB::table('states')->where('name', $stateName)->first();

            if ($state) {
                foreach ($lgas as $lga) {
                    DB::table('lgas')->insert([
                        'state_id' => $state->id,
                        'name' => $lga,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
