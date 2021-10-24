@extends('app')
@section('title', $airline->name)

@section('content')
  <div class="row">
    {{-- LEFT --}}
    <div class="col-9">
      {{-- Navigation --}}
      <div class="nav nav-pills nav-justified mb-3" id="airline-nav" role="tablist">
        @if($subfleets->count() > 0)
          <button class="nav-link active mx-1" id="airline-fleet" data-bs-toggle="pill" data-bs-target="#fleet" type="button" role="tab" aria-controls="fleet" aria-selected="true">
            @lang('DBasic::common.fleet')
          </button>
        @endif
        @if($users->count() > 0)
          <button class="nav-link mx-1" id="airline-pilots" data-bs-toggle="pill" data-bs-target="#pilots" type="button" role="tab" aria-controls="pilots" aria-selected="false">
            @lang('DBasic::common.roster')
          </button>
        @endif
        @if($pireps->count() > 0)
          <button class="nav-link mx-1" id="airline-pireps" data-bs-toggle="pill" data-bs-target="#pireps" type="button" role="tab" aria-controls="pireps" aria-selected="false">
            @lang('DBasic::common.reports')
          </button>
        @endif
      </div>
      {{-- Content --}}
      <div class="tab-content" id="airline-navContent">
        @if($subfleets->count() > 0)
          <div class="tab-pane fade show active" id="fleet" role="tabpanel" aria-labelledby="airline-fleet">
            @include('DBasic::airlines.show_fleet')
          </div>
        @endif
        @if($users->count() > 0)
          <div class="tab-pane fade" id="pilots" role="tabpanel" aria-labelledby="airline-pilots">
            @include('DBasic::airlines.show_roster')
          </div>
        @endif
        @if($pireps->count() > 0)
          <div class="tab-pane fade" id="pireps" role="tabpanel" aria-labelledby="airline-pireps">
            @include('DBasic::airlines.show_reports')
          </div>
        @endif
      </div>
    </div>
    {{-- RIGHT --}}
    <div class="col-3">
      {{-- Airline Details --}}
      <div class="card mb-2">
        <div class="card-header p-1">
          <h5 class="m-1 p-0">
            @lang('DBasic::common.adetails')
            <i class="fas fa-info float-end m-1"></i>
          </h5>
        </div>
        <div class="card-body p-0 table-responsive">
          <table class="table table-sm table-borderless table-striped text-start mb-0">
            <tr>
              <th style="width:30%;">@lang('common.name')</th>
              <td class="text-end">{{ $airline->name }}</td>
            </tr>
            <tr>
              <th>@lang('DBasic::common.icao')</th>
              <td class="text-end">{{ $airline->icao }}</td>
            </tr>
            <tr>
              <th>@lang('DBasic::common.iata')</th>
              <td class="text-end">{{ $airline->iata }}</td>
            </tr>
            <tr>
              <th>@lang('common.country')</th>
              <td class="text-end">{{ $country->alpha2($airline->country)['name'] }} ({{ strtoupper($airline->country) }})</td>
            </tr>
          </table>
        </div>
        @if(filled($airline->logo))
          <div class="card-footer p-1 text-center">
            <img src="{{ $airline->logo }}" style="max-width: 90%; max-height: 70px;">
          </div>
        @endif
      </div>
      {{-- Overall Finance --}}
      <div class="card mb-2">
        <div class="card-header p-1">
          <h5 class="m-1 p-0">
            @lang('DBasic::common.finance')
            <i class="fas fa-receipt float-end m-1"></i>
          </h5>
        </div>
        <div class="card-body p-0 table-responsive">
          <table class="table table-sm table-borderless table-striped text-start mb-0">
            @foreach($finance as $key => $value)
              <tr>
                <th>{{ $key }}</th>
                <td class="text-end">{!! $value !!}</td>
              </tr>
            @endforeach
          </table>
        </div>
      </div>
      {{-- Basic Stats --}}
      <div class="card mb-2">
        <div class="card-header p-1">
          <h5 class="m-1 p-0">
            @lang('DBasic::stats.stats_gen')
            <i class="fas fa-cogs float-end m-1"></i>
          </h5>
        </div>
        <div class="card-body p-0 table-responsive">
          <table class="table table-sm table-borderless table-striped text-start mb-0">
            @foreach($stats_b as $key => $value)
              <tr>
                <th>{{ $key }}</th>
                <td class="text-end">{{ $value }}</td>
              </tr>
            @endforeach
          </table>
        </div>
      </div>
      {{-- Pirep Stats --}}
      <div class="card mb-2">
        <div class="card-header p-1">
          <h5 class="m-1 p-0">
            @lang('DBasic::stats.stats_rep')
            <i class="fas fa-cogs float-end m-1"></i>
          </h5>
        </div>
        <div class="card-body p-0 table-responsive">
          <table class="table table-sm table-borderless table-striped text-start mb-0">
            @foreach($stats_p as $key => $value)
              <tr>
                <th>{{ $key }}</th>
                <td class="text-end">{{ $value }}</td>
              </tr>
            @endforeach
          </table>
        </div>
      </div>
    </div>
  </div>
@endsection