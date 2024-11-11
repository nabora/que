<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Bootstrap CSS -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- FontAwesome CSS -->
    <link href="{{ asset('assets/css/font-awesome.min.css') }}" rel="stylesheet">
    <!-- Custom Style CSS -->
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">

    <title>Educational Bootstrap 5 Login Page Website Template</title>
    <style>
        .welcome-text {
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
            color: #007bff;
            font-weight: 700;
        }

        .welcome-text small {
            display: block;
            font-size: 16px;
            color: #6c757d;
            margin-top: 5px;
        }

        .form-03-main {
            padding: 30px;
            background-color: #f8f9fa;
            border-radius: 8px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        .footer {
            background-color: #007bff;
            color: white;
            text-align: center;
            padding: 23px 0;
            position: absolute;
            width: 100%;
            bottom: 0;
            left: 0;
        }

        .footer p {
            margin: 0;
        }
    </style>
</head>

<body>
    <section class="form-02-main">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="_lk_de">
                        <div class="form-03-main">
                            <div class="logo">
                                <img src="{{ asset('assets/images/user.png') }}" alt="User Icon">
                            </div>
                            <div class="logo1">
                                <img src="{{ asset('assets/images/bg.png') }}" alt="User Icon">
                            </div>

                            <!-- Welcome Text -->
                            <div class="welcome-text">
                                Welcome to DepEd Bukidnon Division Office Queueing System
                                <br>
                            </div>

                            <!-- Display any error messages -->
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <!-- Login Form -->
                            <form method="POST" action="{{ route('login') }}">
                                @csrf
                                <div class="form-group">
                                    <input type="email" name="email" class="form-control _ge_de_ol"
                                        placeholder="Enter Email" required aria-required="true"
                                        value="{{ old('email') }}">
                                </div>

                                <div class="form-group">
                                    <input type="password" name="password" class="form-control _ge_de_ol"
                                        placeholder="Enter Password" required aria-required="true">
                                </div>
                                <div class="form-group">
                                    <div class="_btn_04">
                                        <button type="submit" class="btn btn-primary">Login</button>
                                    </div>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; {{ date('Y') }} DepEd Bukidnon Division Office. All Rights Reserved.</p>
    </footer>

    <!-- Bootstrap JS (Optional, if you need JS functionality) -->
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
</body>

</html>
