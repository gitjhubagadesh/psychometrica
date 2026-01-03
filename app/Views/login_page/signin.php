<?php echo view('login_page/header'); ?>



<div class="container w-xxl w-auto-xs" ng-controller="SigninFormController" ng-init="app.settings.container = false;">
    <a href class="navbar-brand block m-t">Psychometrica</a>
    <div class="m-b-lg">
        <div class="wrapper text-center">
            <strong>Your People.... Our Insights</strong>
        </div>

        <!-- Show error message -->
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger text-center">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <form action="<?= site_url('signin/loginProcess') ?>" method="post">
            <div class="list-group list-group-sm">
                <div class="list-group-item">
                    <input type="text" placeholder="Username" name="username" class="form-control no-border" value="<?= old('username') ?>" required>
                </div>
                <div class="list-group-item">
                    <input type="password" placeholder="Password" name="password" class="form-control no-border" required>
                </div>
            </div>
            <button type="submit" class="btn btn-lg btn-primary btn-block">Log in</button>
            <div class="line line-dashed"></div>
        </form>
    </div>
</div>


<?php echo view('login_page/footer'); ?>
