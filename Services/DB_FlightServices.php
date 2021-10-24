<?php

namespace Modules\DisposableBasic\Services;

use App\Models\Enums\PirepState;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\DisposableBasic\Models\DB_RandomFlight;

class DB_FlightServices
{
  // Random Flight Picker
  public function PickRandomFlights($user, $orig, $count, $whereRF = null, $eager_load = null)
  {
    $allowed_flights = null;
    $today = Carbon::today();

    // Prepare flights array according to phpVMS settings
    $where = [];
    $where['active'] = 1;
    $where['visible'] = 1;
    $where['dpt_airport_id'] = $orig;

    if (setting('pilots.restrict_to_company', false)) {
      $where['airline_id'] = $user->airline_id;
    }

    if (setting('pireps.restrict_aircraft_to_rank', false)) {
      $allowed_subfleets = DB::table('subfleet_rank')->where('rank_id', $user->rank_id)->pluck('subfleet_id')->toArray();
      $allowed_flights = DB::table('flight_subfleet')->whereIn('subfleet_id', $allowed_subfleets)->pluck('flight_id')->toArray();
    }

    $flights = DB::table('flights')->select('id')
      ->where($where)
      ->when(is_array($allowed_flights), function ($query) use ($allowed_flights) { $query->whereIn('id', $allowed_flights); })
      ->pluck('id')->toArray();

    // Eliminate already flown
    $where_pirep = [];
    $where_pirep['user_id'] = $user->id;
    $where_pirep['dpt_airport_id'] = $orig;
    $where_pirep['state'] = PirepState::ACCEPTED;

    $flown = DB::table('pireps')->where($where_pirep)->groupby('flight_id')->pluck('flight_id')->toArray();

    if (count($flights) > count($flown)) {
      $flights = array_diff($flights, $flown);
    }

    // Select random flights
    $count = (count($flights) < $count) ? count($flights) : $count;
    $flights = collect($flights)->random($count);

    // Save each flight
    foreach ($flights as $flight_id) {
      DB_RandomFlight::create([
        'user_id'     => $user->id,
        'airport_id'  => $orig,
        'flight_id'   => $flight_id,
        'assign_date' => $today,
      ]);
    }

    // Return collection
    return DB_RandomFlight::with($eager_load)->where($whereRF)->get();
  }
}