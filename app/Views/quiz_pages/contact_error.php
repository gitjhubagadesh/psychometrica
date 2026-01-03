<div class="container-fluid p-3">
    <div class="row align-items-center">
        <!-- Logo -->
        <div class="col-12 col-md-4 text-start text-md-left mb-2 mb-md-0">
            <a href="<?= base_url(); ?>">
                <img src="<?= base_url(session()->get('quiz_company_logo') ?: 'assets/images/logo/logo.png'); ?>" 
                     alt="Psychometrica Logo" 
                     class="img-fluid" 
                     style="max-height: 60px;">
            </a>
        </div>

        <!-- Timer -->
        <div class="col-12 col-md-4 text-center position-relative">
            <!-- Add your timer content here -->
        </div>

        <!-- Welcome + Logout -->
        <div class="col-12 col-md-4 text-end text-md-end">
            <?php if (session()->get('quiz_name')): ?>
                <div class="d-flex justify-content-end align-items-center gap-3">
                    <span class="fw-bold step_box_desc" style="font-size: 1rem;">
                        Welcome, <?= esc(session()->get('quiz_name')); ?> 👋
                    </span>
                    <a href="<?= site_url('quiz/logout') ?>" 
                       class="btn btn-warning btn-sm text-uppercase text-white">
                        Logout
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="text-center mt-5">
    <p class="text-danger fs-5">
        Something went wrong with your quiz submission. <br>
        We apologize for the inconvenience caused. Please contact our support team for further assistance.
    </p>
    <p class="text-muted">
        If you need help or have any questions, feel free to reach out to us at <a href="mailto:admin@example.com">admin@example.com</a>. Our team is here to help you resolve the issue as quickly as possible.
    </p>
</div>
