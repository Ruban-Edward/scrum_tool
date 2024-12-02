<!--
    manageUser.php

    @category   View
    @purpose    View page to view users and add and update users
    @created    9 July 2024
    @updated
    @autor      Ruban Edward
-->

<?php
// Retrieve the last synchronization datetime
$lastSync = $data['last_sync'];
$time = isset($lastSync[0]['sync_datetime']) ? $lastSync[0]['sync_datetime'] : "First Sync";

// Format the datetime for display
if ($time === "First Sync") {
    $formattedDate = $time;
} else {
    $dateTime = new DateTime($time);
    $formattedDate = $dateTime->format('d, M Y h:i A');
}
?>

<!-- Main Content Section -->
<div class="main-page" id="mainPage">
    <div class="page">
        <div class="line mb-2"></div>
        <div class="row">
            <div class="col-sm-6">
                <div class="cls-sync">
                    <h6>Last user sync since : <?php echo $formattedDate ?></h6>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="search-container">
                    <div class="search-box">
                        <!-- Input for user search -->
                        <input type="text" id="userSearchInput" class="search-input" placeholder="Search User">
                        <button id="search_btn" class="search-button">
                            <i class="icon-search"></i>
                            <span>Search</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <table id="priority-table" class="table table-borderless custom-table cls-tabler">
                <thead id="tableHeader" class="header_color">
                    <!-- Table headers will be inserted here -->
                </thead>
                <tbody id="tableBody" class="body_color">
                    <!-- Table rows will be inserted here -->
                </tbody>
            </table>
            <!-- Pagination Controls -->
            <div id="paginationControls" class="d-flex justify-content-between align-items-center mt-3">
                <button id="prevPage" class="button btn-primary">Prev</button>
                <span id="pageInfo" class="mx-3"></span>
                <button id="nextPage" class="button btn-primary">Next</button>
            </div>
        </div>
    </div>
</div>

<!-- Empty Content Section -->
<div class="empty-content" id="empty">
    <div class="row mt-5 text-center">
        <h2>No user found</h2>
    </div>
</div>

<!-- Modal for User Details -->
<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">

            <div class="modal-header">
                <h1 class="modal-title fs-5" id="userModalLabel">User detail</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="userTableForm">
                    <input type="hidden" id="userId" name="userId" value="">
                    <div class="mb-1 row">
                        <label for="firstName" class="col-sm-3 col-form-label">First name</label>
                        <div class="col-sm-9">
                            <input type="text" readonly class="form-control-plaintext" id="firstName">
                        </div>
                    </div>
                    <div class="mb-1 row">
                        <label for="lastName" class="col-sm-3 col-form-label">Last name</label>
                        <div class="col-sm-9">
                            <input type="text" readonly class="form-control-plaintext" id="lastName">
                        </div>
                    </div>
                    <div class="mb-1 row">
                        <label for="emailId" class="col-sm-3 col-form-label">Email id</label>
                        <div class="col-sm-9">
                            <input type="text" readonly class="form-control-plaintext" id="emailId">
                        </div>
                    </div>
                    <div class="mb-1 row">
                        <label for="role" class="col-sm-3 col-form-label">Role</label>
                        <div class="col-sm-9">
                            <div id="roleDisplay">
                                <input type="text" readonly class="form-control-plaintext" id="role">
                            </div>
                            <div id="roleSelect" style="display: none;">
                                <select class="form-select manageUserDropdown" id="selectUser" name="selectUser"  required>
                                    <option value="" disabled selected>-- Select role --</option>
                                    <?php foreach ($data['roles'] as $value) {
                                        echo "<option value='{$value['role_id']}'>{$value['role_name']}</option>";
                                    } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <!-- Buttons for editing and saving user details -->
                        <button type="button" class="button btn-edit" id="editUserButton" onclick="toggleEditMode()">Edit user</button>
                        <button type="submit" class="button btn-primary" id="saveUserButton">Save user</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Base URL for API calls or redirects
    var baseUrl = '<?= base_url() ?>';
    // JavaScript object containing user data
    let data = <?php echo json_encode($data['showUser']); ?>;

    //module name 
    const manageUser = "manageUser";
</script>