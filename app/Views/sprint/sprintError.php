<script>
    const BASE_URL = "<?= ASSERT_PATH ?>";
    var permit = <?= has_permission('sprint/edit') ? 1 : 0 ?>;
</script>
<?php if (has_permission('sprint/navcreatesprint')): ?>
    <div class="row">
        <ul class="header_button  d-flex align-items-center">
            <li><a href='<?= ASSERT_PATH ?>sprint/navcreatesprint'><button id="sprint" class="btn primary_button" style="
    margin-left: 6px;
    margin-top: 1px;
"><i class="fas fa-plus-circle"></i> Create sprint</button></a></li>
        </ul>
    </div>
<?php endif; ?>
<h5 style="text-align: center;">
    No records found
</h5>