@extends('app')
@section('title', __('DBasic::common.hubs'))

@section('content')
  <div class="row row-cols-3">
    @foreach($hubs as $hub)
      <div class="col">
        <div class="card mb-2">
          <div class="card-header p-1">
            <h5 class="m-1">
              <a href="{{ route('DBasic.hub', [$hub->id]) }}">{{ $hub->name }}</a>
              <span class="float-end m-1 flag-icon flag-icon-{{ strtolower($hub->country) }}" style="font-size: 1.1rem;"></span>
            </h5>
          </div>
          <div class="card-body p-0 table-responsive">
            <table class="table table-sm table-borderless table-striped text-start align-middle mb-0">
              <tr>
                <th>@lang('DBasic::common.icao')</th>
                <td class="text-end">{{ $hub->icao }}</td>
              </tr>
              <tr>
                <th>@lang('DBasic::common.iata')</th>
                <td class="text-end">{{ $hub->iata ?? '--' }}</td>
              </tr>
              <tr>
                <th>@lang('common.country')</th>
                <td class="text-end">
                  @if(strlen($hub->country) === 2)
                    {{ $country->alpha2($hub->country)['name'] }} ({{ strtoupper($hub->country) }})
                  @endif
                </td>
              </tr>
            </table>
          </div>
        </div>
      </div>
    @endforeach
  </div>
@endsection