{{--
    Procurement lifecycle pipeline step indicator.

    Usage:
        @include('partials.procurement-pipeline', ['pipelineStep' => 3])
        @include('partials.procurement-pipeline', ['pipelineStep' => 2, 'pipelineFailed' => true])

    $pipelineStep   int   1–7  (current active step; steps before it are completed)
                          0    renders all steps neutral — use on index/overview pages
    $pipelineFailed bool  true renders the active step in danger/red (e.g. rejected)
--}}
@php
    $pipelineFailed = $pipelineFailed ?? false;
    $pipelineSteps  = [
        1 => ['icon' => 'fa-file-alt',       'label' => trans('general.pl_submit')],
        2 => ['icon' => 'fa-user-check',     'label' => trans('general.pl_approve')],
        3 => ['icon' => 'fa-file-invoice',   'label' => trans('general.pl_create_po')],
        4 => ['icon' => 'fa-quote-right',    'label' => trans('general.pl_quotes')],
        5 => ['icon' => 'fa-trophy',         'label' => trans('general.pl_award')],
        6 => ['icon' => 'fa-box-open',       'label' => trans('general.pl_receive')],
        7 => ['icon' => 'fa-user-tag',       'label' => trans('general.pl_assign')],
    ];
@endphp

<div style="background:#f9f9f9;border:1px solid #e0e0e0;border-radius:4px;padding:14px 16px 10px;margin-bottom:16px;overflow-x:auto;"
     aria-label="{{ trans('general.pl_lifecycle') }}">
    <div style="font-size:11px;color:#888;text-transform:uppercase;letter-spacing:.6px;margin-bottom:10px;">
        <i class="fas fa-stream" aria-hidden="true"></i>
        {{ trans('general.pl_lifecycle') }}
    </div>
    <div style="display:flex;align-items:center;min-width:520px;">
        @foreach ($pipelineSteps as $num => $step)
            @php
                $isDone    = $pipelineStep > 0 && $num < $pipelineStep;
                $isActive  = $pipelineStep > 0 && $num === $pipelineStep;
                $isFailed  = $isActive && $pipelineFailed;

                if ($isDone) {
                    $circle = 'background:#5cb85c;border-color:#5cb85c;color:#fff;';
                    $label  = 'color:#3d8b3d;font-weight:600;';
                    $icon   = 'fa-check';
                } elseif ($isFailed) {
                    $circle = 'background:#d9534f;border-color:#d9534f;color:#fff;';
                    $label  = 'color:#d9534f;font-weight:600;';
                    $icon   = 'fa-times';
                } elseif ($isActive) {
                    $circle = 'background:#337ab7;border-color:#337ab7;color:#fff;';
                    $label  = 'color:#2255a4;font-weight:600;';
                    $icon   = $step['icon'];
                } else {
                    $circle = 'background:#fff;border-color:#ccc;color:#bbb;';
                    $label  = 'color:#bbb;';
                    $icon   = $step['icon'];
                }

                $connColor = $isDone ? '#5cb85c' : '#ddd';
            @endphp

            <div style="display:flex;flex-direction:column;align-items:center;min-width:66px;flex:0 0 auto;">
                <div style="width:34px;height:34px;border-radius:50%;border:2px solid;display:flex;align-items:center;justify-content:center;font-size:13px;{{ $circle }}">
                    <i class="fas {{ $icon }}" aria-hidden="true"></i>
                </div>
                <div style="font-size:10px;text-align:center;margin-top:5px;line-height:1.3;max-width:68px;{{ $label }}">
                    {{ $step['label'] }}
                </div>
            </div>

            @if ($num < count($pipelineSteps))
                <div style="flex:1;height:2px;background:{{ $connColor }};min-width:10px;margin-bottom:17px;"></div>
            @endif

        @endforeach
    </div>
</div>
