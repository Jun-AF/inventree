<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Inventree') }}</title>

    <link rel="stylesheet" href="{{ asset('vendor\bootstrap-5.0.2-dist\css\bootstrap.css') }}">
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="card">
                <div class="card-header bg-slate-200 d-flex align-items-between">
                    <img src="{{ asset('img/SIS-removebg-preview.png') }}">
                </div>
                <div class="card-body">
                    <div class="container-fluid">
                        <form action="{{ route('login') }}" method="POST">
                            @csrf
                            <div class="row form-group">
                                <div class="col-md-2">
                                    <label for="email">Email</label>
                                </div>
                                <div class="col">
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" aria-describedby="emailHelp" name="email" placeholder="Enter Email Address..." value="{{ old('email') }}" required>
                                    @error('email')
                                        <span class="small">Email is invalid</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-2">
                                    <label for="password">Password</label>
                                </div>
                                <div class="col">
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" placeholder="Password" name="password" required>
                                    @error('password')
                                        <span class="small">Password is unrecognized</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col">
                                    <div class="custom-control custom-checkbox small">
                                        <input type="checkbox" class="custom-control-input" id="customCheck">
                                        <label class="custom-control-label" for="customCheck">Remember Me</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col">
                                    <button class="button button-primary button-shadow button-block"> Sign in </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('vendor\bootstrap-5.0.2-dist\js\bootstrap.js') }}"></script>
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
</body>
</html>