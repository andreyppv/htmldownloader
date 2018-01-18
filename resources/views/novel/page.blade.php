<html>
<head>
    <meta charset="utf-8">

    <style>
        p { padding-left: 0 !important; text-align: left; }
    </style>
</head>
<body>
    <div style="width:1024px;margin:0 auto;">
        @if($index > 0)
            <a href="000.html" style="text-align:center;display:block;">To Index Page</a>
            <a href="<?php echo sprintf('%03d.html', $index-1); ?>" style="text-align:center;display:block;">Prev Page</a>
            <a href="<?php echo sprintf('%03d.html', $index+1); ?>" style="text-align:center;display:block;">Next Page</a>
        @endif

        {!! $content !!}

        @if($index > 0)
            <a href="000.html" style="text-align:center;display:block;">To Index Page</a>
            <a href="<?php echo sprintf('%03d.html', $index-1); ?>" style="text-align:center;display:block;">Prev Page</a>
            <a href="<?php echo sprintf('%03d.html', $index+1); ?>" style="text-align:center;display:block;">Next Page</a>
        @endif
    </div>
</body>
</html>
