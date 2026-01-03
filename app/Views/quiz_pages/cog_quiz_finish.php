<div class="container-fluid p-3">
    <div class="row align-items-center">
        <!-- Logo -->
        <div class="col-12 col-md-4 text-start text-md-left mb-2 mb-md-0">
            <a href="<?= base_url(); ?>">
                <img src="<?= base_url(session()->get('test_company_logo') ?: 'assets/images/logo/logo.png'); ?>" 
                     alt="Psychometrica Logo" 
                     class="img-fluid" 
                     style="max-height: 60px;">
            </a>
        </div>

        <!-- Timer -->
        <div class="col-12 col-md-4 text-center position-relative">

        </div>

        <!-- Welcome + Logout -->
        <div class="col-12 col-md-4 text-end text-md-end">
            <?php if (session()->get('test_name')): ?>
                <div class="d-flex justify-content-end align-items-center gap-3">
                    <span class="fw-bold step_box_desc" style="font-size: 1rem;">
                        Welcome, <?= esc(session()->get('test_name')); ?> 👋
                    </span>
                    <a href="<?= site_url('test/logout') ?>" 
                       class="btn btn-warning btn-sm text-uppercase text-white">
                        Logout
                    </a>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<!-- Finish Content -->
<div class="text-center mt-5 p-4">
    <h2 class="text-success fw-bold mb-3">🎉 Congratulations! <?= esc(session()->get('quiz_name')); ?></h2>
    <p class="fs-5 mb-4 text-dark">
        You have successfully completed the test.
    </p>
</div>

<div class="container mt-4">
    <h3 class="mb-4">Test summary of attemped questions </h3>

    <div class="card mb-3" ng-repeat="factor in groupedFactors">
        <div class="card-body">
            <h5 class="card-title text-success">{{ factor.factorName }} </h5>
            <p class="card-text">
                Answered: {{ getAnsweredCountForFactor(factor) }} / {{ getTotalQuestionsForFactor(factor) }}
            </p>
        </div>
    </div>
</div>







