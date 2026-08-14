<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateIndonesiaAddressData extends Command
{
    protected $signature = 'address:generate-indonesia';
    protected $description = 'Generate comprehensive Indonesia administrative divisions JSON data';

    public function handle()
    {
        $this->info('Generating Indonesia address data...');

        $data = [
            'provinces' => [],
            'cities' => [],
            'districts' => [],
            'villages' => [],
        ];

        $indonesiaData = $this->getIndonesiaData();

        foreach ($indonesiaData as $province) {
            $data['provinces'][] = [
                'code' => $province['code'],
                'name' => $province['name'],
            ];

            foreach ($province['cities'] as $city) {
                $data['cities'][] = [
                    'code' => $city['code'],
                    'province_code' => $province['code'],
                    'name' => $city['name'],
                ];

                foreach ($city['districts'] as $district) {
                    $data['districts'][] = [
                        'code' => $district['code'],
                        'city_code' => $city['code'],
                        'name' => $district['name'],
                    ];

                    foreach ($district['villages'] as $village) {
                        $data['villages'][] = [
                            'code' => $village['code'],
                            'district_code' => $district['code'],
                            'name' => $village['name'],
                        ];
                    }
                }
            }
        }

        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        file_put_contents(public_path('data/indonesia-address.json'), $json);

        $this->info('Generated:');
        $this->info('  Provinces: ' . count($data['provinces']));
        $this->info('  Cities: ' . count($data['cities']));
        $this->info('  Districts: ' . count($data['districts']));
        $this->info('  Villages: ' . count($data['villages']));
        $this->info('Saved to: ' . public_path('data/indonesia-address.json'));
    }

    protected function getIndonesiaData()
    {
        return [
            [
                'code' => '11',
                'name' => 'Aceh',
                'cities' => [
                    [
                        'code' => '1101',
                        'name' => 'Kabupaten Aceh Besar',
                        'districts' => $this->generateDistricts('1101', 'Aceh Besar', 36),
                    ],
                    [
                        'code' => '1102',
                        'name' => 'Kabupaten Aceh Jaya',
                        'districts' => $this->generateDistricts('1102', 'Aceh Jaya', 9),
                    ],
                    [
                        'code' => '1103',
                        'name' => 'Kabupaten Bireuen',
                        'districts' => $this->generateDistricts('1103', 'Bireuen', 17),
                    ],
                    [
                        'code' => '1104',
                        'name' => 'Kabupaten Pidie',
                        'districts' => $this->generateDistricts('1104', 'Pidie', 23),
                    ],
                    [
                        'code' => '1105',
                        'name' => 'Kabupaten Simeulue',
                        'districts' => $this->generateDistricts('1105', 'Simeulue', 10),
                    ],
                    [
                        'code' => '1171',
                        'name' => 'Kota Banda Aceh',
                        'districts' => $this->generateDistricts('1171', 'Banda Aceh', 9),
                    ],
                    [
                        'code' => '1172',
                        'name' => 'Kota Sabang',
                        'districts' => $this->generateDistricts('1172', 'Sabang', 5),
                    ],
                    [
                        'code' => '1173',
                        'name' => 'Kota Langsa',
                        'districts' => $this->generateDistricts('1173', 'Langsa', 5),
                    ],
                    [
                        'code' => '1174',
                        'name' => 'Kota Lhokseumawe',
                        'districts' => $this->generateDistricts('1174', 'Lhokseumawe', 5),
                    ],
                ],
            ],
            [
                'code' => '12',
                'name' => 'Sumatera Utara',
                'cities' => [
                    [
                        'code' => '1201',
                        'name' => 'Kabupaten Asahan',
                        'districts' => $this->generateDistricts('1201', 'Asahan', 20),
                    ],
                    [
                        'code' => '1202',
                        'name' => 'Kabupaten Batubara',
                        'districts' => $this->generateDistricts('1202', 'Batubara', 11),
                    ],
                    [
                        'code' => '1203',
                        'name' => 'Kabupaten Dairi',
                        'districts' => $this->generateDistricts('1203', 'Dairi', 15),
                    ],
                    [
                        'code' => '1204',
                        'name' => 'Kabupaten Deli Serdang',
                        'districts' => $this->generateDistricts('1204', 'Deli Serdang', 38),
                    ],
                    [
                        'code' => '1205',
                        'name' => 'Kabupaten Humbang Hasundutan',
                        'districts' => $this->generateDistricts('1205', 'Humbang Hasundutan', 10),
                    ],
                    [
                        'code' => '1206',
                        'name' => 'Kabupaten Karo',
                        'districts' => $this->generateDistricts('1206', 'Karo', 17),
                    ],
                    [
                        'code' => '1207',
                        'name' => 'Kabupaten Labuhanbatu',
                        'districts' => $this->generateDistricts('1207', 'Labuhanbatu', 12),
                    ],
                    [
                        'code' => '1208',
                        'name' => 'Kabupaten Labuhanbatu Selatan',
                        'districts' => $this->generateDistricts('1208', 'Labuhanbatu Selatan', 5),
                    ],
                    [
                        'code' => '1209',
                        'name' => 'Kabupaten Labuhanbatu Utara',
                        'districts' => $this->generateDistricts('1209', 'Labuhanbatu Utara', 6),
                    ],
                    [
                        'code' => '1210',
                        'name' => 'Kabupaten Langkat',
                        'districts' => $this->generateDistricts('1210', 'Langkat', 23),
                    ],
                    [
                        'code' => '1211',
                        'name' => 'Kabupaten Mandailing Natal',
                        'districts' => $this->generateDistricts('1211', 'Mandailing Natal', 23),
                    ],
                    [
                        'code' => '1212',
                        'name' => 'Kabupaten Nias',
                        'districts' => $this->generateDistricts('1212', 'Nias', 10),
                    ],
                    [
                        'code' => '1213',
                        'name' => 'Kabupaten Nias Barat',
                        'districts' => $this->generateDistricts('1213', 'Nias Barat', 5),
                    ],
                    [
                        'code' => '1214',
                        'name' => 'Kabupaten Nias Selatan',
                        'districts' => $this->generateDistricts('1214', 'Nias Selatan', 13),
                    ],
                    [
                        'code' => '1215',
                        'name' => 'Kabupaten Nias Utara',
                        'districts' => $this->generateDistricts('1215', 'Nias Utara', 9),
                    ],
                    [
                        'code' => '1271',
                        'name' => 'Kota Medan',
                        'districts' => $this->generateDistricts('1271', 'Medan', 21),
                    ],
                    [
                        'code' => '1272',
                        'name' => 'Kota Binjai',
                        'districts' => $this->generateDistricts('1272', 'Binjai', 5),
                    ],
                    [
                        'code' => '1273',
                        'name' => 'Kota Gunungsitoli',
                        'districts' => $this->generateDistricts('1273', 'Gunungsitoli', 6),
                    ],
                    [
                        'code' => '1274',
                        'name' => 'Kota Padangsidimpuan',
                        'districts' => $this->generateDistricts('1274', 'Padangsidimpuan', 5),
                    ],
                    [
                        'code' => '1275',
                        'name' => 'Kota Pematangsiantar',
                        'districts' => $this->generateDistricts('1275', 'Pematangsiantar', 6),
                    ],
                    [
                        'code' => '1276',
                        'name' => 'Kota Sibolga',
                        'districts' => $this->generateDistricts('1276', 'Sibolga', 4),
                    ],
                    [
                        'code' => '1277',
                        'name' => 'Kota Tanjungbalai',
                        'districts' => $this->generateDistricts('1277', 'Tanjungbalai', 5),
                    ],
                    [
                        'code' => '1278',
                        'name' => 'Kota Tebing Tinggi',
                        'districts' => $this->generateDistricts('1278', 'Tebing Tinggi', 5),
                    ],
                ],
            ],
            [
                'code' => '13',
                'name' => 'Sumatera Barat',
                'cities' => [
                    [
                        'code' => '1301',
                        'name' => 'Kabupaten Agam',
                        'districts' => $this->generateDistricts('1301', 'Agam', 16),
                    ],
                    [
                        'code' => '1302',
                        'name' => 'Kabupaten Dharmasraya',
                        'districts' => $this->generateDistricts('1302', 'Dharmasraya', 11),
                    ],
                    [
                        'code' => '1303',
                        'name' => 'Kabupaten Kepulauan Mentawai',
                        'districts' => $this->generateDistricts('1303', 'Mentawai', 10),
                    ],
                    [
                        'code' => '1304',
                        'name' => 'Kabupaten Lima Puluh Kota',
                        'districts' => $this->generateDistricts('1304', 'Lima Puluh Kota', 13),
                    ],
                    [
                        'code' => '1305',
                        'name' => 'Kabupaten Padang Pariaman',
                        'districts' => $this->generateDistricts('1305', 'Padang Pariaman', 17),
                    ],
                    [
                        'code' => '1306',
                        'name' => 'Kabupaten Pasaman',
                        'districts' => $this->generateDistricts('1306', 'Pasaman', 11),
                    ],
                    [
                        'code' => '1307',
                        'name' => 'Kabupaten Pasaman Barat',
                        'districts' => $this->generateDistricts('1307', 'Pasaman Barat', 10),
                    ],
                    [
                        'code' => '1308',
                        'name' => 'Kabupaten Pesisir Selatan',
                        'districts' => $this->generateDistricts('1308', 'Pesisir Selatan', 15),
                    ],
                    [
                        'code' => '1309',
                        'name' => 'Kabupaten Solok',
                        'districts' => $this->generateDistricts('1309', 'Solok', 14),
                    ],
                    [
                        'code' => '1310',
                        'name' => 'Kabupaten Solok Selatan',
                        'districts' => $this->generateDistricts('1310', 'Solok Selatan', 7),
                    ],
                    [
                        'code' => '1311',
                        'name' => 'Kabupaten Tanah Datar',
                        'districts' => $this->generateDistricts('1311', 'Tanah Datar', 14),
                    ],
                    [
                        'code' => '1371',
                        'name' => 'Kota Padang',
                        'districts' => $this->generateDistricts('1371', 'Padang', 11),
                    ],
                    [
                        'code' => '1372',
                        'name' => 'Kota Bukittinggi',
                        'districts' => $this->generateDistricts('1372', 'Bukittinggi', 3),
                    ],
                    [
                        'code' => '1373',
                        'name' => 'Kota Padang Panjang',
                        'districts' => $this->generateDistricts('1373', 'Padang Panjang', 2),
                    ],
                    [
                        'code' => '1374',
                        'name' => 'Kota Pariaman',
                        'districts' => $this->generateDistricts('1374', 'Pariaman', 4),
                    ],
                    [
                        'code' => '1375',
                        'name' => 'Kota Payakumbuh',
                        'districts' => $this->generateDistricts('1375', 'Payakumbuh', 5),
                    ],
                    [
                        'code' => '1376',
                        'name' => 'Kota Sawahlunto',
                        'districts' => $this->generateDistricts('1376', 'Sawahlunto', 4),
                    ],
                    [
                        'code' => '1377',
                        'name' => 'Kota Solok',
                        'districts' => $this->generateDistricts('1377', 'Solok', 3),
                    ],
                ],
            ],
            [
                'code' => '14',
                'name' => 'Riau',
                'cities' => [
                    [
                        'code' => '1401',
                        'name' => 'Kabupaten Indragiri Hilir',
                        'districts' => $this->generateDistricts('1401', 'Indragiri Hilir', 20),
                    ],
                    [
                        'code' => '1402',
                        'name' => 'Kabupaten Indragiri Hulu',
                        'districts' => $this->generateDistricts('1402', 'Indragiri Hulu', 14),
                    ],
                    [
                        'code' => '1403',
                        'name' => 'Kabupaten Kampar',
                        'districts' => $this->generateDistricts('1403', 'Kampar', 21),
                    ],
                    [
                        'code' => '1404',
                        'name' => 'Kabupaten Kuantan Singingi',
                        'districts' => $this->generateDistricts('1404', 'Kuantan Singingi', 15),
                    ],
                    [
                        'code' => '1405',
                        'name' => 'Kabupaten Pelalawan',
                        'districts' => $this->generateDistricts('1405', 'Pelalawan', 12),
                    ],
                    [
                        'code' => '1406',
                        'name' => 'Kabupaten Siak',
                        'districts' => $this->generateDistricts('1406', 'Siak', 14),
                    ],
                    [
                        'code' => '1407',
                        'name' => 'Kabupaten Bengkalis',
                        'districts' => $this->generateDistricts('1407', 'Bengkalis', 11),
                    ],
                    [
                        'code' => '1408',
                        'name' => 'Kabupaten Rokan Hilir',
                        'districts' => $this->generateDistricts('1408', 'Rokan Hilir', 16),
                    ],
                    [
                        'code' => '1409',
                        'name' => 'Kabupaten Rokan Hulu',
                        'districts' => $this->generateDistricts('1409', 'Rokan Hulu', 14),
                    ],
                    [
                        'code' => '1410',
                        'name' => 'Kabupaten Kepulauan Meranti',
                        'districts' => $this->generateDistricts('1410', 'Kepulauan Meranti', 7),
                    ],
                    [
                        'code' => '1471',
                        'name' => 'Kota Pekanbaru',
                        'districts' => $this->generateDistricts('1471', 'Pekanbaru', 12),
                    ],
                    [
                        'code' => '1472',
                        'name' => 'Kota Dumai',
                        'districts' => $this->generateDistricts('1472', 'Dumai', 5),
                    ],
                ],
            ],
            [
                'code' => '15',
                'name' => 'Jambi',
                'cities' => [
                    [
                        'code' => '1501',
                        'name' => 'Kabupaten Batang Hari',
                        'districts' => $this->generateDistricts('1501', 'Batang Hari', 12),
                    ],
                    [
                        'code' => '1502',
                        'name' => 'Kabupaten Bungo',
                        'districts' => $this->generateDistricts('1502', 'Bungo', 17),
                    ],
                    [
                        'code' => '1503',
                        'name' => 'Kabupaten Kerinci',
                        'districts' => $this->generateDistricts('1503', 'Kerinci', 16),
                    ],
                    [
                        'code' => '1504',
                        'name' => 'Kabupaten Merangin',
                        'districts' => $this->generateDistricts('1504', 'Merangin', 18),
                    ],
                    [
                        'code' => '1505',
                        'name' => 'Kabupaten Muaro Jambi',
                        'districts' => $this->generateDistricts('1505', 'Muaro Jambi', 11),
                    ],
                    [
                        'code' => '1506',
                        'name' => 'Kabupaten Sarolangun',
                        'districts' => $this->generateDistricts('1506', 'Sarolangun', 10),
                    ],
                    [
                        'code' => '1507',
                        'name' => 'Kabupaten Tanjung Jabung Barat',
                        'districts' => $this->generateDistricts('1507', 'Tanjung Jabung Barat', 13),
                    ],
                    [
                        'code' => '1508',
                        'name' => 'Kabupaten Tanjung Jabung Timur',
                        'districts' => $this->generateDistricts('1508', 'Tanjung Jabung Timur', 11),
                    ],
                    [
                        'code' => '1509',
                        'name' => 'Kabupaten Tebo',
                        'districts' => $this->generateDistricts('1509', 'Tebo', 12),
                    ],
                    [
                        'code' => '1571',
                        'name' => 'Kota Jambi',
                        'districts' => $this->generateDistricts('1571', 'Jambi', 9),
                    ],
                    [
                        'code' => '1572',
                        'name' => 'Kota Sungai Penuh',
                        'districts' => $this->generateDistricts('1572', 'Sungai Penuh', 4),
                    ],
                ],
            ],
            [
                'code' => '16',
                'name' => 'Sumatera Selatan',
                'cities' => [
                    [
                        'code' => '1601',
                        'name' => 'Kabupaten Banyu Asin',
                        'districts' => $this->generateDistricts('1601', 'Banyu Asin', 19),
                    ],
                    [
                        'code' => '1602',
                        'name' => 'Kabupaten Empat Lawang',
                        'districts' => $this->generateDistricts('1602', 'Empat Lawang', 9),
                    ],
                    [
                        'code' => '1603',
                        'name' => 'Kabupaten Lahat',
                        'districts' => $this->generateDistricts('1603', 'Lahat', 24),
                    ],
                    [
                        'code' => '1604',
                        'name' => 'Kabupaten Muara Enim',
                        'districts' => $this->generateDistricts('1604', 'Muara Enim', 16),
                    ],
                    [
                        'code' => '1605',
                        'name' => 'Kabupaten Musi Banyu Asin',
                        'districts' => $this->generateDistricts('1605', 'Musi Banyu Asin', 10),
                    ],
                    [
                        'code' => '1606',
                        'name' => 'Kabupaten Musi Rawas',
                        'districts' => $this->generateDistricts('1606', 'Musi Rawas', 14),
                    ],
                    [
                        'code' => '1607',
                        'name' => 'Kabupaten Musi Rawas Utara',
                        'districts' => $this->generateDistricts('1607', 'Musi Rawas Utara', 7),
                    ],
                    [
                        'code' => '1608',
                        'name' => 'Kabupaten Ogan Ilir',
                        'districts' => $this->generateDistricts('1608', 'Ogan Ilir', 16),
                    ],
                    [
                        'code' => '1609',
                        'name' => 'Kabupaten Ogan Komering Ilir',
                        'districts' => $this->generateDistricts('1609', 'Ogan Komering Ilir', 29),
                    ],
                    [
                        'code' => '1610',
                        'name' => 'Kabupaten Ogan Komering Ulu',
                        'districts' => $this->generateDistricts('1610', 'Ogan Komering Ulu', 13),
                    ],
                    [
                        'code' => '1611',
                        'name' => 'Kabupaten Ogan Komering Ulu Selatan',
                        'districts' => $this->generateDistricts('1611', 'Ogan Komering Ulu Selatan', 19),
                    ],
                    [
                        'code' => '1612',
                        'name' => 'Kabupaten Ogan Komering Ulu Timur',
                        'districts' => $this->generateDistricts('1612', 'Ogan Komering Ulu Timur', 20),
                    ],
                    [
                        'code' => '1671',
                        'name' => 'Kota Palembang',
                        'districts' => $this->generateDistricts('1671', 'Palembang', 18),
                    ],
                    [
                        'code' => '1672',
                        'name' => 'Kota Lubuklinggau',
                        'districts' => $this->generateDistricts('1672', 'Lubuklinggau', 8),
                    ],
                    [
                        'code' => '1673',
                        'name' => 'Kota Pagar Alam',
                        'districts' => $this->generateDistricts('1673', 'Pagar Alam', 5),
                    ],
                    [
                        'code' => '1674',
                        'name' => 'Kota Prabumulih',
                        'districts' => $this->generateDistricts('1674', 'Prabumulih', 5),
                    ],
                ],
            ],
            [
                'code' => '17',
                'name' => 'Bengkulu',
                'cities' => [
                    [
                        'code' => '1701',
                        'name' => 'Kabupaten Bengkulu Selatan',
                        'districts' => $this->generateDistricts('1701', 'Bengkulu Selatan', 11),
                    ],
                    [
                        'code' => '1702',
                        'name' => 'Kabupaten Bengkulu Tengah',
                        'districts' => $this->generateDistricts('1702', 'Bengkulu Tengah', 8),
                    ],
                    [
                        'code' => '1703',
                        'name' => 'Kabupaten Bengkulu Utara',
                        'districts' => $this->generateDistricts('1703', 'Bengkulu Utara', 19),
                    ],
                    [
                        'code' => '1704',
                        'name' => 'Kabupaten Kaur',
                        'districts' => $this->generateDistricts('1704', 'Kaur', 15),
                    ],
                    [
                        'code' => '1705',
                        'name' => 'Kabupaten Kepahiang',
                        'districts' => $this->generateDistricts('1705', 'Kepahiang', 7),
                    ],
                    [
                        'code' => '1706',
                        'name' => 'Kabupaten Lebong',
                        'districts' => $this->generateDistricts('1706', 'Lebong', 9),
                    ],
                    [
                        'code' => '1707',
                        'name' => 'Kabupaten Mukomuko',
                        'districts' => $this->generateDistricts('1707', 'Mukomuko', 15),
                    ],
                    [
                        'code' => '1708',
                        'name' => 'Kabupaten Rejang Lebong',
                        'districts' => $this->generateDistricts('1708', 'Rejang Lebong', 15),
                    ],
                    [
                        'code' => '1709',
                        'name' => 'Kabupaten Seluma',
                        'districts' => $this->generateDistricts('1709', 'Seluma', 16),
                    ],
                    [
                        'code' => '1771',
                        'name' => 'Kota Bengkulu',
                        'districts' => $this->generateDistricts('1771', 'Bengkulu', 9),
                    ],
                ],
            ],
            [
                'code' => '18',
                'name' => 'Lampung',
                'cities' => [
                    [
                        'code' => '1801',
                        'name' => 'Kabupaten Bandar Lampung',
                        'districts' => $this->generateDistricts('1801', 'Bandar Lampung', 12),
                    ],
                    [
                        'code' => '1802',
                        'name' => 'Kabupaten East Lampung',
                        'districts' => $this->generateDistricts('1802', 'Lampung Timur', 24),
                    ],
                    [
                        'code' => '1803',
                        'name' => 'Kabupaten Lampung Barat',
                        'districts' => $this->generateDistricts('1803', 'Lampung Barat', 15),
                    ],
                    [
                        'code' => '1804',
                        'name' => 'Kabupaten Lampung Selatan',
                        'districts' => $this->generateDistricts('1804', 'Lampung Selatan', 22),
                    ],
                    [
                        'code' => '1805',
                        'name' => 'Kabupaten Lampung Tengah',
                        'districts' => $this->generateDistricts('1805', 'Lampung Tengah', 28),
                    ],
                    [
                        'code' => '1806',
                        'name' => 'Kabupaten Lampung Timur',
                        'districts' => $this->generateDistricts('1806', 'Lampung Timur', 24),
                    ],
                    [
                        'code' => '1807',
                        'name' => 'Kabupaten Lampung Utara',
                        'districts' => $this->generateDistricts('1807', 'Lampung Utara', 20),
                    ],
                    [
                        'code' => '1808',
                        'name' => 'Kabupaten Mesuji',
                        'districts' => $this->generateDistricts('1808', 'Mesuji', 7),
                    ],
                    [
                        'code' => '1809',
                        'name' => 'Kabupaten Pesawaran',
                        'districts' => $this->generateDistricts('1809', 'Pesawaran', 11),
                    ],
                    [
                        'code' => '1810',
                        'name' => 'Kabupaten Pesisir Barat',
                        'districts' => $this->generateDistricts('1810', 'Pesisir Barat', 11),
                    ],
                    [
                        'code' => '1811',
                        'name' => 'Kabupaten Pringsewu',
                        'districts' => $this->generateDistricts('1811', 'Pringsewu', 9),
                    ],
                    [
                        'code' => '1812',
                        'name' => 'Kabupaten Tanggamus',
                        'districts' => $this->generateDistricts('1812', 'Tanggamus', 20),
                    ],
                    [
                        'code' => '1813',
                        'name' => 'Kabupaten Tulang Bawang',
                        'districts' => $this->generateDistricts('1813', 'Tulang Bawang', 13),
                    ],
                    [
                        'code' => '1814',
                        'name' => 'Kabupaten Tulang Bawang Barat',
                        'districts' => $this->generateDistricts('1814', 'Tulang Bawang Barat', 9),
                    ],
                    [
                        'code' => '1815',
                        'name' => 'Kabupaten Way Kanan',
                        'districts' => $this->generateDistricts('1815', 'Way Kanan', 14),
                    ],
                    [
                        'code' => '1871',
                        'name' => 'Kota Bandar Lampung',
                        'districts' => $this->generateDistricts('1871', 'Bandar Lampung', 13),
                    ],
                    [
                        'code' => '1872',
                        'name' => 'Kota Metro',
                        'districts' => $this->generateDistricts('1872', 'Metro', 5),
                    ],
                ],
            ],
            [
                'code' => '19',
                'name' => 'Kepulauan Bangka Belitung',
                'cities' => [
                    [
                        'code' => '1901',
                        'name' => 'Kabupaten Bangka',
                        'districts' => $this->generateDistricts('1901', 'Bangka', 8),
                    ],
                    [
                        'code' => '1902',
                        'name' => 'Kabupaten Bangka Barat',
                        'districts' => $this->generateDistricts('1902', 'Bangka Barat', 6),
                    ],
                    [
                        'code' => '1903',
                        'name' => 'Kabupaten Bangka Selatan',
                        'districts' => $this->generateDistricts('1903', 'Bangka Selatan', 8),
                    ],
                    [
                        'code' => '1904',
                        'name' => 'Kabupaten Bangka Tengah',
                        'districts' => $this->generateDistricts('1904', 'Bangka Tengah', 7),
                    ],
                    [
                        'code' => '1905',
                        'name' => 'Kabupaten Belitung',
                        'districts' => $this->generateDistricts('1905', 'Belitung', 9),
                    ],
                    [
                        'code' => '1906',
                        'name' => 'Kabupaten Belitung Timur',
                        'districts' => $this->generateDistricts('1906', 'Belitung Timur', 5),
                    ],
                    [
                        'code' => '1971',
                        'name' => 'Kota Pangkal Pinang',
                        'districts' => $this->generateDistricts('1971', 'Pangkal Pinang', 5),
                    ],
                ],
            ],
            [
                'code' => '21',
                'name' => 'Kepulauan Riau',
                'cities' => [
                    [
                        'code' => '2101',
                        'name' => 'Kabupaten Bintan',
                        'districts' => $this->generateDistricts('2101', 'Bintan', 9),
                    ],
                    [
                        'code' => '2102',
                        'name' => 'Kabupaten Karimun',
                        'districts' => $this->generateDistricts('2102', 'Karimun', 12),
                    ],
                    [
                        'code' => '2103',
                        'name' => 'Kabupaten Kepulauan Anambas',
                        'districts' => $this->generateDistricts('2103', 'Anambas', 6),
                    ],
                    [
                        'code' => '2104',
                        'name' => 'Kabupaten Lingga',
                        'districts' => $this->generateDistricts('2104', 'Lingga', 8),
                    ],
                    [
                        'code' => '2105',
                        'name' => 'Kabupaten Natuna',
                        'districts' => $this->generateDistricts('2105', 'Natuna', 16),
                    ],
                    [
                        'code' => '2171',
                        'name' => 'Kota Batam',
                        'districts' => $this->generateDistricts('2171', 'Batam', 12),
                    ],
                    [
                        'code' => '2172',
                        'name' => 'Kota Tanjung Pinang',
                        'districts' => $this->generateDistricts('2172', 'Tanjung Pinang', 4),
                    ],
                ],
            ],
            [
                'code' => '31',
                'name' => 'DKI Jakarta',
                'cities' => [
                    [
                        'code' => '3101',
                        'name' => 'Kabupaten Adm. Kepulauan Seribu',
                        'districts' => $this->generateDistricts('3101', 'Kepulauan Seribu', 6),
                    ],
                    [
                        'code' => '3171',
                        'name' => 'Kota Jakarta Barat',
                        'districts' => $this->generateDistricts('3171', 'Jakarta Barat', 8),
                    ],
                    [
                        'code' => '3172',
                        'name' => 'Kota Jakarta Pusat',
                        'districts' => $this->generateDistricts('3172', 'Jakarta Pusat', 8),
                    ],
                    [
                        'code' => '3173',
                        'name' => 'Kota Jakarta Selatan',
                        'districts' => $this->generateDistricts('3173', 'Jakarta Selatan', 10),
                    ],
                    [
                        'code' => '3174',
                        'name' => 'Kota Jakarta Timur',
                        'districts' => $this->generateDistricts('3174', 'Jakarta Timur', 10),
                    ],
                    [
                        'code' => '3175',
                        'name' => 'Kota Jakarta Utara',
                        'districts' => $this->generateDistricts('3175', 'Jakarta Utara', 6),
                    ],
                ],
            ],
            [
                'code' => '32',
                'name' => 'Jawa Barat',
                'cities' => [
                    [
                        'code' => '3201',
                        'name' => 'Kabupaten Bandung',
                        'districts' => $this->generateDistricts('3201', 'Bandung', 23),
                    ],
                    [
                        'code' => '3202',
                        'name' => 'Kabupaten Bandung Barat',
                        'districts' => $this->generateDistricts('3202', 'Bandung Barat', 16),
                    ],
                    [
                        'code' => '3203',
                        'name' => 'Kabupaten Bekasi',
                        'districts' => $this->generateDistricts('3203', 'Bekasi', 23),
                    ],
                    [
                        'code' => '3204',
                        'name' => 'Kabupaten Bogor',
                        'districts' => $this->generateDistricts('3204', 'Bogor', 40),
                    ],
                    [
                        'code' => '3205',
                        'name' => 'Kabupaten Ciamis',
                        'districts' => $this->generateDistricts('3205', 'Ciamis', 27),
                    ],
                    [
                        'code' => '3206',
                        'name' => 'Kabupaten Cianjur',
                        'districts' => $this->generateDistricts('3206', 'Cianjur', 32),
                    ],
                    [
                        'code' => '3207',
                        'name' => 'Kabupaten Cirebon',
                        'districts' => $this->generateDistricts('3207', 'Cirebon', 40),
                    ],
                    [
                        'code' => '3208',
                        'name' => 'Kabupaten Garut',
                        'districts' => $this->generateDistricts('3208', 'Garut', 21),
                    ],
                    [
                        'code' => '3209',
                        'name' => 'Kabupaten Indramayu',
                        'districts' => $this->generateDistricts('3209', 'Indramayu', 31),
                    ],
                    [
                        'code' => '3210',
                        'name' => 'Kabupaten Karawang',
                        'districts' => $this->generateDistricts('3210', 'Karawang', 12),
                    ],
                    [
                        'code' => '3211',
                        'name' => 'Kabupaten Kuningan',
                        'districts' => $this->generateDistricts('3211', 'Kuningan', 32),
                    ],
                    [
                        'code' => '3212',
                        'name' => 'Kabupaten Majalengka',
                        'districts' => $this->generateDistricts('3212', 'Majalengka', 26),
                    ],
                    [
                        'code' => '3213',
                        'name' => 'Kabupaten Pangandaran',
                        'districts' => $this->generateDistricts('3213', 'Pangandaran', 10),
                    ],
                    [
                        'code' => '3214',
                        'name' => 'Kabupaten Purwakarta',
                        'districts' => $this->generateDistricts('3214', 'Purwakarta', 17),
                    ],
                    [
                        'code' => '3215',
                        'name' => 'Kabupaten Subang',
                        'districts' => $this->generateDistricts('3215', 'Subang', 20),
                    ],
                    [
                        'code' => '3216',
                        'name' => 'Kabupaten Sukabumi',
                        'districts' => $this->generateDistricts('3216', 'Sukabumi', 47),
                    ],
                    [
                        'code' => '3217',
                        'name' => 'Kabupaten Sumedang',
                        'districts' => $this->generateDistricts('3217', 'Sumedang', 26),
                    ],
                    [
                        'code' => '3218',
                        'name' => 'Kabupaten Tasikmalaya',
                        'districts' => $this->generateDistricts('3218', 'Tasikmalaya', 39),
                    ],
                    [
                        'code' => '3271',
                        'name' => 'Kota Bandung',
                        'districts' => $this->generateDistricts('3271', 'Bandung', 10),
                    ],
                    [
                        'code' => '3272',
                        'name' => 'Kota Banjar',
                        'districts' => $this->generateDistricts('3272', 'Banjar', 4),
                    ],
                    [
                        'code' => '3273',
                        'name' => 'Kota Bekasi',
                        'districts' => $this->generateDistricts('3273', 'Bekasi', 12),
                    ],
                    [
                        'code' => '3274',
                        'name' => 'Kota Bogor',
                        'districts' => $this->generateDistricts('3274', 'Bogor', 6),
                    ],
                    [
                        'code' => '3275',
                        'name' => 'Kota Cimahi',
                        'districts' => $this->generateDistricts('3275', 'Cimahi', 3),
                    ],
                    [
                        'code' => '3276',
                        'name' => 'Kota Cirebon',
                        'districts' => $this->generateDistricts('3276', 'Cirebon', 5),
                    ],
                    [
                        'code' => '3277',
                        'name' => 'Kota Depok',
                        'districts' => $this->generateDistricts('3277', 'Depok', 11),
                    ],
                    [
                        'code' => '3278',
                        'name' => 'Kota Sukabumi',
                        'districts' => $this->generateDistricts('3278', 'Sukabumi', 6),
                    ],
                    [
                        'code' => '3279',
                        'name' => 'Kota Tasikmalaya',
                        'districts' => $this->generateDistricts('3279', 'Tasikmalaya', 10),
                    ],
                ],
            ],
            [
                'code' => '33',
                'name' => 'Jawa Tengah',
                'cities' => [
                    [
                        'code' => '3301',
                        'name' => 'Kabupaten Banjarnegara',
                        'districts' => $this->generateDistricts('3301', 'Banjarnegara', 20),
                    ],
                    [
                        'code' => '3302',
                        'name' => 'Kabupaten Banyumas',
                        'districts' => $this->generateDistricts('3302', 'Banyumas', 27),
                    ],
                    [
                        'code' => '3303',
                        'name' => 'Kabupaten Batang',
                        'districts' => $this->generateDistricts('3303', 'Batang', 13),
                    ],
                    [
                        'code' => '3304',
                        'name' => 'Kabupaten Blora',
                        'districts' => $this->generateDistricts('3304', 'Blora', 16),
                    ],
                    [
                        'code' => '3305',
                        'name' => 'Kabupaten Boyolali',
                        'districts' => $this->generateDistricts('3305', 'Boyolali', 19),
                    ],
                    [
                        'code' => '3306',
                        'name' => 'Kabupaten Brebes',
                        'districts' => $this->generateDistricts('3306', 'Brebes', 17),
                    ],
                    [
                        'code' => '3307',
                        'name' => 'Kabupaten Cilacap',
                        'districts' => $this->generateDistricts('3307', 'Cilacap', 24),
                    ],
                    [
                        'code' => '3308',
                        'name' => 'Kabupaten Demak',
                        'districts' => $this->generateDistricts('3308', 'Demak', 20),
                    ],
                    [
                        'code' => '3309',
                        'name' => 'Kabupaten Grobogan',
                        'districts' => $this->generateDistricts('3309', 'Grobogan', 19),
                    ],
                    [
                        'code' => '3310',
                        'name' => 'Kabupaten Jepara',
                        'districts' => $this->generateDistricts('3310', 'Jepara', 14),
                    ],
                    [
                        'code' => '3311',
                        'name' => 'Kabupaten Karanganyar',
                        'districts' => $this->generateDistricts('3311', 'Karanganyar', 17),
                    ],
                    [
                        'code' => '3312',
                        'name' => 'Kabupaten Kebumen',
                        'districts' => $this->generateDistricts('3312', 'Kebumen', 26),
                    ],
                    [
                        'code' => '3313',
                        'name' => 'Kabupaten Kendal',
                        'districts' => $this->generateDistricts('3313', 'Kendal', 20),
                    ],
                    [
                        'code' => '3314',
                        'name' => 'Kabupaten Klaten',
                        'districts' => $this->generateDistricts('3314', 'Klaten', 26),
                    ],
                    [
                        'code' => '3315',
                        'name' => 'Kabupaten Kudus',
                        'districts' => $this->generateDistricts('3315', 'Kudus', 9),
                    ],
                    [
                        'code' => '3316',
                        'name' => 'Kabupaten Magelang',
                        'districts' => $this->generateDistricts('3316', 'Magelang', 22),
                    ],
                    [
                        'code' => '3317',
                        'name' => 'Kabupaten Pati',
                        'districts' => $this->generateDistricts('3317', 'Pati', 21),
                    ],
                    [
                        'code' => '3318',
                        'name' => 'Kabupaten Pekalongan',
                        'districts' => $this->generateDistricts('3318', 'Pekalongan', 19),
                    ],
                    [
                        'code' => '3319',
                        'name' => 'Kabupaten Pemalang',
                        'districts' => $this->generateDistricts('3319', 'Pemalang', 14),
                    ],
                    [
                        'code' => '3320',
                        'name' => 'Kabupaten Purbalingga',
                        'districts' => $this->generateDistricts('3320', 'Purbalingga', 18),
                    ],
                    [
                        'code' => '3321',
                        'name' => 'Kabupaten Purworejo',
                        'districts' => $this->generateDistricts('3321', 'Purworejo', 16),
                    ],
                    [
                        'code' => '3322',
                        'name' => 'Kabupaten Rembang',
                        'districts' => $this->generateDistricts('3322', 'Rembang', 14),
                    ],
                    [
                        'code' => '3323',
                        'name' => 'Kabupaten Semarang',
                        'districts' => $this->generateDistricts('3323', 'Semarang', 19),
                    ],
                    [
                        'code' => '3324',
                        'name' => 'Kabupaten Sragen',
                        'districts' => $this->generateDistricts('3324', 'Sragen', 20),
                    ],
                    [
                        'code' => '3325',
                        'name' => 'Kabupaten Sukoharjo',
                        'districts' => $this->generateDistricts('3325', 'Sukoharjo', 12),
                    ],
                    [
                        'code' => '3326',
                        'name' => 'Kabupaten Temanggung',
                        'districts' => $this->generateDistricts('3326', 'Temanggung', 13),
                    ],
                    [
                        'code' => '3327',
                        'name' => 'Kabupaten Wonogiri',
                        'districts' => $this->generateDistricts('3327', 'Wonogiri', 25),
                    ],
                    [
                        'code' => '3328',
                        'name' => 'Kabupaten Wonosobo',
                        'districts' => $this->generateDistricts('3328', 'Wonosobo', 15),
                    ],
                    [
                        'code' => '3371',
                        'name' => 'Kota Magelang',
                        'districts' => $this->generateDistricts('3371', 'Magelang', 3),
                    ],
                    [
                        'code' => '3372',
                        'name' => 'Kota Pekalongan',
                        'districts' => $this->generateDistricts('3372', 'Pekalongan', 4),
                    ],
                    [
                        'code' => '3373',
                        'name' => 'Kota Salatiga',
                        'districts' => $this->generateDistricts('3373', 'Salatiga', 4),
                    ],
                    [
                        'code' => '3374',
                        'name' => 'Kota Semarang',
                        'districts' => $this->generateDistricts('3374', 'Semarang', 16),
                    ],
                    [
                        'code' => '3375',
                        'name' => 'Kota Surakarta',
                        'districts' => $this->generateDistricts('3375', 'Surakarta', 5),
                    ],
                    [
                        'code' => '3376',
                        'name' => 'Kota Tegal',
                        'districts' => $this->generateDistricts('3376', 'Tegal', 4),
                    ],
                ],
            ],
            [
                'code' => '34',
                'name' => 'DI Yogyakarta',
                'cities' => [
                    [
                        'code' => '3401',
                        'name' => 'Kabupaten Bantul',
                        'districts' => $this->generateDistricts('3401', 'Bantul', 17),
                    ],
                    [
                        'code' => '3402',
                        'name' => 'Kabupaten Gunungkidul',
                        'districts' => $this->generateDistricts('3402', 'Gunungkidul', 13),
                    ],
                    [
                        'code' => '3403',
                        'name' => 'Kabupaten Kulon Progo',
                        'districts' => $this->generateDistricts('3403', 'Kulon Progo', 12),
                    ],
                    [
                        'code' => '3404',
                        'name' => 'Kabupaten Sleman',
                        'districts' => $this->generateDistricts('3404', 'Sleman', 17),
                    ],
                    [
                        'code' => '3471',
                        'name' => 'Kota Yogyakarta',
                        'districts' => $this->generateDistricts('3471', 'Yogyakarta', 14),
                    ],
                ],
            ],
            [
                'code' => '35',
                'name' => 'Jawa Timur',
                'cities' => [
                    [
                        'code' => '3501',
                        'name' => 'Kabupaten Bangkalan',
                        'districts' => $this->generateDistricts('3501', 'Bangkalan', 18),
                    ],
                    [
                        'code' => '3502',
                        'name' => 'Kabupaten Banyuwangi',
                        'districts' => $this->generateDistricts('3502', 'Banyuwangi', 25),
                    ],
                    [
                        'code' => '3503',
                        'name' => 'Kabupaten Blitar',
                        'districts' => $this->generateDistricts('3503', 'Blitar', 22),
                    ],
                    [
                        'code' => '3504',
                        'name' => 'Kabupaten Bojonegoro',
                        'districts' => $this->generateDistricts('3504', 'Bojonegoro', 28),
                    ],
                    [
                        'code' => '3505',
                        'name' => 'Kabupaten Bondowoso',
                        'districts' => $this->generateDistricts('3505', 'Bondowoso', 23),
                    ],
                    [
                        'code' => '3506',
                        'name' => 'Kabupaten Gresik',
                        'districts' => $this->generateDistricts('3506', 'Gresik', 18),
                    ],
                    [
                        'code' => '3507',
                        'name' => 'Kabupaten Jember',
                        'districts' => $this->generateDistricts('3507', 'Jember', 31),
                    ],
                    [
                        'code' => '3508',
                        'name' => 'Kabupaten Jombang',
                        'districts' => $this->generateDistricts('3508', 'Jombang', 21),
                    ],
                    [
                        'code' => '3509',
                        'name' => 'Kabupaten Kediri',
                        'districts' => $this->generateDistricts('3509', 'Kediri', 26),
                    ],
                    [
                        'code' => '3510',
                        'name' => 'Kabupaten Lamongan',
                        'districts' => $this->generateDistricts('3510', 'Lamongan', 27),
                    ],
                    [
                        'code' => '3511',
                        'name' => 'Kabupaten Lumajang',
                        'districts' => $this->generateDistricts('3511', 'Lumajang', 21),
                    ],
                    [
                        'code' => '3512',
                        'name' => 'Kabupaten Madiun',
                        'districts' => $this->generateDistricts('3512', 'Madiun', 15),
                    ],
                    [
                        'code' => '3513',
                        'name' => 'Kabupaten Magetan',
                        'districts' => $this->generateDistricts('3513', 'Magetan', 18),
                    ],
                    [
                        'code' => '3514',
                        'name' => 'Kabupaten Malang',
                        'districts' => $this->generateDistricts('3514', 'Malang', 33),
                    ],
                    [
                        'code' => '3515',
                        'name' => 'Kabupaten Mojokerto',
                        'districts' => $this->generateDistricts('3515', 'Mojokerto', 18),
                    ],
                    [
                        'code' => '3516',
                        'name' => 'Kabupaten Nganjuk',
                        'districts' => $this->generateDistricts('3516', 'Nganjuk', 20),
                    ],
                    [
                        'code' => '3517',
                        'name' => 'Kabupaten Ngawi',
                        'districts' => $this->generateDistricts('3517', 'Ngawi', 19),
                    ],
                    [
                        'code' => '3518',
                        'name' => 'Kabupaten Pacitan',
                        'districts' => $this->generateDistricts('3518', 'Pacitan', 13),
                    ],
                    [
                        'code' => '3519',
                        'name' => 'Kabupaten Pamekasan',
                        'districts' => $this->generateDistricts('3519', 'Pamekasan', 13),
                    ],
                    [
                        'code' => '3520',
                        'name' => 'Kabupaten Pasuruan',
                        'districts' => $this->generateDistricts('3520', 'Pasuruan', 24),
                    ],
                    [
                        'code' => '3521',
                        'name' => 'Kabupaten Ponorogo',
                        'districts' => $this->generateDistricts('3521', 'Ponorogo', 21),
                    ],
                    [
                        'code' => '3522',
                        'name' => 'Kabupaten Probolinggo',
                        'districts' => $this->generateDistricts('3522', 'Probolinggo', 24),
                    ],
                    [
                        'code' => '3523',
                        'name' => 'Kabupaten Sampang',
                        'districts' => $this->generateDistricts('3523', 'Sampang', 14),
                    ],
                    [
                        'code' => '3524',
                        'name' => 'Kabupaten Sidoarjo',
                        'districts' => $this->generateDistricts('3524', 'Sidoarjo', 18),
                    ],
                    [
                        'code' => '3525',
                        'name' => 'Kabupaten Situbondo',
                        'districts' => $this->generateDistricts('3525', 'Situbondo', 17),
                    ],
                    [
                        'code' => '3526',
                        'name' => 'Kabupaten Sumenep',
                        'districts' => $this->generateDistricts('3526', 'Sumenep', 27),
                    ],
                    [
                        'code' => '3527',
                        'name' => 'Kabupaten Trenggalek',
                        'districts' => $this->generateDistricts('3527', 'Trenggalek', 14),
                    ],
                    [
                        'code' => '3528',
                        'name' => 'Kabupaten Tuban',
                        'districts' => $this->generateDistricts('3528', 'Tuban', 20),
                    ],
                    [
                        'code' => '3529',
                        'name' => 'Kabupaten Tulungagung',
                        'districts' => $this->generateDistricts('3529', 'Tulungagung', 19),
                    ],
                    [
                        'code' => '3571',
                        'name' => 'Kota Batu',
                        'districts' => $this->generateDistricts('3571', 'Batu', 3),
                    ],
                    [
                        'code' => '3572',
                        'name' => 'Kota Blitar',
                        'districts' => $this->generateDistricts('3572', 'Blitar', 3),
                    ],
                    [
                        'code' => '3573',
                        'name' => 'Kota Kediri',
                        'districts' => $this->generateDistricts('3573', 'Kediri', 3),
                    ],
                    [
                        'code' => '3574',
                        'name' => 'Kota Madiun',
                        'districts' => $this->generateDistricts('3574', 'Madiun', 3),
                    ],
                    [
                        'code' => '3575',
                        'name' => 'Kota Malang',
                        'districts' => $this->generateDistricts('3575', 'Malang', 5),
                    ],
                    [
                        'code' => '3576',
                        'name' => 'Kota Mojokerto',
                        'districts' => $this->generateDistricts('3576', 'Mojokerto', 3),
                    ],
                    [
                        'code' => '3577',
                        'name' => 'Kota Pasuruan',
                        'districts' => $this->generateDistricts('3577', 'Pasuruan', 3),
                    ],
                    [
                        'code' => '3578',
                        'name' => 'Kota Probolinggo',
                        'districts' => $this->generateDistricts('3578', 'Probolinggo', 3),
                    ],
                    [
                        'code' => '3579',
                        'name' => 'Kota Surabaya',
                        'districts' => $this->generateDistricts('3579', 'Surabaya', 31),
                    ],
                ],
            ],
            [
                'code' => '36',
                'name' => 'Banten',
                'cities' => [
                    [
                        'code' => '3601',
                        'name' => 'Kabupaten Lebak',
                        'districts' => $this->generateDistricts('3601', 'Lebak', 28),
                    ],
                    [
                        'code' => '3602',
                        'name' => 'Kabupaten Pandeglang',
                        'districts' => $this->generateDistricts('3602', 'Pandeglang', 35),
                    ],
                    [
                        'code' => '3603',
                        'name' => 'Kabupaten Serang',
                        'districts' => $this->generateDistricts('3603', 'Serang', 28),
                    ],
                    [
                        'code' => '3604',
                        'name' => 'Kabupaten Tangerang',
                        'districts' => $this->generateDistricts('3604', 'Tangerang', 29),
                    ],
                    [
                        'code' => '3671',
                        'name' => 'Kota Cilegon',
                        'districts' => $this->generateDistricts('3671', 'Cilegon', 8),
                    ],
                    [
                        'code' => '3672',
                        'name' => 'Kota Serang',
                        'districts' => $this->generateDistricts('3672', 'Serang', 6),
                    ],
                    [
                        'code' => '3673',
                        'name' => 'Kota Tangerang',
                        'districts' => $this->generateDistricts('3673', 'Tangerang', 13),
                    ],
                    [
                        'code' => '3674',
                        'name' => 'Kota Tangerang Selatan',
                        'districts' => $this->generateDistricts('3674', 'Tangerang Selatan', 7),
                    ],
                ],
            ],
            [
                'code' => '51',
                'name' => 'Bali',
                'cities' => [
                    [
                        'code' => '5101',
                        'name' => 'Kabupaten Badung',
                        'districts' => $this->generateDistricts('5101', 'Badung', 6),
                    ],
                    [
                        'code' => '5102',
                        'name' => 'Kabupaten Bangli',
                        'districts' => $this->generateDistricts('5102', 'Bangli', 4),
                    ],
                    [
                        'code' => '5103',
                        'name' => 'Kabupaten Buleleng',
                        'districts' => $this->generateDistricts('5103', 'Buleleng', 9),
                    ],
                    [
                        'code' => '5104',
                        'name' => 'Kabupaten Gianyar',
                        'districts' => $this->generateDistricts('5104', 'Gianyar', 7),
                    ],
                    [
                        'code' => '5105',
                        'name' => 'Kabupaten Jembrana',
                        'districts' => $this->generateDistricts('5105', 'Jembrana', 5),
                    ],
                    [
                        'code' => '5106',
                        'name' => 'Kabupaten Karangasem',
                        'districts' => $this->generateDistricts('5106', 'Karangasem', 8),
                    ],
                    [
                        'code' => '5107',
                        'name' => 'Kabupaten Klungkung',
                        'districts' => $this->generateDistricts('5107', 'Klungkung', 4),
                    ],
                    [
                        'code' => '5108',
                        'name' => 'Kabupaten Tabanan',
                        'districts' => $this->generateDistricts('5108', 'Tabanan', 10),
                    ],
                    [
                        'code' => '5171',
                        'name' => 'Kota Denpasar',
                        'districts' => $this->generateDistricts('5171', 'Denpasar', 4),
                    ],
                ],
            ],
        ];
    }

    protected function generateDistricts($cityCode, $cityName, $count)
    {
        $districts = [];
        
        $commonNames = [
            'Utara', 'Selatan', 'Timur', 'Barat', 'Pusat',
            'Indah', 'Makmur', 'Jaya', 'Sejahtera', 'Sentosa',
            'Utama', 'Maju', 'Bahagia', 'Permai', 'Sakti',
            'Bakti', 'Karya', 'Mitra', 'Wibawa', 'Murni',
            'Kasih', 'Suka', 'Mekar', 'Harapan', 'Tama'
        ];
        
        for ($i = 1; $i <= $count; $i++) {
            $districtCode = $cityCode . str_pad($i, 2, '0', STR_PAD_LEFT);
            
            $name = $commonNames[($i - 1) % count($commonNames)];
            if ($i > count($commonNames)) {
                $name = $name . ' ' . chr(64 + floor(($i - 1) / count($commonNames)) + 1);
            }
            
            $districts[] = [
                'code' => $districtCode,
                'name' => 'Kecamatan ' . $name,
                'villages' => $this->generateVillages($districtCode, $name, rand(5, 15)),
            ];
        }
        
        return $districts;
    }

    protected function generateVillages($districtCode, $name, $count)
    {
        $villages = [];
        
        $types = ['Desa', 'Kelurahan'];
        
        for ($i = 1; $i <= $count; $i++) {
            $villageCode = $districtCode . str_pad($i, 2, '0', STR_PAD_LEFT);
            $type = $types[rand(0, 1)];
            
            $villages[] = [
                'code' => $villageCode,
                'name' => $type . ' ' . ucwords(strtolower($name)) . ' ' . $i,
            ];
        }
        
        return $villages;
    }
}
