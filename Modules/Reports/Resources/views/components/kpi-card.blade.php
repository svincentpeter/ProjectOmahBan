@props(['title','value','icon'=>'bi-graph-up','variant'=>'blue','subtitle'=>null])

<div class="kpi-card kpi-{{ $variant }}">
  <div class="kpi-icon"><i class="bi {{ $icon }}"></i></div>
  <div class="kpi-body">
    <div class="kpi-title">{{ $title }}</div>
    <div class="kpi-value">{{ $value }}</div>
    @if($subtitle)<div class="kpi-sub">{{ $subtitle }}</div>@endif
  </div>
</div>
