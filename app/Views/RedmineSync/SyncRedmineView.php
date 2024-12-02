<?php
// Assuming you have a function or database query to get the last update times
// Replace these with your actual data retrieval method

$memberLastUpdate = isset($data["usersync"]) ? $data["usersync"] : "first sync";
$userLastUpdate = isset($data["productusersync"]) ? $data["productusersync"] : "first sync";
$productLastUpdate = isset($data["productsync"]) ? $data["productsync"] : "first sync";
$taskLastUpdate = isset($data["tasksync"]) ? $data["tasksync"] : "first sync";
$customerLastUpdate=isset($data["customersync"])?$data["customersync"]: "first sync";
?>
<script>
    const BASE_URL="<?=ASSERT_PATH?>";
</script>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <form id="syncForm">
                <div class="sync-dashboard">
                    <div class="sync-tile">
                        <input type="checkbox" id="userSyncSwitch" name="usersync" value="usersync" class="sync-checkbox">
                        <label for="userSyncSwitch" class="sync-label">
                            <div class="tick-icon"><i class="fas fa-check"></i></div>
                            <div class="sync-icon">
                                <i class="icon-users" aria-hidden="true"></i>
                            </div>
                            <div class="sync-content">
                                <h5>Product users</h5>
                                <p>Product users</p>
                                <div class="sync-status">
                                    <span class="status-dot"></span>
                                    <span class="status-text">Not synced</span>
                                </div>
                                <div class="last-updated">Last updated: 
                                    <span class="update-time" id="userSyncLastUpdate">
                                        <?php echo isset($userLastUpdate) ? date("d, M Y h:i A", strtotime($userLastUpdate)) : 'N/A';  ?>
                                    </span>
                                </div>
                            </div>
                        </label>
                    </div>
                    <div class="sync-tile">
                        <input type="checkbox" id="productSyncSwitch" name="productsync" class="sync-checkbox">
                        <label for="productSyncSwitch" class="sync-label">
                            <div class="tick-icon"><i class="fas fa-check"></i></div>
                            <div class="sync-icon">
                                <i class="icon-package"></i>
                            </div>
                            <div class="sync-content">
                                <h5>Products</h5>
                                <p>Update products</p>
                                <div class="sync-status">
                                    <span class="status-dot"></span>
                                    <span class="status-text">Not synced</span>
                                </div>
                                <div class="last-updated">
                                    Last updated: <span class="update-time" id="productSyncLastUpdate">
                                        <?php echo isset($productLastUpdate) ? date("d, M Y h:i A", strtotime($productLastUpdate)) : 'N/A'; ?></span>
                                </div>
                            </div>
                        </label>
                    </div>
                    <div class="sync-tile">
                        <input type="checkbox" id="taskSyncSwitch" name="tasksync" value="tasksync" class="sync-checkbox">
                        <label for="taskSyncSwitch" class="sync-label">
                            <div class="tick-icon"><i class="fas fa-check"></i></div>
                            <div class="sync-icon">
                                <i class="icon-list"></i>
                            </div>
                            <div class="sync-content">
                                <h5>Tasks</h5>
                                <p>Sync to-do lists</p>
                                <div class="sync-status">
                                    <span class="status-dot"></span>
                                    <span class="status-text">Not synced</span>
                                </div>
                                <div class="last-updated">
                                    Last updated: <span class="update-time" id="taskSyncLastUpdate">
                                        <?php echo isset($taskLastUpdate) ? date("d, M Y h:i A", strtotime($taskLastUpdate)) : 'N/A'; ?>
                                    </span>
                                </div>
                            </div>
                        </label>
                    </div>
                    <div class="sync-tile">
                        <input type="checkbox" id="memberSyncSwitch" name="membersync" value="membersync" class="sync-checkbox">
                        <label for="memberSyncSwitch" class="sync-label">
                            <div class="tick-icon"><i class="icon-user-check"></i></div>
                            <div class="sync-icon">
                                 <i class="icon-user-check"></i>
                            </div>
                            <div class="sync-content">
                                <h5>Users</h5>
                                <p>Sync users</p>
                                <div class="sync-status">
                                    <span class="status-dot"></span>
                                    <span class="status-text">Not synced</span>
                                </div>
                                <div class="last-updated">
                                    Last updated: <span class="update-time" id="userSyncLastUpdate">
                                        <?php echo isset($memberLastUpdate) ? date("d, M Y h:i A", strtotime($memberLastUpdate)) : 'N/A'; ?></span>
                                </div>
                            </div>
                        </label>
                    </div>
                    <div class="sync-tile">
                        <input type="checkbox" id="customerSyncSwitch" name="customersync" value="customersync" class="sync-checkbox">
                        <label for="customerSyncSwitch" class="sync-label">
                            <div class="tick-icon"><i class="icon-user-check"></i></div>
                            <div class="sync-icon">
                                 <i class="icon-user-check"></i>
                            </div>
                            <div class="sync-content">
                                <h5>Customers</h5>
                                <p>Sync customers</p>
                                <div class="sync-status">
                                    <span class="status-dot"></span>
                                    <span class="status-text">Not synced</span>
                                </div>
                                <div class="last-updated">
                                    Last updated: <span class="update-time" id="customerSyncLastUpdate">
                                        <?php echo isset($customerLastUpdate) ? date("d, M Y h:i A", strtotime($customerLastUpdate)) : 'N/A'; ?></span>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div>
                        <button type="button" class="button-secondary me-2" id="resetBtn">
                            Reset
                        </button>
                        <button type="button" class="btn primary_button" id="selectAllBtn">
                            Select all
                        </button>
                    </div>
                    <button type="submit" class="btn primary_button">
                         Sync 
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- loading screen  -->
<div id="loading-screen" style="display: none;">
    <div class="loading-content">
        <h2 class='sync'>Syncing...</h2>
        <div class="progress">
            <div id="progress-bar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
        <p id="progress-text">0%</p>
    </div>
</div>

<script>
    const syncRedmineView = "syncRedmineView ";
</script>