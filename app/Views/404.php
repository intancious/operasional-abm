<?= $this->extend('auth/template'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md">
            <div class="card mt-5 shadow-lg">
                <div class="card-body">
                    <div class="text-center">
                        <h1 style="font-size: 75px;">404</h1>
                        <p class="lead text-gray-800 mb-0">Page Not Found</p>
                        <p class="text-gray-500 mb-0">The requested URL was not found on this server.</p>
                        <p class="text-gray-500 mb-4 small">That's all we know.</p>
                        <a href="/" class="text-decoration-none text-uppercase small"><i class="fas fa-arrow-circle-left mr-1"></i> Back to Dashboard</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>