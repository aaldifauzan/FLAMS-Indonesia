@extends('layouts.main')

@section('container')
<div class="row justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="col-lg-4 col-md-6 col-sm-8">
        <main class="form-signin shadow p-3 mb-5 bg-white rounded">
            <h1 class="h3 fw-normal text-left"><span style="font-weight: bold;">Login</span></h1>

            <div class="mb-3 text-left">
              <small class="d-block">Please login to access the dashboard.</small>
          </div>
            @if (session()->has('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session()->has('loginError'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('loginError') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="/login" method="POST">
                @csrf
                <div class="form-floating mb-3">
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                        id="email" placeholder="Email address" autofocus required value="{{ old('email') }}">
                    @error('email')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
                <div class="form-floating mb-3 position-relative">
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                        id="password" placeholder="Password" required>
                    <span class="position-absolute end-0 top-50 translate-middle-y pe-3" id="toggle-password" style="cursor: pointer;">
                        <i class="bi bi-eye-slash" id="toggle-icon"></i>
                    </span>
                    @error('password')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
                <button class="btn btn-primary w-100 mt-3" type="submit">Login</button>
            </form>
            <div class="mt-3 text-center">
                <small>Not registered? <a href="/register">Register Now!</a></small>
            </div>
        </main>
    </div>
</div>

<script>
    document.getElementById('toggle-password').addEventListener('click', function (e) {
        const password = document.getElementById('password');
        const icon = document.getElementById('toggle-icon');
        if (password.type === 'password') {
            password.type = 'text';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        } else {
            password.type = 'password';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        }
    });
</script>

@endsection
