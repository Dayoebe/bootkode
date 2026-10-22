@if($files)
    <div class="space-y-2 text-sm">
        <h4 class="font-semibold text-content">Submitted files</h4>
        <ul class="space-y-2">@foreach($files as $file)<li class="min-w-0">@if($file['available'])<a href="{{ $file['url'] }}" class="break-all font-medium text-action underline">{{ $file['name'] }}</a>@else<span class="break-all text-content">{{ $file['name'] }} · File unavailable</span>@endif <span class="text-content-muted">({{ $file['size'] < 1024 ? $file['size'].' bytes' : number_format($file['size'] / 1024, 1).' KiB' }})</span></li>@endforeach</ul>
    </div>
@endif
