@foreach($pages as $page) @include('marksheet-pdf', $page) @if(!$loop->last)<div style="page-break-after:always"></div>@endif @endforeach
