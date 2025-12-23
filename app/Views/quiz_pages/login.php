<?= $this->include('quiz_pages/header'); ?>
<style>
    .login_container {
        display: flex;
        flex-direction: column;
        align-items: center;
        min-height: 100vh;
        padding: 40px 20px; /* adds space around content */
        background-color: #f8f9fa;
    }

    /* Logo at the top, centered */
    .form_logo {
        margin-bottom: 30px;
        background-color: white;
        padding: 10px 20px;
        border-radius: 8px;
        text-align: center;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        max-width: 90%;
        width: auto;
    }

    /* Login box styling */
    .login_form {
        background: white;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        width: 100%;
        max-width: 450px;
        text-align: center;
    }
</style>
<!-- Fixed Logo -->
<div class="login_container">
    <div class="form_logo">
        <a href="<?= base_url(); ?>">
            <img src="<?= base_url('assets/images/logo/logo.png'); ?>" alt="Psychometrica Logo">
        </a>
    </div>

    <!-- Login Container -->
    <div class="login_form">
        <h4 class="fw-bold mb-4">Login</h4>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show text-center" role="alert">
                <strong>Error!</strong> <?= session()->getFlashdata('error'); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('test-login'); ?>" method="POST">
            <div class="mb-3 text-start">
                <input type="text" class="form-control" name="user_id" placeholder="User name" required>
            </div>
            <div class="mb-3 text-start">
                <input type="password" class="form-control" name="password" placeholder="Password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>
    </div>
</div>

<?= $this->include('quiz_pages/footer'); ?>
