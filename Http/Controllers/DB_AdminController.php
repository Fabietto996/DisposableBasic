<?php

namespace Modules\DisposableBasic\Http\Controllers;

use App\Contracts\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Modules\DisposableBasic\Services\DB_FleetServices;

class DB_AdminController extends Controller
{
    public function index()
    {
        // Get settings (of Disposable Basic)
        $settings = DB::table('disposable_settings')->where('key', 'LIKE', 'dbasic.%')->get();
        // $settings = $settings->groupBy('group'); // This may be used to have all settings in one card like phpVMS core

        return view('DBasic::admin.index', [
            'settings' => $settings,
        ]);
    }

    public function settings_update()
    {
        $formdata = Request::post();
        $section = null;

        foreach ($formdata as $id => $value) {

            if ($id === 'group') {
                $section = $value;
            }

            $setting = DB::table('disposable_settings')->where('id', $id)->first();

            if (!$setting) {
                continue;
            }

            Log::debug('Disposable Basic, ' . $setting->group . ' setting for ' . $setting->name . ' changed to ' . $value);
            DB::table('disposable_settings')->where(['id' => $setting->id])->update(['value' => $value]);
        }

        flash()->success($section . ' settings saved.');
        return redirect(route('DBasic.admin'));
    }

    public function park_aircraft()
    {
        $formdata = Request::post();
        $FleetSvc = app(DB_FleetServices::class);
        $result = $FleetSvc->ParkAircraft($formdata['aircraft_reg']);

        if ($result === 0) {
            flash()->error('Nothing Done... Aircraft Not Found or was already PARKED');
        } elseif ($result === 1) {
            flash()->success('Aircraft State Changed Back to PARKED');
        } elseif ($result === 2) {
            flash()->success('Aircraft State Changed Back to PARKED and Pirep CANCELLED');
        }

        return redirect(route('DBasic.admin'));
    }

    // Database Checks
    public function health_check()
    {
        // Build Arrays from what we have
        $current_users = DB::table('users')->pluck('id')->toArray();
        $current_airports = DB::table('airports')->pluck('id')->toArray();
        $current_pireps = DB::table('pireps')->pluck('id')->toArray();
        $current_airlines = DB::table('airlines')->pluck('id')->toArray();
        $current_aircraft = DB::table('aircraft')->pluck('id')->toArray();
        // Check Pireps
        $pirep_user = DB::table('pireps')->whereNotIn('user_id', $current_users)->pluck('id')->toArray();
        $pirep_comp = DB::table('pireps')->whereNotIn('airline_id', $current_airlines)->pluck('id')->toArray();
        $pirep_orig = DB::table('pireps')->whereNotIn('dpt_airport_id', $current_airports)->pluck('id')->toArray();
        $pirep_dest = DB::table('pireps')->whereNotIn('arr_airport_id', $current_airports)->pluck('id')->toArray();
        $pirep_acft = DB::table('pireps')->whereNotIn('aircraft_id', $current_aircraft)->pluck('id')->toArray();
        // Check Acars Table
        $acars_pirep = DB::table('acars')->whereNotIn('pirep_id', $current_pireps)->pluck('id')->toArray();
        // Check Subfleets
        $fleet_comp = DB::table('subfleets')->whereNotIn('airline_id', $current_airlines)->pluck('id')->toArray();
        // Check Flights
        $flight_comp = DB::table('flights')->whereNotIn('airline_id', $current_airlines)->pluck('id')->toArray();
        $flight_orig = DB::table('flights')->whereNotIn('dpt_airport_id', $current_airports)->pluck('id')->toArray();
        $flight_dest = DB::table('flights')->whereNotIn('arr_airport_id', $current_airports)->pluck('id')->toArray();
        // Check Users
        $users_comp = DB::table('users')->whereNotIn('airline_id', $current_airlines)->pluck('id')->toArray();
        $users_field = DB::table('user_field_values')->whereNotIn('user_id', $current_users)->pluck('id')->toArray();
        // Missing Airports
        $airports_pirep_dep = DB::table('pireps')->whereNotIn('dpt_airport_id', $current_airports)->groupBy('dpt_airport_id')->pluck('dpt_airport_id')->toArray();
        $airports_pirep_arr = DB::table('pireps')->whereNotIn('arr_airport_id', $current_airports)->groupBy('arr_airport_id')->pluck('arr_airport_id')->toArray();
        $airports_flight_dep = DB::table('flights')->whereNotIn('dpt_airport_id', $current_airports)->groupBy('dpt_airport_id')->pluck('dpt_airport_id')->toArray();
        $airports_flight_arr = DB::table('flights')->whereNotIn('arr_airport_id', $current_airports)->groupBy('arr_airport_id')->pluck('arr_airport_id')->toArray();

        $missing_airports = array_merge($airports_pirep_dep, $airports_pirep_arr, $airports_flight_dep, $airports_flight_arr);
        $missing_airports = array_unique($missing_airports, SORT_STRING);

        return view('DBasic::admin.health_check', [
            'acars_pirep' => $acars_pirep,
            'fleet_comp'  => $fleet_comp,
            'flight_comp' => $flight_comp,
            'flight_orig' => $flight_orig,
            'flight_dest' => $flight_dest,
            'missing_apt' => $missing_airports,
            'pirep_user'  => $pirep_user,
            'pirep_comp'  => $pirep_comp,
            'pirep_orig'  => $pirep_orig,
            'pirep_dest'  => $pirep_dest,
            'pirep_acft'  => $pirep_acft,
            'users_comp'  => $users_comp,
            'users_field' => $users_field,
        ]);
    }
}
