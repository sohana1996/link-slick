<div class="col-md-2 noPadding">
    <div class="panel panel-default">
        <div class="panel-body">
            <ul class="nav nav-pills nav-stacked">
                {{--<li class="text-sm @if(isset($page['feedLoad']) && $page['feedLoad'] == 'home') active @endif"><a
                            href="{{env('APP_URL')}}/home">Shortener</a></li>--}}
                <li class="text-sm @if(isset($page['feedLoad']) && $page['feedLoad'] == 'links') active @endif"><a
                            href="{{env('APP_URL')}}/links">Destination Links</a></li>
                <li class="text-sm @if(isset($page['feedLoad']) && $page['feedLoad'] == 'sources') active @endif"><a
                            href="{{env('APP_URL')}}/asset/sources">Sources</a></li>
                <li class="text-sm @if(isset($page['feedLoad']) && $page['feedLoad'] == 'media') active @endif"><a
                            href="{{env('APP_URL')}}/asset/medium">Medium</a></li>
                <li class="text-sm @if(isset($page['feedLoad']) && $page['feedLoad'] == 'content') active @endif"><a
                            href="{{env('APP_URL')}}/asset/content">Content</a></li>
                <li class="text-sm @if(isset($page['feedLoad']) && $page['feedLoad'] == 'domains') active @endif"><a
                            href="{{env('APP_URL')}}/asset/domains">Domains</a></li>
            </ul>
        </div>
    </div>
</div>