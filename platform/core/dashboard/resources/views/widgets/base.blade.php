@if (empty($widgetSetting) || $widgetSetting->status == 1)
    <div class="{{ $widget->column }} col-12 widget_item" id="{{ $widget->name }}" data-url="{{ $widget->route }}">
        <div class="portlet light bordered portlet-no-padding @if ($widget->hasLoadCallback) widget-load-has-callback @endif">
            <div class="portlet-title">
                <div class="caption">
                    <i class="{{ $widget->icon }} font-dark" style="font-weight: 700;"></i>
                    <span class="caption-subject font-dark">{{ $widget->title }}</span>
                    @if($widget->title == 'Activities Logs')
                        <button class="btn btn-primary" title="Decrypt Logs" data-toggle="modal" data-target="#decryptLogsModal"><i class="fa fa-lock-open"></i></button>
                    @endif
                </div>
                @include('core/dashboard::partials.tools', ['settings' => !empty($widgetSetting) ? $widgetSetting->settings : []])
            </div>
            <div class="portlet-body @if ($widget->isEqualHeight) equal-height @endif widget-content {{ $widget->bodyClass }} {{ Arr::get(!empty($widgetSetting) ? $widgetSetting->settings : [], 'state') }}"></div>
        </div>
    </div>
@endif


<div class="modal fade" id="decryptLogsModal" tabindex="-1" aria-labelledby="decryptLogsModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
    <form method="post" action="somethign">
      <div class="modal-header">
        <h5 class="modal-title" id="decryptLogsModalLabel">Decrypt Logs</h5>
        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row ml-2">
            <p>Please Provide your private key to decrypt logs.</p>
        </div>        
        <div class="row ml-2 mr-2">
            <textarea name="decryption_key" class="form-control" placeholder="Decryption Key"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Decrypt</button>
      </div>
      </form>
    </div>
  </div>
</div>
