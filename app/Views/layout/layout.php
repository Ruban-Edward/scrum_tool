<?= $this->include('layout/header') ?>
<?= $this->include('layout/sidebar') ?>
<div id="main" class="main-content">
    <?php if(isset($breadcrumbs)): ?>
    <div >
        <p class="bread">
            <?php foreach($breadcrumbs as $key => $url): ?>
                <a href="<?= $url ?>"><?= ucfirst($key) ?></a>
                <?php if($url !== end($breadcrumbs)): ?> > <?php endif; ?>
            <?php endforeach; ?>
        </p>
    </div>
    <?php endif; ?>
    
    <?php if(isset($title)): ?>
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center mb-3 border-bottom">
        <h1 class="cls-h2"><?= $title ?></h1>
    </div>
    <?php endif; ?>

    <?php if(isset($view)){
        echo $this->include($view);
    } ?>
</div>
<?= $this->include('layout/footer') ?>