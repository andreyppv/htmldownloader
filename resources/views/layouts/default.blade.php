<!DOCTYPE html>
<html lang="en">
<head>
    @include('layouts.default.head')
</head>
<body>
    @include('layouts.default.header')

    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                @yield('content')
            </div>
        </div>
    </div>

    @include('layouts.default.foot')
</body>
</html>
