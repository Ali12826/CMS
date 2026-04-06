<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GIGA MALL CMS</title>

    <!-- Bootstrap 3 CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- Flatpickr -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,500,600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
        }

        /* --- SIMPLIFIED SIDEBAR --- */
        .main-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            width: 250px;
            background-color: #222d32;
            color: #fff;
            z-index: 1000;
            overflow-y: auto;
        }

        /* --- CONTENT WRAPPER --- */
        /* This margin pushes ALL content to the right, creating space for sidebar */
        .content-wrapper {
            margin-left: 250px;
            padding: 20px;
            min-height: 100vh;
            background-color: #f4f6f9;
            transition: margin-left 0.3s;
        }

        /* --- FOOTER --- */
        .main-footer {
            margin-left: 250px;
            padding: 15px;
            background: #fff;
            border-top: 1px solid #d2d6de;
            text-align: center;
        }

        /* Mobile Responsiveness */
        @media (max-width: 768px) {
            .main-sidebar { width: 0; overflow: hidden; }
            .content-wrapper { margin-left: 0; }
            .main-footer { margin-left: 0; }
        }
    </style>
</head>
<body>

    <!-- 1. The Fixed Sidebar -->
    @auth
        @include('partials.sidebar')
    @endauth

    <!-- 2. The Main Content Area -->
    <div class="content-wrapper">
        @yield('content')
    </div>

    <!-- 3. The Footer -->
    @auth
        @include('partials.footer')
    @endauth

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        // Global Datepicker Init
        $(document).ready(function(){
            flatpickr(".datetimepicker", {
                enableTime: true,
                dateFormat: "Y-m-d H:i",
                time_24hr: true
            });
        });
    </script>
</body>
</html>
