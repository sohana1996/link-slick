<!DOCTYPE html>

<html>
<head>

    <title>{{$ddt['title']}}</title>
    <link rel="icon" href="{{$ddt['favicon']}}"/>
    <meta charset="UTF-8">
    <meta content="text/html;charset=utf-8" http-equiv="Content-Type">
    <meta content="utf-8" http-equiv="encoding">

    @if(isset($metaTags))
        @foreach($metaTags as $k=>$mtag)
            <meta {{$mtag['type']}}="{{$mtag['name']}}" content="{{$mtag['content']}}">
        @endforeach
    @endif

    @if(isset($storeImg))
        @foreach($storeImg as $k=>$imgtag)
            <meta property="og:image" content="{{$imgtag}}">
            <meta name="twitter:image" content="{{$imgtag}}">
        @endforeach
    @endif




    <style>
        * {
            box-sizing: border-box;
            outline: 0;
        }

        body {
            margin: 0;
            padding: 0;
            overflow: hidden;
        }

        #IntercomDefaultWidget {
            display: none;
        }

        #Intercom {
            display: none;
        }

        .link-iframe {
            height: 100vh;
            width: 100%;
            border: 0;
        }
    </style>
    <meta name="nodo-proxy" content="html"/>
</head>
<body>
<iframe src="{{$findShort->url}}" class="link-iframe"></iframe>

{!! $findShort->write_script !!}
</body>
</html>
