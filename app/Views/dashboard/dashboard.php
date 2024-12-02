
<div class="row content ">
    <?php 
    // for count the delayed products and on-track products 
    $ontrackProducts = 0;
    $delayedProducts = 0;
    $delayedProductsId = [];
    foreach($data['on_track_and_delay'] as $value){
        if($value['delay'] > 0){
            $delayedProducts += 1;
            $delayedProductsId[] = $value['r_product_id'];
        }
        else{
            $ontrackProducts += 1;
        }
    }
    ?>
            <div class="col-md-12 ">
                <div class="container">
                    <div class="row mt-2" id="card-row">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body bg-card-1">
                                    <h5 class="card-title d-flex justify-content-left align-items-center">
                                        <div>
                                            Product portfolio
                                        </div>
                                    </h5>                                             
                                    <div class="row d-flex align-items-center">
                                        <div class="col-md-3">
                                            <h1><i class="icon-git-pull-request"></i></h1>
                                        </div>
                                        <div class="col-md-9">
                                            <div class="row mx-0 align-items-center pointer" onclick="orderByRunningSprint('active')"
                                                data-toggle="tooltip" data-placement="top"  title="Click to see the active products">
                                                <p class="card-text col-sm-8 d-flex justify-content-between">
                                                    <strong>Active products </strong> :
                                                </p>
                                                <div class="col-sm-4 text-end">
                                                    <span class="badge cls-span">
                                                        <?= count($data['on_track_and_delay']) ?>
                                                    </span>
                                                </div>
                                            </div>
                                            
                                            <div class="row mx-0 align-items-center pointer" onclick="orderByRunningSprint('onTrack')"
                                                data-toggle="tooltip" data-placement="top"  title="Click to see the on-track products">
                                                <p class="card-text col-sm-8 d-flex justify-content-between">
                                                    <strong>Product on-track </strong> :
                                                </p>
                                                <div class="col-sm-4 text-end">
                                                    <span class="badge cls-span">
                                                        <?= $ontrackProducts?>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="row mx-0 align-items-center pointer" onclick="orderByRunningSprint('delayed')"
                                                data-toggle="tooltip" data-placement="top"  title="Click to see the delayed products">
                                                <p class="card-text col-sm-8 d-flex justify-content-between">
                                                    <strong>Delayed products </strong> :
                                                </p>
                                                <div class="col-sm-4 text-end">
                                                    <span class="badge cls-span">
                                                        <?= $delayedProducts?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body bg-card-2">
                                    <?php 
                                    $projectsWithSprints = [];
                                    $activeSprintsCount = 0;
                                    $pendingTask = 0;
                                    $pendingTaskWithCompletion = [];
                                    if(is_array($data['sprints'])){
                                        foreach($data['sprints'] as $value){
                                            // check the variables is set or not
                                            if(
                                                isset($value['r_product_id']) 
                                                && isset($value['sprint_id']) 
                                                && isset($value['sprint_version'])
                                            ){
                                                // check projectsWithSprints variable has product_id with sprint_id as an key
                                                if(! isset($projectsWithSprints[$value['r_product_id']][$value['sprint_id']])) {
                                                    $projectsWithSprints[$value['r_product_id']][$value['sprint_id']] = [
                                                                                                                            $value['sprint_version'],
                                                                                                                            $value['delay'],
                                                                                                                            $value['end_date']
                                                                                                                        ];
                                                    $activeSprintsCount += 1;
                                                }
                                            }
                                            // check the task is setted and the status is running  
                                            if(
                                                isset($value['task_status']) 
                                                && isset($value['r_task_id']) 
                                                && in_array($value['task_status'],$data['pending_task_status'])
                                                && isset($value['sprint_task_deleted'])
                                                && isset($value['task_deleted'])
                                            ){
                                                // check the task is not deleted
                                                if(
                                                    ($value['sprint_task_deleted'] == 'N')
                                                    && ($value['task_deleted'] == 'N')
                                                ){
                                                    $pendingTask += 1;
                                                    $pendingTaskWithCompletion[$value['r_task_id']] = $value['completed_percentage'];
                                                }
                                            }
                                        }
                                    }
                                    ?>
                                    <h5 class="card-title d-flex justify-content-left align-items-center">
                                        <div>
                                            Sprint performance
                                        </div>
                                    </h5>
                                    <div class="row d-flex align-items-center">
                                        <div class="col-md-3">
                                            <h1><i class="icon-sliders"></i></h1> 
                                        </div>
                                        <div class="col-md-9">
                                            <div class="row mx-0 align-items-center">
                                                <p class="card-text col-sm-8 d-flex justify-content-between">
                                                    <strong>Active sprints</strong>:
                                                </p>
                                                <div class="col-sm-4 text-end">
                                                    <span class="badge">
                                                        <?= $activeSprintsCount ?>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="row mx-0 align-items-center">
                                                <p class="card-text col-sm-8 d-flex justify-content-between">
                                                    <strong>Pending tasks</strong>: 
                                                </p>
                                                <div class="col-sm-4 text-end">
                                                    <span class="badge"> 
                                                        <?= $pendingTask ?>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="row mx-0 align-items-center">
                                                <p class="card-text col-sm-8 d-flex justify-content-between">
                                                    <strong>Completion rate</strong>: 
                                                </p>
                                                <div class="col-sm-4 text-end">
                                                    <span class="badge">
                                                        <?php
                                                        if(! empty($pendingTaskWithCompletion)){
                                                            echo round(array_sum($pendingTaskWithCompletion)/count($pendingTaskWithCompletion))."%";
                                                        }
                                                        else {
                                                            echo "0%";
                                                        } 
                                                        ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body bg-card-3">
                                    <?php
                                    // for get backlog items priority                                     
                                    $backlogPriority = array_column($data['backlogs'],'pblsCount','priority');
                                    ?>
                                    <h5 class="card-title d-flex justify-content-left align-items-center">
                                        <div>
                                            Global backlogs  
                                            (<?= array_sum($backlogPriority)?>)
                                        </div>
                                    </h5>
                                    <!-- <div class="row mx-0 align-items-center">                                    
                                        <p class="card-text col-sm-8"><strong>Total Items:</strong> </p>
                                        <div class="col-sm-4 px-2">
                                            <span class="badge bg-success">
                                                <?= ""//($highCount??0)+($mediumCount??0)+($lowCount??0)?>
                                            </span>
                                        </div>
                                    </div> -->
                                    <div class="row d-flex align-items-center">
                                        <div class="col-md-3">
                                            <h1><i class="icon-globe"></i></h1>
                                        </div>
                                        <div class="col-md-9">
                                            <div class="row mx-0 align-items-center">
                                                <p class="card-text col-sm-8 d-flex justify-content-between">
                                                    <strong>High priority</strong>: 
                                                </p>
                                                <div class="col-sm-4 text-end">
                                                    <span class="badge">
                                                        <?= $backlogPriority['H'] ?? 0?>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="row mx-0 align-items-center">
                                                <p class="card-text col-sm-8 d-flex justify-content-between">
                                                    <strong>Medium priority</strong>: 
                                                </p>
                                                <div class="col-sm-4 text-end">
                                                    <span class="badge">
                                                        <?= $backlogPriority['M'] ?? 0?>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="row mx-0 align-items-center">
                                                <p class="card-text col-sm-8 d-flex justify-content-between">
                                                    <strong>Low priority</strong>: 
                                                </p>
                                                <div class="col-sm-4 text-end">
                                                    <span class="badge">
                                                        <?= $backlogPriority['L'] ?? 0?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>  
                </div>
                <div class="col-12 container pt-0">
                    <div class="card">
                        <div class="card-header">
                            <div class="cls-cardHeader">
                                <i class="icon-package cls-packIcon"></i>
                                <h4 class="mb-0 col-sm-8 pointer" onclick="orderByRunningSprint()" 
                                    data-toggle="tooltip" data-placement="top"  title="Click to see the total products" >
                                    Products 
                                    (<?= count($data['products'])?>)
                                </h4>
                            </div>
                         
                            <div class="col-sm-4 px-4 dashboardSearch ">
                                <input 
                                    type="text" class="form-control dashboardSearchInput"  
                                    id="searchInput" placeholder="Search product" 
                                    onkeyup="filterAndReorderProducts()"
                                >
                            </div>
                        </div>
                        <div class="card-body pb-2 pt-0">
                            <ul class="list-group product-items" id="productList">
                                <?php foreach($data['products'] as $value):?>
                                    <li class="list-group-item d-flex product align-items-center" data-name="<?= $value['product_name']?>">
                                        <div class="text-dark col-sm-3 cls-active-products ">
                                            <span class="<?= in_array($value['product_id'],$delayedProductsId)?'cls-delayed-product':''?> ">
                                                <?= trim(ucfirst(strtolower($value['product_name'])))?>
                                            </span> 
                                        </div>
                                        <div class="col-sm-7 cls-active-sprints">
                                            <div class="cls-sprints">
                                            <?php 
                                            if(
                                                ! empty($projectsWithSprints) 
                                                && isset($projectsWithSprints[$value['product_id']])
                                            ):
                                                $count = 1;
                                                foreach($projectsWithSprints[$value['product_id']] as $sprint_id=>$sprintDetails) : 
                                                    if($count <= 5){
                                                        $count += 1;
                                                    }
                                                    else{
                                                        break;
                                                    }

                                            ?>
                                                <form action="<?= base_url()?>sprint/navsprintview" method="get">
                                                    <input type="text" name="sprint_id" value="<?= $sprint_id ?>" hidden>
                                                    <button type="submit" class="badge btn <?= $sprintDetails[1] == 1 ? 'cls-delayed-sprint' : ''?>" 
                                                        data-toggle="tooltip" data-placement="top" name="<?= $sprintDetails[1] == 1 ? 'delayed' : 'onTrack'?>"
                                                        title="Click to open sprint <?= $sprintDetails[0]?>">
                                                        <div>
                                                            <div class="text-center mb-1 dashboardSprint">
                                                                <i class="fas fa-running"></i> 
                                                                Sprint 
                                                                <?= $sprintDetails[0] ?>
                                                            </div>
                                                            <div class="text-center dashboardSprintEndDate">
                                                                End date: <b><?= format_date($sprintDetails[2]) ?></b>
                                                            </div>
                                                        </div>
                                                    </button>
                                                </form>
                                            <?php endforeach; ?>
                                            <?php else:?>
                                                <form action="<?= base_url()?>sprint/sprintlist">
                                                    <button class="badge" name="No Running Sprint" data-toggle="tooltip" data-placement="top" title="Click to open sprintlist">
                                                        No running sprint 
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <form class="text-center" action="<?= ASSERT_PATH ?>dashboard/showProductDashboard/<?= $value['product_id']?>" method="get">
                                                <button class="btn my-2 view" type="submit" data-toggle="tooltip" data-placement="top" title="Click to view <?= trim(ucfirst(strtolower($value['product_name'])))?> dashboard"> 
                                                    <i class="icon-eye view"></i> 
                                                </button>
                                            </form>
                                        </div>
                                    </li>
                                <?php endforeach;?>
                            </ul>
                            <div class="empty-content" id="empty" style="display: none;">
                                <div class="text-center cls-noProduct">
                                <span class="bi bi-emoji-frown cls-noProductIcon"></span>
                                    <h4> No such products found</h4>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>

            
        </div>
</div>

<script>
    const dashboard = "dashboard";
</script>


