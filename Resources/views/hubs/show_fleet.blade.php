<div class="row row-cols-2">
  <div class="col">
    @if($aircraft_hub->count() > 0)
      <div class="card">
        <div class="card-header p-1">
          <h5 class="m-1">
            @lang('DBasic::common.haircraft')
            <i class="fas fa-plane m-1 float-end"></i>
          </h5>
        </div>
        <div class="card-body p-0 overflow-auto table-responsive">
          @include('DBasic::fleet.table', ['fleet' => $aircraft_hub, 'type' => 'hub'])
        </div>
        <div class="card-footer p-1 small text-end">
          @lang('DBasic::common.total'): {{ $aircraft_hub->count() }}
        </div>
      </div>
    @endif
  </div>
  <div class="col">
    @if($aircraft_off->count() > 0)
      <div class="card">
        <div class="card-header p-1">
          <h5 class="m-1">
            @lang('DBasic::common.vaircraft')
            <i class="fas fa-plane m-1 float-end"></i>
          </h5>
        </div>
        <div class="card-body p-0 overflow-auto table-responsive">
          @include('DBasic::fleet.table', ['fleet' => $aircraft_off, 'type' => 'hub'])
        </div>
        <div class="card-footer p-1 small text-end">
          @lang('DBasic::common.total'): {{ $aircraft_off->count() }}
        </div>
      </div>
    @endif
  </div>
</div>
   