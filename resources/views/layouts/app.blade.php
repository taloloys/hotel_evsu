<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'Hotel Management System')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>
        body{
            background:#f4f7fc;
            font-family:'Segoe UI',sans-serif;
        }

        .sidebar{
            width:260px;
            min-height:100vh;
            background:#0f172a;
            position:fixed;
            left:0;
            top:0;
        }

        .main-content{
            margin-left:260px;
            padding:30px;
        }

        .content-card{
            background:#fff;
            border:none;
            border-radius:20px;
            box-shadow:0 5px 20px rgba(0,0,0,.05);
        }

        .stat-card{
            border-radius:20px;
            color:white;
            padding:25px;
        }

        .nav-link{
            color:#cbd5e1;
            padding:12px 16px;
            border-radius:12px;
            margin-bottom:8px;
        }

        .nav-link:hover{
            background:#1e293b;
            color:white;
        }

        .nav-link.active{
            background:#2563eb;
            color:white;
        }
    </style>
</head>

<body>

<div class="sidebar">
    @include('layouts.sidebar')
</div>

<div class="main-content">

    @include('layouts.topbar')

    <div class="mt-4">
        @yield('content')
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>