@extends('layout.login')

@section('title-head')
    <title>Login</title>
@endsection

@section('content')
    <div class="content-wrapper d-flex align-items-center auth">
        <div class="row flex-grow">
            <div class="col-lg-4 mx-auto">
                <div class="auth-form-light text-left p-5">
                    <div class="brand-logo text-center">
                        <img src="{{ asset('assets/images/logo.png') }}" alt="logo">
                    </div>
                    <h4>Hello! nice to see you</h4>
                    <h6 class="font-weight-light">Sign in to continue.</h6>
                    <form class="pt-3" method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="form-group">
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                                name="email" placeholder="input email" value="{{ old('email') }}" required
                                autocomplete="email" autofocus>

                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <input id="password" type="password" placeholder="input password"
                                class="form-control @error('password') is-invalid @enderror" name="password" required
                                autocomplete="current-password">
                            <h6 class="float-end my-2">
                                <a class="text-dark" href="javascript:;" data-bs-toggle='modal'
                                    data-bs-target='#forgetPasswordModal'>Forget
                                    Password?
                                </a>
                            </h6>
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <div class="btn-group mb-1">
                                <img src="{{ captcha_src('math') }}" alt="captcha">
                                <input id="captcha" type="text"
                                    class="form-control @error('captcha') is-invalid @enderror" style="width: 100%"
                                    name="captcha" placeholder="captcha" required>
                            </div>
                            <h6 class="float-end my-2">
                                <a class="text-dark" href="javascript:;" onclick="refreshCaptcha()">
                                    Refresh Captcha
                                </a>
                            </h6>
                            @error('captcha')
                                <span class="invalid-feedback" role="alert">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mt-5">
                            <button class="btn btn-block btn-gradient-primary btn-lg font-weight-medium auth-form-btn"
                                type="submit">
                                SIGN IN
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Forget Password Modal -->
    <div class="modal fade" id="forgetPasswordModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Forget password?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-center">Silahkan hubungi admin untuk reset password</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Forget Password Modal -->
@endsection

@section('javascript')
    <script>
        function refreshCaptcha() {
            fetch('{{ route("captcha.refresh") }}')
                .then(response => response.json())
                .then(data => {
                    document.querySelector('img[alt="captcha"]').src = data.captcha;
                });
        }
    </script>
@endsection
