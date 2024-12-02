<div class="product-view-container">
    <div class="row d-flex justify-content-end align-items-center">
        <!-- <div class="col-md-auto">
            <h5><span class="h6">No of products: </span></h5>
        </div> -->
        <div class="col-md-auto d-flex align-items-center">
            <div class="search-container me-2">
                <div class="search-box">
                    <input type="text" id="userSearchInput" class="search-input" placeholder="Search products">
                    <button id="search_btn" class="search-button">
                        <i class="bi bi-search"></i>
                        <span>Search</span>
                    </button>
                </div>
            </div>
            <!-- <div class="sort-container">
                <select id="sortCriteria" class="form-select">
                    <option value="">Sort by</option>
                    <option value="backlog_items">Number of backlog items</option>
                    <option value="items_in_sprint">Number of items in sprint</option>
                    <option value="completed_percentage">Completed percentage</option>
                </select>
            </div> -->
        </div>
    </div>
    <div class="row g-3 mt-0">
        <?php
        $cardClass = count($data['product_details']) === 1 ? 'product-card increased-width' : 'product-card';
        ?>
        <?php foreach ($data['product_details'] as $index => $details) : ?>
            <div class="col-md-4 backlog-card">
                        <?php $id = $details['product_id']; ?>
                        <a href="<?= ASSERT_PATH ?>backlog/backlogitems?pid=<?= $id ?>" class="<?= $cardClass ?> active" data-toggle="tooltip" data-placement="top" title="View backlog items of <?= strtoupper($details["product_name"]) ?>">     
                        <div class="card-body-container">
                            <div class="title-product"> 
                                <h4 class="product-title"><?= trim(strtoupper($details["product_name"])) ?></h4>
                            </div>
                            <div class="backlog-content">
                                <div class="row d-flex justify-content-center">
                                    <div class="col-sm-7 text-left">
                                        <p class="product-detail-label">No of pbl items:</p>
                                    </div> 
                                    <div class="col-sm-5 text-left">
                                        <p class="product-detail"><?= $details['number_of_backlog_items'] ?></p>
                                    </div>
                                </div>
                                <div class="row d-flex justify-content-center">
                                    <div class="col-sm-7 text-left">
                                        <p class="product-detail-label">No of user stories:</p>
                                    </div>
                                    <div class="col-sm-5 d-flex align-items-end">
                                        <p class="product-detail"><?= (empty($details['number_of_user_stories']) ? '0' : $details['number_of_user_stories']) ?></p>
                                    </div>
                                </div>
            
                                <div class="row d-flex justify-content-center">
                                    <div class="col-sm-7 text-left">
                                        <p class="product-detail-label">Items onhold:</p>
                                    </div>
                                    <div class="col-sm-5 text-left">
                                        <p class="product-detail"><?= (empty($details['on_hold']) ? '0' : $details['on_hold']) ?></p>
                                    </div>
                                </div>
                                
                                <!-- <div class="row d-flex justify-content-center">
                                    <div class="col-sm-7 text-left">
                                        <p class="product-detail-label">Product owner:</p>
                                    </div>
                                    <div class="col-sm-5 text-left">
                                        <p class="product-detail"></p>
                                    </div>
                                </div> -->
                                <div class="row d-flex justify-content-center">
                                    <div class="col-sm-7 text-left">
                                        <p class="product-detail-label">Last updated:</p>
                                    </div>
                                    <div class="col-sm-5 text-left">
                                        <p class="product-detail">
                                        <?= isset($details['last_updated']) ? date('d, M Y',strtotime($details['last_updated'])):'N/A';?>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            
                            <!-- <div class="cls-pie-graph">

                                <div class="chart-container">
                                    <canvas id="pieChart<?= $index + 1 ?>"></canvas>
                                </div>
                                <div class="legend-container" id="pieChart<?= $index + 1 ?>Legend"></div>
                            </div> -->
                        </div>
                    </a>
                
            </div>
        <?php endforeach; ?>
    </div>

    <div class="empty-content" id="empty" style="display: none;">
        <div class="row mt-5 text-center cls-norecFound">
        <span class="bi bi-emoji-frown cls-noProductIcon"></span>
            <h2>No such products found</h2>
        </div>
    </div>
</div>
<script>
    var productCount = <?= count($data['product_details']);?>
    
    //module name 
    const m_ProductBacklog = [];
</script>