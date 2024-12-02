if (typeof manageUser !== 'undefined') {

    let sortColumn = "first_name";
    let sortOrder = 1; // 1 for ascending, -1 for descending
    let currentPage = 1;
    let rowsPerPage = 10; // Adjust as needed

    function generateTable() {
        generateTableHeader();
        generateTableBody(data);
    }

    function generateTableHeader() {
        const tableHeader = document.getElementById("tableHeader");
        tableHeader.innerHTML = "";
        const headerRow = document.createElement("tr");
        const columns = [{
            name: "first_name",
            label: "First name"
        },
        {
            name: "last_name",
            label: "Last name"
        },
        {
            name: "email_id",
            label: "Email"
        },
        {
            name: "role_name",
            label: "Role"
        },
        {
            name: "action",
            label: "Action"
        },
        ];

        columns.forEach((column) => {
            const th = document.createElement("th");
            th.textContent = column.label;
            th.setAttribute("class", "text-center");
            if (column.name !== "action") {
                th.classList.add("sorting");
                th.addEventListener("click", () => sortTable(column.name));
            }
            headerRow.appendChild(th);
        });

        tableHeader.appendChild(headerRow);
    }

    function generateTableBody(data) {
        const tableBody = document.getElementById("tableBody");
        tableBody.innerHTML = "";

        // Sort data
        data.sort((a, b) => {
            if (a[sortColumn] < b[sortColumn]) return -sortOrder;
            if (a[sortColumn] > b[sortColumn]) return sortOrder;
            return 0;
        });

        // Paginate data
        const startIndex = (currentPage - 1) * rowsPerPage;
        const endIndex = startIndex + rowsPerPage;
        const paginatedData = data.slice(startIndex, endIndex);

        paginatedData.forEach((item) => {
            const row = document.createElement("tr");

            row.innerHTML = `
            <td class="text-center">${item.first_name}</td>
            <td class="text-center">${item.last_name}</td>
            <td class="text-center">${item.email_id}</td>
            <td class="text-center">${item.role_name}</td>
            <td>
                <div class='btn-group' role='group' aria-label='Button group with nested dropdown'>
                    <button type='button' class='cls-pencil' data-bs-toggle='modal' data-bs-target='#userModal' onclick='editModal(${JSON.stringify(item)})'><i class='icon-edit'></i></button>
                </div>
            </td>
        `;

            tableBody.appendChild(row);
        });
    }

    // function sortTable(columnName) {
    //     sortColumn = columnName;
    //     sortOrder = sortOrder === 1 ? -1 : 1; // Toggle sort order between ascending and descending
    //     generateTableBody(data); // Regenerate the table body with sorted data
    // }

    function viewModal(id) {
        console.log(id);
        document.getElementById('userModalLabel').textContent = "User Details";
        document.getElementById('firstName').value = id.first_name;
        document.getElementById('lastName').value = id.last_name;
        document.getElementById('emailId').value = id.email_id;
        document.getElementById('role').style.display = "block";
        document.getElementById('role').value = id.role_name;
        document.getElementById('roleDisplay').style.display = 'block';
        document.getElementById('roleSelect').style.display = 'none';
        document.getElementById('editUserButton').style.display = 'block';
        document.getElementById('saveUserButton').style.display = 'none';
    }

    function editModal(id) {
        document.getElementById('firstName').value = id.first_name;
        document.getElementById('lastName').value = id.last_name;
        document.getElementById('emailId').value = id.email_id;
        document.getElementById('selectUser').value = id.role_id;
        document.getElementById('userId').value = id.user_id;
        toggleEditMode();
    }

    function toggleEditMode() {
        document.getElementById('userModalLabel').textContent = "Edit user details";
        document.getElementById('role').style.display = "none";
        document.getElementById('roleDisplay').style.display = 'none';
        document.getElementById('roleSelect').style.display = 'block';
        document.getElementById('editUserButton').style.display = 'none';
        document.getElementById('saveUserButton').style.display = 'block';
    }

    $(document).ready(function () {
        $('#userTableForm').on('submit', function (e) {
            e.preventDefault();

            let formData = $(this).serialize();
            formData += '&SaveUserbutton=1';
            let id = $('#userId').val();
            let url = baseUrl + 'admin/updaterole';

            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                dataType: 'json',
                success: function (response) {
                    $("#userTableForm").modal("hide");
                    Swal.fire({
                        title: 'Success',
                        text: 'User role updated successfully',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = window.location.href;
                        }
                    });
                },
                error: function () {
                    // Handle AJAX error
                    Swal.fire({
                        title: 'Error',
                        text: 'An error occurred while updating the role',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });
    });

    // Function to update pagination controls
    function updatePaginationControls(data) {
        const totalPages = Math.ceil(data.length / rowsPerPage);
        document.getElementById("pageInfo").textContent = `Page ${currentPage} of ${totalPages}`;

        // Show or hide pagination buttons based on the current page
        document.getElementById("prevPage").style.visibility = currentPage > 1 ? "visible" : "hidden";
        document.getElementById("nextPage").style.visibility = currentPage < totalPages ? "visible" : "hidden";
    }

    // Event listeners for pagination controls
    document.getElementById("prevPage").addEventListener("click", () => {
        if (currentPage > 1) {
            currentPage--;
            generateTableHeader(data);
            generateTableBody(data);
            updatePaginationControls(data); // Regenerate table body with the updated page
        }
    });

    document.getElementById("nextPage").addEventListener("click", () => {
        const totalPages = Math.ceil(data.length / rowsPerPage);

        if (currentPage < totalPages) {
            currentPage++;
            generateTableHeader(data);
            generateTableBody(data);
            updatePaginationControls(data); // Regenerate table body with the updated page
        }
    });


    // Call this function to generate the table when the page loads or data is updated
    generateTable();
    updatePaginationControls(data);

    if (data.length === 0) {
        document.getElementById("mainPage").style.display = "none";
        document.getElementById("empty").style.display = "block";
        document.getElementById("paginationControls").style.display = "none";
    } else {
        document.getElementById("empty").style.display = "none";
        document.getElementById("mainPage").style.display = "block";

        if (data.length <= 10) {
            document.getElementById("paginationControls").style.display = "none";
        } else {
            document.getElementById("paginationControls").style.display = "block";
        }
    }

    // Function to filter the table data based on the search query
    function filterTable() {
        let searchQuery = $("#userSearchInput").val().toLowerCase();

        // Perform an AJAX request to fetch filtered data
        $.ajax({
            url: baseUrl + "admin/searchrole",
            type: "POST",
            data: JSON.stringify({
                searchQuery: searchQuery
            }),
            contentType: "application/json",
            dataType: "json",
            success: function (response) {
                if (response.data.length) {
                    currentPage = 1;
                    data = response.data
                    generateTableHeader(data);
                    generateTableBody(data);
                    updatePaginationControls(data);
                } else {
                    currentPage = 0;
                    data = response.data
                    generateTableHeader(data);
                    updatePaginationControls(data);
                    console.error("Invalid response format");
                    $("#tableBody").html("<tr><td colspan='100%' class='text-center'>No Data Found</td></tr>");
                }
            },
            error: function (xhr, status, error) {
                console.error("AJAX Error:", status, error);
                console.error("Response Text:", xhr.responseText);
            }
        });
    }

    $(document).ready(function () {
        $("#userSearchInput").on("input", function () {
            filterTable();
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        const searchBox = document.querySelector('.search-box');
        const searchBtn = document.getElementById('search_btn');
        const searchInput = document.getElementById('userSearchInput');

        searchBtn.addEventListener('click', function (e) {
            e.preventDefault();
            searchBox.classList.toggle('expanded');
            if (searchBox.classList.contains('expanded')) {
                searchInput.focus();
            }
        });

        // Close search box when clicking outside
        document.addEventListener('click', function (e) {
            if (!searchBox.contains(e.target)) {
                searchBox.classList.remove('expanded');
            }
        });
    });

}