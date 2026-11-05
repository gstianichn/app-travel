<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Master data for pickup points and intercity routes across 18 cities:
 * Bandung, Bekasi, Boyolali, Brebes, Ciamis, Cianjur, Cibubur, Cimahi,
 * Cirebon, Depok, Garut, Indramayu, Jakarta, Karawang, Kuningan,
 * Magelang, Majalengka, and Purwakarta.
 */
return new class extends Migration
{
    /**
     * @var array<int, array{code: string, name: string, city: string, address: string, type: string}>
     */
    private array $locations = [
        // 1. BANDUNG
        ['code' => 'BDO-BBT', 'name' => 'Buah Batu', 'city' => 'Bandung', 'address' => 'Jl. Terusan Buah Batu No.227, Kujangsari, Kec. Bandung Kidul, Kota Bandung, Jawa Barat 40287', 'type' => 'pool'],
        ['code' => 'BDO-MKW', 'name' => 'Mekarwangi', 'city' => 'Bandung', 'address' => 'Jl. Soekarno Hatta No.385, Karasak, Kec. Astanaanyar, Kota Bandung, 40243', 'type' => 'pool'],
        ['code' => 'BDO-PST', 'name' => 'Pasteur', 'city' => 'Bandung', 'address' => 'Jl. Dr. Djunjunan No.135 A, Pajajaran, Kec. Cicendo, Kota Bandung, Jawa Barat 40173', 'type' => 'pool'],
        ['code' => 'BDO', 'name' => 'Dipati Ukur', 'city' => 'Bandung', 'address' => 'Jl. Dipati Ukur 108, Lebakgede, Kecamatan Coblong, Kota Bandung, Jawa Barat 40132', 'type' => 'pool'],
        ['code' => 'BDO-CLY', 'name' => 'Denhaag Klappertaart Cileunyi (Virtual Point)', 'city' => 'Bandung', 'address' => 'Jl. Raya Bandung - Garut No.472, Cileunyi Wetan, Kec. Cileunyi, Kabupaten Sumedang, Jawa Barat 45363', 'type' => 'shuttle'],
        ['code' => 'BDO-CBR', 'name' => 'Cibiru', 'city' => 'Bandung', 'address' => 'Jl. A.H. Nasution No.228, Raya Ujung Berung, 40614 Kota Bandung', 'type' => 'pool'],
        ['code' => 'BDO-PDL', 'name' => 'Almaz Padalarang (Virtual Point)', 'city' => 'Bandung', 'address' => 'Jl. Raya Padalarang No.465B, Kertajaya, Kec. Padalarang, Kabupaten Bandung Barat, Jawa Barat 40553', 'type' => 'shuttle'],
        ['code' => 'BDO-BBT2', 'name' => 'Buah Batu 2', 'city' => 'Bandung', 'address' => 'Jl. Terusan Buah Batu No.312, Kujangsari, Kec. Bandung Kidul, Kota Bandung, Jawa Barat 40287', 'type' => 'pool'],
        ['code' => 'BDO-GDB', 'name' => 'Gedebage', 'city' => 'Bandung', 'address' => 'Jl. Gedebage Selatan No.121A, Cisaranten Kidul, Kec. Gedebage, Kota Bandung, Jawa Barat 40552', 'type' => 'pool'],
        ['code' => 'BDO-MCDC', 'name' => 'MCD Cibiru', 'city' => 'Bandung', 'address' => 'Bunderan Cibiru No.310, Cipadung Wetan, Kec. Panyileukan, Kota Bandung, Jawa Barat 40614', 'type' => 'shuttle'],

        // 2. BEKASI
        ['code' => 'BKS-CKR', 'name' => 'Cikarang Lippo (Ruko Roxy)', 'city' => 'Bekasi', 'address' => 'Bhinneka Shuttle Cikarang - Ruko Roxy Blok A No. 6 Jl. MH. Thamrin (Samping SPBU)', 'type' => 'pool'],
        ['code' => 'BKS-JTA', 'name' => 'Jatiasih', 'city' => 'Bekasi', 'address' => 'Jl. Wibawa Mukti II No.70, RT.003/RW.009, Jatiasih, Kec. Jatiasih, Kota Bks, Jawa Barat 17423', 'type' => 'pool'],
        ['code' => 'BKS', 'name' => 'Bekasi Barat Kemang View', 'city' => 'Bekasi', 'address' => 'Apartemen Kemang View kios Flamboyan GF.16 Jl. Raya Pekayon No.2A, RT.003/RW.020, Pekayon Jaya, Kec. Bekasi Selatan, Kota Bekasi', 'type' => 'pool'],
        ['code' => 'BKS-BKJ', 'name' => 'Burger King Jatiasih (virtual point)', 'city' => 'Bekasi', 'address' => 'Jl. Raya Jatiasih No.36, RT.005/RW.010, Jatimurni, Kec. Pd. Melati, Kota Bks, Jawa Barat 17423', 'type' => 'shuttle'],
        ['code' => 'BKS-HLP', 'name' => 'Halte Living Plaza', 'city' => 'Bekasi', 'address' => 'Pekayon Jaya, Kec. Bekasi Sel., Kota Bks, Jawa Barat', 'type' => 'shuttle'],
        ['code' => 'BKS-BKGW', 'name' => 'Burger King Grand Wisata', 'city' => 'Bekasi', 'address' => 'Jl. West Gateway Blvd, Lambangsari, Kec. Tambun Sel., Kabupaten Bekasi, Jawa Barat 17510', 'type' => 'shuttle'],

        // 3. BOYOLALI
        ['code' => 'BYL-AMP', 'name' => 'Alfamart Ampel Boyolali (Virtual Point)', 'city' => 'Boyolali', 'address' => 'Jl. Raya Semarang - Solo No.163, Dusun 2, Kaligentong, Kec. Ampel, Kabupaten Boyolali, Jawa Tengah 57352', 'type' => 'shuttle'],
        ['code' => 'BYL', 'name' => 'BVPIP4', 'city' => 'Boyolali', 'address' => 'Jl. Pandanaran No.11, Ngrancah, Siswodipuran, Kec. Boyolali, Kabupaten Boyolali, Jawa Tengah 57311', 'type' => 'pool'],

        // 4. BREBES
        ['code' => 'BRB', 'name' => 'Indomaret Diponogoro Brebes (Virtual Point)', 'city' => 'Brebes', 'address' => '42JJ+CHF, Jl. Pangeran Diponegoro No.31, Pebatan Timur, Pebatan, Kec. Wanasari, Kabupaten Brebes, Jawa Tengah 52222', 'type' => 'shuttle'],
        ['code' => 'BRB-KLG', 'name' => 'Indomaret Kaligangsa Brebes (Virtual Point)', 'city' => 'Brebes', 'address' => '43CF+R6V, AH2, Kaligangsaindah, Kaligangsa Wetan, Kec. Brebes, Kabupaten Brebes, Jawa Tengah 52217', 'type' => 'shuttle'],

        // 5. CIAMIS
        ['code' => 'CMS', 'name' => 'Alun Alun Ciamis', 'city' => 'Ciamis', 'address' => 'Ciamis, Kec. Ciamis, Kabupaten Ciamis, Jawa Barat', 'type' => 'pool'],
        ['code' => 'CMS-KWL', 'name' => 'Alun Alun Kawali', 'city' => 'Ciamis', 'address' => 'R979+WQM, Kawali, Kec. Kawali, Kabupaten Ciamis, Jawa Barat 46253', 'type' => 'shuttle'],
        ['code' => 'CMS-SDK', 'name' => 'Sindangkasih Toko Bungsu', 'city' => 'Ciamis', 'address' => 'Jl. Raya Sindang Kasih No.09, Sukasenang, Sindangkasih, Sukasenang, Kec. Sindangkasih, Kabupaten Ciamis, Jawa Barat 46268', 'type' => 'pool'],

        // 6. CIANJUR
        ['code' => 'CJR', 'name' => 'Cianjur', 'city' => 'Cianjur', 'address' => 'Jln.Dr Muwardi no 78 (bypass) Samping Cianjur Permai /baso gemar deretan Kampus UNPI', 'type' => 'pool'],
        ['code' => 'CJR-SML', 'name' => 'Protanmas Samolo Cianjur (Virtual Point)', 'city' => 'Cianjur', 'address' => 'Ciherang, Kec. Karangtengah, Kabupaten Cianjur, Jawa Barat', 'type' => 'shuttle'],
        ['code' => 'CJR-CPN', 'name' => 'Cipanas (Selamat Toserba)', 'city' => 'Cianjur', 'address' => 'Jl. Gadog No.39, RT.002/RW.010, Gadog, Kec. Pacet, Kabupaten Cianjur, Jawa Barat 43253 (Selamat Toserba)', 'type' => 'shuttle'],
        ['code' => 'CJR-CRJ', 'name' => 'Yogya Ciranjang', 'city' => 'Cianjur', 'address' => 'Jl. Raya Ciranjang No.80-82, Ciranjang, Kec. Ciranjang, Kabupaten Cianjur, Jawa Barat 41373 (Lobby Supermarket)', 'type' => 'shuttle'],

        // 7. CIBUBUR
        ['code' => 'CBB', 'name' => 'Cibubur', 'city' => 'Cibubur', 'address' => 'Cibubur Point, Harjamukti, Kec. Cimanggis, Kota Depok, Jawa Barat', 'type' => 'pool'],

        // 8. CIMAHI
        ['code' => 'CMH', 'name' => 'Baros Subdenzibang 02.1', 'city' => 'Cimahi', 'address' => 'Jl. HMS Mintareja Sarjana Hukum No.1, Baros, Kec. Cimahi Tengah, Kota Cimahi, Jawa Barat 40521', 'type' => 'pool'],
        ['code' => 'CMH-MLL', 'name' => 'Cimahi Mall', 'city' => 'Cimahi', 'address' => 'Cimahi Mall, Jl. Gandawijaya, Setiamanah, Kec. Cimahi Tengah, Kota Cimahi, Jawa Barat 40524', 'type' => 'shuttle'],

        // 9. CIREBON
        ['code' => 'CBN', 'name' => 'Pilang Raya', 'city' => 'Cirebon', 'address' => 'JL. PILANG RAYA NO. 587, Kota Cirebon', 'type' => 'pool'],
        ['code' => 'CBN-PGC', 'name' => 'Alfamart Penggung Ciperna (virtual point)', 'city' => 'Cirebon', 'address' => 'Jl.Jend.Sudirman No.05, Jl. Penggung Selatan No.Kel, RT.03/RW.07, Kalijaga, Kec. Harjamukti, Kota Cirebon, Jawa Barat 45145', 'type' => 'shuttle'],

        // 10. DEPOK
        ['code' => 'DPK', 'name' => 'Depok', 'city' => 'Depok', 'address' => 'Jl. Jelutung 4 No.180 Kelurahan Bakti Jaya, Kecamatan Sukmajaya, Kota Depok Belakang Halte Bus', 'type' => 'pool'],

        // 11. GARUT
        ['code' => 'GRT', 'name' => 'Garut', 'city' => 'Garut', 'address' => 'Anarto Mall, Garut Hypersquare Ruko Blok A No. 3, Jl. Perintis Kemerdekaan, Haurpanggung, Kec. Garut Kota, Kabupaten Garut, Jawa Barat 44151', 'type' => 'pool'],
        ['code' => 'GRT-MLB', 'name' => 'Warung Baso Cepot Malangbong', 'city' => 'Garut', 'address' => 'W3PP+6XR, Jl. Raya Wado - Malangbong, Sukamanah, cidungus, Kabupaten Garut, Jawa Barat 44188', 'type' => 'shuttle'],

        // 12. INDRAMAYU
        ['code' => 'IDM', 'name' => 'Singajaya', 'city' => 'Indramayu', 'address' => 'JL. IR H. JUANDA SINGAJAYA, ( Samping RM. Waroeng WS PECEL PINCUK) INDRAMAYU', 'type' => 'pool'],
        ['code' => 'IDM-TKD', 'name' => 'Pertigaan Tukdana (Virtual Point)', 'city' => 'Indramayu', 'address' => 'F75P+85R Tukdana, Kabupaten Indramayu, Jawa Barat', 'type' => 'shuttle'],
        ['code' => 'IDM-SRJ', 'name' => 'Surya Toserba Jatibarang (Virtual Point)', 'city' => 'Indramayu', 'address' => 'Jl. Mayor Dasuki No.50, Jatibarang, Kec. Jatibarang, Kabupaten Indramayu, Jawa Barat 45273', 'type' => 'shuttle'],
        ['code' => 'IDM-JTB', 'name' => 'Jatibarang', 'city' => 'Indramayu', 'address' => 'Jl.Mayor Dasuki RT.17 RW.02 Kec.Jatibarang Kab.Indramayu', 'type' => 'pool'],

        // 13. JAKARTA
        ['code' => 'JKT-CKN', 'name' => 'Cikini', 'city' => 'Jakarta', 'address' => 'Jl. Cikini 1 No. 6 RT. 13 / RW. 05, RT.13/RW.5, Cikini, Kec. Menteng, Kota Jakarta Pusat, DK Jakarta 10330', 'type' => 'pool'],
        ['code' => 'JKT-CPK', 'name' => 'Central Park (virtual point)', 'city' => 'Jakarta', 'address' => 'Jl. Letjen S. Parman No.Kav. 28, Tj. Duren Sel., Kec. Grogol petamburan, Kota Jakarta Barat, Daerah Khusus Ibukota Jakarta 11470', 'type' => 'shuttle'],
        ['code' => 'JKT-RWM', 'name' => 'SPBU Rawamangun (virtual point)', 'city' => 'Jakarta', 'address' => 'SPBU 34-13208 - Jl. Jenderal Ahmad Yani No.1 11, RT.11/RW.14, Rawamangun, Kec. Pulo Gadung, Kota Jakarta Timur, DK Jakarta 13220', 'type' => 'shuttle'],
        ['code' => 'JKT-TMG', 'name' => 'Indomaret Fresh Tomang', 'city' => 'Jakarta', 'address' => 'Indomaret Fresh Tomang - Jl. Mandala Raya, RT.13/RW.5, Tomang, Kec. Grogol petamburan, Kota Jakarta Barat, DK Jakarta 11440', 'type' => 'shuttle'],
        ['code' => 'JKT-TBT', 'name' => 'Kedai Kopi Bali Cangkir', 'city' => 'Jakarta', 'address' => 'Jl. Tebet Barat Dalam Raya No.126, Tebet Bar., Kec. Tebet, Kota Jakarta Selatan, Daerah Khusus Jakarta 12810', 'type' => 'shuttle'],
        ['code' => 'JKT-UKR', 'name' => 'Halte Ukrida Tanjung Duren (drop only)', 'city' => 'Jakarta', 'address' => 'Tj. Duren Utara, Kec. Grogol petamburan, Kota Jakarta Barat, Daerah Khusus Ibukota Jakarta', 'type' => 'drop_point'],
        ['code' => 'JKT-ACP', 'name' => 'Alfamart Central Park', 'city' => 'Jakarta', 'address' => '12, RT.6/RW.6, Tj. Duren Sel., Kec. Grogol petamburan, Kota Jakarta Barat, Daerah Khusus Ibukota Jakarta 11470', 'type' => 'shuttle'],
        ['code' => 'JKT-PRB', 'name' => 'Alfamart Pasar Rebo', 'city' => 'Jakarta', 'address' => 'Jl. TB Simatupang No.22 9, RT.10/RW.oo3, Rambutan, Kec. Ciracas, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta 13830', 'type' => 'shuttle'],
        ['code' => 'JKT-GRG', 'name' => 'Grogol', 'city' => 'Jakarta', 'address' => 'Bhinneka Shuttle Grogol - Daan mogot. 1 (seberang RS Royal Taruma) - Indomaret, Jakarta, DK Jakarta 11460', 'type' => 'pool'],
        ['code' => 'JKT-CWG', 'name' => 'Cawang', 'city' => 'Jakarta', 'address' => 'Jl Raya Dewi Sartika No. 75, Jakarta Timur', 'type' => 'pool'],
        ['code' => 'JKT-MKG', 'name' => 'Mall Kelapa Gading', 'city' => 'Jakarta', 'address' => 'Jl. Boulevard Raya, RT.13/RW.18, Klp. Gading Tim., Kec. Klp. Gading, Jkt Utara, Daerah Khusus Jakarta 14240', 'type' => 'pool'],
        ['code' => 'JKT', 'name' => 'Jakarta Semanggi', 'city' => 'Jakarta', 'address' => 'Bhinneka Shuttle Semanggi ( Hotel Kartika Chandara ) - Jl. Gatot Subroto No.18-20, RT.8/RW.2, Karet Semanggi, Kecamatan Setiabudi, Kota Jakarta Selatan, DK Jakarta 12930', 'type' => 'pool'],
        ['code' => 'JKT-FTM', 'name' => 'Fatmawati', 'city' => 'Jakarta', 'address' => 'Blk. Alea Jl. RS. Fatmawati Raya No.160, RT.4/RW.8, Cilandak Bar., Kec. Cilandak, Kota Jakarta Selatan, Daerah Khusus Ibukota Jakarta 12430', 'type' => 'pool'],
        ['code' => 'JKT-JTW', 'name' => 'Jatiwaringin', 'city' => 'Jakarta', 'address' => 'Jl. Raya Jatiwaringin No.7, RT.4/RW.5, Cipinang Melayu, Kec. Makasar, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta 13620', 'type' => 'pool'],
        ['code' => 'JKT-PLM', 'name' => 'Virtual Point Pulo Mas Kelapa Gading', 'city' => 'Jakarta', 'address' => 'Indomaret Perintis Kemerdekaan 49 Jl. Perintis Kemerdekaan Kampung Pulo Nangka, RT.4/RW.11, Kayu Putih, Pulo Gadung, East Jakarta City, Jakarta 13210', 'type' => 'shuttle'],

        // 14. KARAWANG
        ['code' => 'KRW', 'name' => 'Karawang Aneka Baru', 'city' => 'Karawang', 'address' => 'Jl. Tuparev No.375A, Karawang Wetan, Kec. Karawang Tim., Karawang, Jawa Barat 41314', 'type' => 'pool'],
        ['code' => 'KRW-TGM', 'name' => 'Technomart Galuh Mas', 'city' => 'Karawang', 'address' => 'Lobi Utama Galuh Mas Karawang Barat - Jl. Arteri Galuh Mas, Sukaharja, Kec. Telukjambe Tim., Kabupaten Karawang, Jawa Barat 41361', 'type' => 'pool'],
        ['code' => 'KRW-VLG', 'name' => 'Villaggio Outlets', 'city' => 'Karawang', 'address' => 'Summarecon Emerald, Jl. Bulevar Blk. A No.Kav.1, Kondangjaya, Kec. Karawang Tim., Karawang, Jawa Barat 41371', 'type' => 'shuttle'],

        // 15. KUNINGAN
        ['code' => 'KNG-CGD', 'name' => 'Alfamart Cigandamekar', 'city' => 'Kuningan', 'address' => '4GC4+G86, Panawuan, Kec. Cigandamekar, Kabupaten Kuningan, Jawa Barat 45556', 'type' => 'shuttle'],
        ['code' => 'KNG-TIK', 'name' => 'Tugu Ikan Kuningan', 'city' => 'Kuningan', 'address' => '5G25+56R Tugu Ikan Kuningan, Sampora, Kec. Cilimus, Kabupaten Kuningan, Jawa Barat 45556', 'type' => 'shuttle'],
        ['code' => 'KNG', 'name' => 'Ciporang', 'city' => 'Kuningan', 'address' => 'Jl RE Martadinata No 151 (sebrang RS Wijaya), Ciporang Kuningan', 'type' => 'pool'],

        // 16. MAGELANG
        ['code' => 'MGL', 'name' => 'Virtual Point Hotel Trio Magelang', 'city' => 'Magelang', 'address' => 'Jl. Jend. Sudirman No.72, Magersari, Kec. Magelang Sel., Kota Magelang, Jawa Tengah 56126', 'type' => 'shuttle'],
        ['code' => 'MGL-BLB', 'name' => 'Indomaret Blabak Magelang (Virtual Point)', 'city' => 'Magelang', 'address' => 'Mungkid, Kec. Mungkid, Kabupaten Magelang, Jawa Tengah', 'type' => 'shuttle'],
        ['code' => 'MGL-SCG', 'name' => 'Maza Coffee Secang (virtual point)', 'city' => 'Magelang', 'address' => 'Jl. Raya Semarang - Yogyakarta No.05, RT.12/RW.04, Secang, Kec. Secang, Kabupaten Magelang, Jawa Tengah 56195', 'type' => 'shuttle'],
        ['code' => 'MGL-MTL', 'name' => 'Bhisa Virtual Tape Ketan Muntilan', 'city' => 'Magelang', 'address' => 'Jl. Pemuda No.187, Sabrangs, Gunungpring, Kec. Muntilan, Kabupaten Magelang, Jawa Tengah 56415', 'type' => 'shuttle'],

        // 17. MAJALENGKA
        ['code' => 'MJL-KTJ', 'name' => 'Kertajati (Tidak ke BIJB)', 'city' => 'Majalengka', 'address' => 'Blok jagreg desa Babakan. Kec kertajati. jln.Raya kadipaten jatitujuh (tidak ke BIJB)', 'type' => 'pool'],
        ['code' => 'MJL-BIJB', 'name' => 'Bandara Kertajati (BIJB)', 'city' => 'Majalengka', 'address' => 'Bandar Udara Internasional Jawa Barat Kertajati', 'type' => 'pool'],
        ['code' => 'MJL', 'name' => 'Alun-alun Majalengka (virtual point)', 'city' => 'Majalengka', 'address' => '567H+W32, Majalengka Kulon, Kec. Majalengka, Kabupaten Majalengka, Jawa Barat 45418', 'type' => 'shuttle'],
        ['code' => 'MJL-MNC', 'name' => 'Bunderan Muncul (virtual point)', 'city' => 'Majalengka', 'address' => 'Jl. K.H.Abdul Halim No.69, Munjul, Kec. Majalengka, Kabupaten Majalengka, Jawa Barat 45418', 'type' => 'shuttle'],
        ['code' => 'MJL-GGM', 'name' => 'Gelanggang Guru Muda (virtual point)', 'city' => 'Majalengka', 'address' => 'Jl. Pemuda,Majalengka Kulon, Kec. Majalengka, Kabupaten Majalengka, Jawa Barat 45418', 'type' => 'shuttle'],
        ['code' => 'MJL-CKJ', 'name' => 'Polsek Cikijing', 'city' => 'Majalengka', 'address' => 'Jl. Raya Cikijing - Talaga, Cikijing, Majalengka, Kabupaten Majalengka, Jawa Barat 45466', 'type' => 'shuttle'],
        ['code' => 'MJL-KDP', 'name' => 'Indomaret Kadipaten (virtual point)', 'city' => 'Majalengka', 'address' => 'Jl. Raya Timur No.Rt 03, Rw 08, Kec. Kadipaten, Kabupaten Majalengka, Jawa Barat 45452', 'type' => 'shuttle'],
        ['code' => 'MJL-CGS', 'name' => 'Cigasong', 'city' => 'Majalengka', 'address' => 'Cigasong, Kec. Cigasong, Kabupaten Majalengka, Jawa Barat 45476', 'type' => 'pool'],

        // 18. PURWAKARTA
        ['code' => 'PWK-CGN', 'name' => 'Ezra Cell Ciganea (Virtual Point)', 'city' => 'Purwakarta', 'address' => 'Jl. Pemuda no 5 Mekargalih Jatiluhur Purwakarta', 'type' => 'shuttle'],
        ['code' => 'PWK-SDG', 'name' => 'Purwakarta Sadang', 'city' => 'Purwakarta', 'address' => 'Jl. Sadang Raya No.242 B Kel. Ciwangi Kec. Bungursari', 'type' => 'pool'],
        ['code' => 'PWK', 'name' => 'Purwakarta Kota', 'city' => 'Purwakarta', 'address' => 'Jl. Ibrahim Singadilaga No. 55 Kel. Nagri Kaler - Purwakarta (Seberang Indomaret)', 'type' => 'pool'],
        ['code' => 'PWK-BKP', 'name' => 'Burger King Purwakarta', 'city' => 'Purwakarta', 'address' => 'Jl. Veteran No.84, Nagri Kaler, Kec. Purwakarta, Kabupaten Purwakarta, Jawa Barat 41115', 'type' => 'shuttle'],
    ];

    /**
     * Each entry is served in both directions.
     *
     * @var array<int, array{0: string, 1: string, 2: int, 3: string, 4: int}>
     */
    private array $routes = [
        // Koridor Bandung <-> Jakarta
        ['BDO', 'JKT', 125000, '2 Jam 45 Menit', 150],
        ['BDO-PST', 'JKT-TBT', 125000, '2 Jam 30 Menit', 145],
        ['BDO', 'JKT-CKN', 130000, '2 Jam 45 Menit', 155],
        ['BDO-BBT', 'JKT-CWG', 120000, '2 Jam 30 Menit', 145],
        ['BDO-PST', 'JKT-GRG', 130000, '2 Jam 45 Menit', 155],
        ['BDO-MKW', 'JKT-FTM', 130000, '2 Jam 45 Menit', 155],
        ['BDO-CBR', 'JKT-JTW', 125000, '2 Jam 40 Menit', 150],
        ['BDO-PST', 'JKT-MKG', 135000, '3 Jam 00 Menit', 160],

        // Koridor Bandung <-> Bekasi, Cibubur, Depok, Karawang, Purwakarta
        ['BDO', 'BKS', 115000, '2 Jam 15 Menit', 130],
        ['BDO-BBT', 'BKS-JTA', 115000, '2 Jam 15 Menit', 130],
        ['BDO-PST', 'BKS-CKR', 105000, '2 Jam 00 Menit', 115],
        ['BDO', 'CBB', 120000, '2 Jam 30 Menit', 140],
        ['BDO', 'DPK', 125000, '2 Jam 45 Menit', 155],
        ['BDO', 'KRW', 95000, '1 Jam 45 Menit', 95],
        ['BDO', 'PWK', 75000, '1 Jam 15 Menit', 65],

        // Koridor Bandung <-> Priangan & Ciayumajakuning
        ['BDO', 'CJR', 65000, '1 Jam 45 Menit', 65],
        ['BDO', 'CMH', 35000, '35 Menit', 18],
        ['BDO', 'GRT', 75000, '2 Jam 00 Menit', 65],
        ['BDO', 'CMS', 95000, '3 Jam 00 Menit', 120],
        ['BDO', 'CBN', 110000, '2 Jam 15 Menit', 130],
        ['BDO', 'MJL', 90000, '1 Jam 45 Menit', 95],
        ['BDO-PST', 'MJL-BIJB', 100000, '1 Jam 30 Menit', 90],
        ['BDO', 'IDM', 115000, '2 Jam 30 Menit', 140],
        ['BDO', 'KNG', 120000, '2 Jam 45 Menit', 155],

        // Koridor Bandung <-> Jawa Tengah (Brebes, Boyolali, Magelang)
        ['BDO', 'BRB', 150000, '3 Jam 15 Menit', 195],
        ['BDO', 'MGL', 240000, '6 Jam 30 Menit', 380],
        ['BDO', 'BYL', 250000, '6 Jam 45 Menit', 410],

        // Koridor Jakarta & Bodebek <-> Kota Lain
        ['JKT', 'CBN', 140000, '3 Jam 00 Menit', 215],
        ['JKT', 'KNG', 155000, '3 Jam 30 Menit', 245],
        ['JKT', 'IDM', 135000, '3 Jam 00 Menit', 200],
        ['JKT', 'MJL', 135000, '2 Jam 45 Menit', 185],
        ['JKT', 'KRW', 75000, '1 Jam 15 Menit', 65],
        ['JKT', 'PWK', 85000, '1 Jam 30 Menit', 95],
        ['JKT', 'CJR', 110000, '2 Jam 30 Menit', 115],
        ['JKT', 'CMH', 125000, '2 Jam 30 Menit', 140],
        ['JKT', 'GRT', 145000, '3 Jam 30 Menit', 205],
        ['JKT', 'CMS', 165000, '4 Jam 30 Menit', 260],
        ['JKT', 'BRB', 175000, '3 Jam 45 Menit', 275],
        ['JKT', 'MGL', 275000, '7 Jam 00 Menit', 460],
        ['JKT', 'BYL', 285000, '7 Jam 15 Menit', 485],

        // Koridor Antar Kota Regional
        ['BKS', 'CBN', 130000, '2 Jam 45 Menit', 195],
        ['BKS', 'MJL', 125000, '2 Jam 30 Menit', 165],
        ['BKS', 'KNG', 145000, '3 Jam 15 Menit', 225],
        ['BKS', 'CMH', 115000, '2 Jam 15 Menit', 125],
        ['CBN', 'KNG', 45000, '1 Jam 00 Menit', 35],
        ['CBN', 'IDM', 55000, '1 Jam 15 Menit', 55],
        ['CBN', 'MJL', 50000, '1 Jam 00 Menit', 45],
        ['CBN', 'BRB', 60000, '1 Jam 00 Menit', 65],
        ['BRB', 'MGL', 140000, '3 Jam 45 Menit', 210],
        ['BRB', 'BYL', 150000, '4 Jam 00 Menit', 235],
        ['MGL', 'BYL', 65000, '1 Jam 30 Menit', 60],
    ];

    public function up(): void
    {
        if (app()->environment('testing')) {
            return;
        }

        $this->insertMasterData();
    }

    /**
     * Insert or refresh locations and routes. Safe to run more than once.
     */
    public function insertMasterData(): void
    {
        $now = now();
        $activeCodes = array_column($this->locations, 'code');

        // Nonaktifkan lokasi lama yang tidak termasuk dalam 18 kota aktif
        if (! app()->environment('testing')) {
            DB::table('locations')
                ->whereNotIn('code', $activeCodes)
                ->update(['is_active' => false, 'updated_at' => $now]);

            $inactiveLocationIds = DB::table('locations')
                ->where('is_active', false)
                ->pluck('id');

            if ($inactiveLocationIds->isNotEmpty()) {
                DB::table('routes')
                    ->whereIn('origin_id', $inactiveLocationIds)
                    ->orWhereIn('destination_id', $inactiveLocationIds)
                    ->update(['is_active' => false, 'updated_at' => $now]);

                $inactiveRouteIds = DB::table('routes')
                    ->where('is_active', false)
                    ->pluck('id');

                if ($inactiveRouteIds->isNotEmpty()) {
                    DB::table('schedules')
                        ->whereIn('route_id', $inactiveRouteIds)
                        ->whereNotExists(function ($query) {
                            $query->select(DB::raw(1))
                                ->from('bookings')
                                ->whereColumn('bookings.schedule_id', 'schedules.id');
                        })
                        ->delete();
                }
            }
        }

        foreach ($this->locations as $location) {
            DB::table('locations')->updateOrInsert(
                ['code' => $location['code']],
                [
                    'name' => $location['name'],
                    'city' => $location['city'],
                    'address' => $location['address'],
                    'type' => $location['type'] ?? 'pool',
                    'is_active' => true,
                    'updated_at' => $now,
                    'created_at' => $now,
                ],
            );
        }

        $locationIds = DB::table('locations')->pluck('id', 'code');

        foreach ($this->routes as [$from, $to, $price, $duration, $distance]) {
            if (! isset($locationIds[$from], $locationIds[$to])) {
                continue;
            }

            foreach ([[$from, $to], [$to, $from]] as [$origin, $destination]) {
                DB::table('routes')->updateOrInsert(
                    ['origin_id' => $locationIds[$origin], 'destination_id' => $locationIds[$destination]],
                    [
                        'price' => $price,
                        'duration' => $duration,
                        'distance_km' => $distance,
                        'is_active' => true,
                        'updated_at' => $now,
                        'created_at' => $now,
                    ],
                );
            }
        }
    }

    public function down(): void
    {
        //
    }
};
