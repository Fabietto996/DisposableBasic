<div class="row row-cols-4">
  <div class="col">
    @widget('DBasic::LeaderBoard', ['source' => 'pilot', 'period' => 'currentm', 'count' => 5, 'type' => 'flights'])
    @widget('DBasic::LeaderBoard', ['source' => 'pilot', 'period' => 'lastm', 'count' => 3, 'type' => 'flights'])
  </div>
  <div class="col">
    @widget('DBasic::LeaderBoard', ['source' => 'pilot', 'period' => 'currentm', 'count' => 5, 'type' => 'time'])
    @widget('DBasic::LeaderBoard', ['source' => 'pilot', 'period' => 'lastm', 'count' => 3, 'type' => 'time'])
  </div>
  <div class="col">
    @widget('DBasic::LeaderBoard', ['source' => 'pilot', 'period' => 'currentm', 'count' => 5, 'type' => 'distance'])
    @widget('DBasic::LeaderBoard', ['source' => 'pilot', 'period' => 'lastm', 'count' => 3, 'type' => 'distance'])
  </div>
  <div class="col">
    @widget('DBasic::LeaderBoard', ['source' => 'pilot', 'period' => 'currentm', 'count' => 5, 'type' => 'lrate'])
    @widget('DBasic::LeaderBoard', ['source' => 'pilot', 'period' => 'lastm', 'count' => 3, 'type' => 'lrate'])
  </div>
</div>
