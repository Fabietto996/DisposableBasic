<?php

namespace Modules\DisposableBasic\Services;

use App\Events\PirepCancelled;
use App\Models\Aircraft;
use App\Models\Pirep;
use App\Models\Enums\AircraftState;
use App\Models\Enums\PirepState;
use App\Models\Enums\PirepStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Laracasts\Flash\Flash;

class DB_FleetServices
{
    // Fix Aircraft State
    public function FixAircraftState($reg)
    {
        $result = 0;
        $aircraft = Aircraft::where('registration', $reg)->where('state', '!=', AircraftState::PARKED)->first();

        if ($aircraft) {
            $pirep = Pirep::where(['aircraft_id' => $aircraft->id, 'state' => PirepState::IN_PROGRESS])->orderby('updated_at', 'desc')->first();

            if ($pirep) {
                $pirep->state = PirepState::CANCELLED;
                $pirep->status = PirepStatus::CANCELLED;
                $pirep->notes = 'Cancelled By Admin';
                $pirep->save();
                $result = 1;
                event(new PirepCancelled($pirep));
                Log::info('Disposable Basic, Pirep ID:' . $pirep->id . ' CANCELLED to fix aircraft state');
            }
            $aircraft->state = AircraftState::PARKED;
            $aircraft->save();
            $result = $result + 1;
            Log::info('Disposable Basic, Aircraft REG:' . $aircraft->registration . ' PARKED by Admin');
        }

        if ($result === 0) {
            Flash::error('Nothing Done... Aircraft Not Found or was already PARKED');
        } elseif ($result === 1) {
            Flash::success('Aircraft State Changed Back to PARKED');
        } elseif ($result === 2) {
            Flash::success('Aircraft State Changed Back to PARKED and Pirep CANCELLED');
        }
    }

    // Provide avg fuel burn per minute (for fuel calculations etc)
    public function AverageFuelBurn($aircraft_id)
    {
        $results = [];
        $aircraft_icao = DB::table('aircraft')->where('id', $aircraft_id)->value('icao');

        if (Schema::hasTable('disposable_tech') && Schema::hasColumn('disposable_tech', 'avg_fuel')) {
            $result = DB::table('disposable_tech')->where('icao', $aircraft_icao)->value('avg_fuel');
        }

        if ($result > 0) {
            $results['source'] = 'ICAO Type Avg (Manufacturer)';
            $results['avg_pounds'] = round($result / 60, 2);
            $results['avg_metric'] = round(($result / 60) / 2.20462262185, 2);

            return $results;
        }

        $aircraft_array = DB::table('aircraft')->where('icao', $aircraft_icao)->pluck('id')->toArray();

        $where = [];
        $where['state'] = PirepState::ACCEPTED;
        $where['aircraft_id'] = $aircraft_id;

        $results['source'] = 'Aircraft Avg (Pireps)';

        $count_while = 1;
        while ($count_while <= 3) {

            if ($count_while === 2) {
                // Remove the aircraft_id from where array and try getting icao based pirep average
                unset($where['aircraft_id']);
                $results['source'] = 'ICAO Type Avg (Pireps)';
            }

            $result = DB::table('pireps')
                ->selectRaw('sum(fuel_used) as total_fuel, sum(flight_time) as total_time')
                ->where($where)
                ->when($count_while === 2, function ($query) use ($aircraft_array) {
                    return $query->whereIn('aircraft_id', $aircraft_array);
                })
                ->first();

            if ($result && $result->total_fuel > 0 && $result->total_time > 0) {
                break;
            }
            $count_while++;
        }

        if ($result && $result->total_fuel > 0 && $result->total_time > 0) {
            $results['avg_pounds'] = round($result->total_fuel / $result->total_time, 2);
            $results['avg_metric'] = round($results['avg_pounds'] / 2.20462262185, 2);
        }

        return $results;
    }
}
