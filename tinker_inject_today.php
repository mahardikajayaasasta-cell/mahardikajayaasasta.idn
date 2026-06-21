<?php

$locations = \App\Models\Location::all();
$users = \App\Models\User::where('role', 'karyawan')->get();
$today = \Illuminate\Support\Carbon::today();

if ($locations->isNotEmpty() && $users->isNotEmpty()) {
    foreach ($users as $u) {
        $exists = \App\Models\Attendance::where('user_id', $u->id)->whereDate('date', $today)->exists();
        if ($exists) continue;

        $loc = $locations->random();
        $isLate = rand(0, 100) > 80;
        $isMangkir = rand(0, 100) > 95;

        // Skip mangkir mostly so dashboard looks green
        if ($isMangkir) {
             \App\Models\Attendance::create([
                'user_id' => $u->id,
                'date' => $today,
                'status' => 'Mangkir',
            ]);
        } else {
            $clockIn = $isLate
                ? $today->copy()->setTime(rand(8, 9), rand(31, 59), 0)
                : $today->copy()->setTime(rand(7, 8), rand(0, 29), 0);
            
            // Randomly some people haven't clocked out yet (clock_out = null)
            $hasClockedOut = rand(0, 1) > 0;
            $clockOut = $hasClockedOut ? $clockIn->copy()->addHours(rand(8, 10))->addMinutes(rand(0, 59)) : null;

            \App\Models\Attendance::create([
                'user_id' => $u->id,
                'location_id' => $loc->id,
                'date' => $today,
                'clock_in' => $clockIn,
                'clock_in_latitude' => $loc->latitude + (rand(-100, 100) / 1000000),
                'clock_in_longitude' => $loc->longitude + (rand(-100, 100) / 1000000),
                'clock_in_distance' => rand(5, max(6, $loc->radius - 5)),
                'clock_in_photo' => 'https://ui-avatars.com/api/?name=' . urlencode($u->name) . '&background=random&color=fff&size=600',
                'clock_out' => $clockOut,
                'clock_out_latitude' => $hasClockedOut ? $loc->latitude + (rand(-100, 100) / 1000000) : null,
                'clock_out_longitude' => $hasClockedOut ? $loc->longitude + (rand(-100, 100) / 1000000) : null,
                'clock_out_distance' => $hasClockedOut ? rand(5, max(6, $loc->radius - 5)) : null,
                'clock_out_photo' => $hasClockedOut ? 'https://ui-avatars.com/api/?name=' . urlencode($u->name) . '&background=random&color=fff&size=600' : null,
                'status' => $isLate ? 'Telat' : 'Hadir',
            ]);
        }
    }
}
echo "Done injecting today's data!";
