<?php

if (!function_exists('haversine')) {
    /**
     * Menghitung jarak antara dua titik koordinat menggunakan Haversine Formula.
     *
     * @param  float  $lat1  Latitude titik 1
     * @param  float  $lon1  Longitude titik 1
     * @param  float  $lat2  Latitude titik 2
     * @param  float  $lon2  Longitude titik 2
     * @return float         Jarak dalam meter
     */
    function haversine(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371000; // jari-jari bumi dalam meter

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2)
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
            * sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c; // hasil dalam meter
    }
}

if (!function_exists('isWithinRadius')) {
    /**
     * Cek apakah posisi karyawan berada dalam radius lokasi kerja.
     *
     * @param  float  $userLat    Latitude karyawan
     * @param  float  $userLon    Longitude karyawan
     * @param  float  $officeLat  Latitude kantor
     * @param  float  $officeLon  Longitude kantor
     * @param  int    $radius     Radius toleransi dalam meter
     * @return array              ['within' => bool, 'distance' => float]
     */
    function isWithinRadius(float $userLat, float $userLon, float $officeLat, float $officeLon, int $radius): array
    {
        $distance = haversine($userLat, $userLon, $officeLat, $officeLon);

        return [
            'within'   => $distance <= $radius,
            'distance' => round($distance, 2),
        ];
    }
}
