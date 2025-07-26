<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-center">
                <h5 class="modal-title text-center w-100" id="userModalLabel">Akun Saya</h5>
                <button type="button" class="btn-close position-absolute end-0 me-3" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <a href="{{ route('profile') }}" class="btn btn-light w-100 mb-2">
                    <i class="fa fa-user me-2"></i> Profile
                </a>
                <a href="{{ route('forgot-password') }}" class="btn btn-light w-100 mb-2">
                    <i class="fa fa-key me-2"></i> Lupa Sandi
                </a>
                <a href="{{ route('cart') }}" class="btn btn-light w-100 mb-2">
                    <i class="fa fa-shopping-cart me-2"></i> Lihat Keranjang
                </a>
                <form action="{{ route('logout') }}" method="GET">
                    <button type="submit" class="btn btn-danger w-100">
                        <i class="fa fa-sign-out-alt me-2"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
