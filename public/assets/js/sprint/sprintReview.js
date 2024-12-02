 if (typeof m_sprintReview !== 'undefined') {
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('sprintReviewForm');
    const dateInput = document.getElementById('date-picker');
    const successMessage = document.getElementById('successMessage');
    const challengeYes = document.getElementById('challengeYes');
    const challengeNo = document.getElementById('challengeNo');
    const challengeDetails = document.getElementById('challengeDetails');
    const codeReviewYes = document.getElementById('codeReviewYes');
    const codeReviewNo = document.getElementById('codeReviewNo');
    const codeReviewReason = document.getElementById('codeReviewReason');
    const memberVisibilityDiv = document.querySelector('.membervisibility');
    const goalsMetNo = document.getElementById('goalsMetNo');
    const goalsNotMetReason = document.getElementById('goalsNotMetReason');

    // Set current date
    const today = new Date();
    dateInput.valueAsDate = today;

    // // Fetch sprint data from database (simulated)
    // fetchSprintData().then(data => {
    //     document.getElementById('sprintName').textContent = data.name;
    //     document.getElementById('sprintVersion').textContent = data.version;
    // });

    // Submit User Stories
    $('#userStoriesForm').submit(function (e) {
        e.preventDefault();
        //const selectedStories = $('#userStories').val();
        // console.log(selectedStories);
        // let formData = {checkedTasks};
        let intArray = checkedTasks.map(Number);
        let submittedtask = intArray.filter(value => !Number.isNaN(value));
        let status = document.querySelector('input[name="status"]:checked').value;
        let formData = { "submittedTasks": submittedtask, "status": status };
        console.log(formData);
        $.ajax({
            url: path + 'sprint/submitUserStories',
            type: 'POST',
            dataType: 'json',
            data: formData,
            success: function (response) {
                console.log(response);
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Status has been updated',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = path + "/sprint/navsprintview?sprint_id=" + sprintId;
                    }
                });
            },
            error: function (xhr, status, error) {
                console.log(error);
            }
        });
    });

    console.log(backlogData);

    // Submit Sprint Review
    $('#sprintReviewForm').submit(function (e) {
        e.preventDefault();
        let reviewData = $(this).serialize();

        console.log(reviewData);

        $.ajax({
            url: path + 'sprint/submitSprintReview?sprint_id=' + sprintId,
            type: 'POST',
            dataType: 'json',
            data: reviewData,
            success: function (response) {
                console.log(response);
                if(response.success){
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Review has been updated',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = path + "/sprint/navsprintview?sprint_id=" + sprintId + "#navreview";
                        }
                    });
                }else{
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning!',
                        text: 'Review has not been updated',
                        confirmButtonText: 'OK'
                    })
                }           
            },
            error: function (xhr, status, error, response) {
                console.log(response);
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning!',
                    text: 'Review has not been updated',
                    confirmButtonText: 'OK'
                })
            }
        });
    });

    challengeYes.addEventListener('change', function () {
        challengeDetails.style.display = this.checked ? 'block' : 'none';
        document.getElementById('challenges').value = "";
    });

    challengeNo.addEventListener('change', function () {
        challengeDetails.style.display = 'none';
        document.getElementById('challenges').value = "No challenges faced";
    });

    function toggleMemberVisibility() {
        if (codeReviewYes.checked) {
            memberVisibilityDiv.style.display = 'block';
            codeReviewReason.style.display = 'none';
            document.getElementById('codeReviewReasonText').value = "Code review done";
        } else {
            memberVisibilityDiv.style.display = 'none';
            codeReviewReason.style.display = 'block';
            document.getElementById('codeReviewReasonText').value = "";
        }
    }

    codeReviewYes.addEventListener('change', toggleMemberVisibility);
    codeReviewNo.addEventListener('change', toggleMemberVisibility);

    goalsMetYes.addEventListener('change', function () {
        goalsNotMetReason.style.display = 'none';
        document.getElementById('goalsNotMetReasonText').value = "Sprint goal met";
    });

    goalsMetNo.addEventListener('change', function () {
        goalsNotMetReason.style.display = 'block';
        document.getElementById('goalsNotMetReasonText').value = "";
    });

});

var itemsPerPage = 1; // Number of items per page
var currentPage = 1; // Current page
var selectedStatus = 'all';
var checkedTasks = [];
var checkedArray = []; // Array to store checked task IDs
var totalPages = 0;
// var checkAllItems = document.createElement('th');
// checkAllItems.setAttribute('id', 'all-check');
// document.getElementById('table-heading').appendChild(checkAllItems);

// Function to generate the table based on the current page
function getsprintTask()
{
    $.ajax({
        url: path + 'sprint/navsprintreview', // Adjust URL to match your setup
        method: 'POST',
        data: { sprint_id: sprintId },
        dataType: 'json',
        success: function (response) {
            // Assuming 'response' is in the correct format for backlogData and userData
            backlogData = response.data; // Adjust this as per your data structure
            // sprintRetrospective = response.sprintRetrospective; // Adjust this as per your data structure
            //console.log(backlogData);
            // Initialize functions after data retrieval
            console.log('baclogData After', backlogData);
            generateTable(currentPage);

        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.error('Error fetching data:', textStatus, errorThrown);
        }
    });
}
function applyStatusFilter(event) {
    selectedStatus = event.target.value;
    console.log(selectedStatus);
    console.log('baclogData Before', backlogData);
   getsprintTask();
   currentPage=1;

}

function generateTable(page) {
    document.getElementById('backlogTable').style.display = 'block'
    document.getElementById('myNav').style.display = 'flex'
    document.getElementById('errormsg').style.display = 'none';
    var BacklogArray = `BacklogData${currentPage}`;
    var tableBody = document.getElementById('table-body');
    var pagination = document.getElementById('pagination');
    var tableContent = '';
    // document.getElementById('all-check').innerHTML = "";

    var startIndex = (page - 1) * itemsPerPage;
    var endIndex = startIndex + itemsPerPage;
    console.log(backlogData);
    var filteredBacklogData = filterBacklogDataByStatus(backlogData, selectedStatus);
    if (filteredBacklogData.length === 0 && selectedStatus != 'all') {
        errormsg();
    }
    var backlogSlice = filteredBacklogData.slice(startIndex, endIndex);
    backlogSlice.forEach(function (backlogItem, backlogIndex) {
        var backlogName = backlogItem.backlog;
        var backlogRowSpan = countBacklogRows(backlogItem);
        var completedTasks = countCompletedTasks(backlogItem);
        var totalTasks = countTotalTasks(backlogItem);
        var completionPercentage = totalTasks > 0 ? ((completedTasks / totalTasks) * 100) : 0;
        backlogItem.epics.forEach(function (epicItem, epicIndex) {
            var epicName = epicItem.epic;
            var epicRowSpan = countEpicRows(epicItem);

            epicItem.userStories.forEach(function (userStoryItem, userStoryIndex) {
                var userStoryName = userStoryItem.userStory;
                var userStoryId=userStoryItem.userStoryId;
                var userStoryRowSpan = countUserStoryRows(userStoryItem);

                userStoryItem.tasks.forEach(function (taskItem, taskIndex) {
                    var taskName = taskItem.task;
                    var status = taskItem.status;
                    var taskId = taskItem.taskId;
                    // Assuming taskId is unique for each task

                    // Determine if the checkbox should be checked
                    var isChecked = checkedTasks.includes(taskId);
                    var isBacklogChecked = checkedTasks.includes(BacklogArray);

                    // Construct table row HTML
                    if (taskIndex === 0) {
                        tableContent += '<tr>';
                        if (epicIndex === 0 && userStoryIndex === 0) {
                            document.getElementById('backlogName').innerHTML = '<h6>Backlog Name:</h6><span>' + backlogName + '</span> </h5>';
                        }
                        if (userStoryIndex === 0) {
                            tableContent += '<td rowspan="' + epicRowSpan + '" class="epic-cell">' + epicName + '</td>';
                        }
                        tableContent += '<td rowspan="' + userStoryRowSpan + '" class="user-story">US_' + userStoryId + '</td>';
                        tableContent += '<td rowspan="' + userStoryRowSpan + '" class="user-story-cell">' + userStoryName + '</td>';
                        tableContent += '<td class="task-cell">' + taskName + '</td>';
                        tableContent += `<td class='selectTask'><input type='hidden' class='taskid' value='${taskId}'><select class='selectTaskSelector form-select'>
                         <option class="status dis"><span>${status}</span></option>
                         <option value="17" >Move to Prelive</option>
                         <option value="18" >Assign for UAT</option>
                         <option value="19">Assign for Testing</option>
                         <option value="20">Move to Live</option>
                         <option value="16">OnHold</option>
                     </select></td>`;

                        // tableContent += '<td class="checkbox-td"><input type="checkbox" class="checkbox' + currentPage + '"  name="submittedTasks[]" value="' + taskId + '" ' + (isChecked ? 'checked' : '') + '></td>';
                        tableContent += '</tr>';

                        //document.getElementById('all-check').innerHTML = '<input type="checkbox" id="BacklogData' + currentPage + '"  value="BacklogData' + currentPage + '" ' + (isBacklogChecked ? 'checked' : '') + '>';
                    } else {
                        tableContent += '<tr>';
                        tableContent += '<td class="task-cell">' + taskName + '</td>';
                        tableContent += `<td class='selectTask'><input type='hidden' value='${taskId}'><select class='selectTaskSelector form-select'>
                         <option class="status"><span>${status}</span></option>
                         <option value="17">Move to Prelive</option>
                         <option value="18">Assign for UAT</option>
                         <option value="19">Assign for Testing</option>
                         <option value="20">Move to Live</option>
                         <option value="16">On Hold</option>
                     </select></td>`;
                        //tableContent += '<td class="checkbox-td"><input type="checkbox" class="checkbox' + currentPage + '" name="submittedTasks[]" value="' + taskId + '" ' + (isChecked ? 'checked' : '') + '></td>';
                        tableContent += '</tr>';
                        //document.getElementById('all-check').innerHTML = '<input type="checkbox" id="BacklogData' + currentPage + '"  value="BacklogData' + currentPage + '" ' + (isChecked ? 'checked' : '') + '>';

                    }
                });
            });
        });
    });


    // Update the table body with generated content
    tableBody.innerHTML = tableContent;
    // Select all elements with the class 'selectTaskSelector'
    const selectedComponents = document.querySelectorAll('.selectTaskSelector');

    // Iterate over each selected component
    selectedComponents.forEach(select => {
        // Find all <option> elements within the current <select> element
        const options = select.querySelectorAll('option');
        // Check if there are options in the select element
        if (options.length > 0) {
            // Get the first option
            const firstOption = options[0];
            // Get the value or text of the first option
            const firstOptionValue = firstOption.value;
            const firstOptionText = firstOption.textContent.trim();
            // Iterate over all <option> elements
            options.forEach(option => {
                // Check if the current option matches the first option
                if (option.value === firstOptionValue || option.textContent.trim() === firstOptionText) {
                    // Log or perform actions on the matching options
                    option.disabled = true;
                }
            });
        }
    });
    document.querySelectorAll('.selectTask').forEach(user => {

        user.addEventListener('change', (event) => {
            const hiddenInput = event.target.closest('.selectTask').querySelector('input[type="hidden"]').value;
            const taskStatus = event.target.closest('.selectTask').querySelector('select').value
            const selectElement = event.target.closest('.selectTask').querySelector('select');
            const selectedOption = selectElement.options[selectElement.selectedIndex];
            const selectedText = selectedOption.innerText;
            console.log(taskStatus);
            console.log(hiddenInput);
            event.target.closest('.selectTask').querySelector('select').querySelectorAll('option').forEach(optional => {
                if (optional.innerText === selectedText) {
                    optional.disabled = true;
                }
                else {
                    optional.disabled = false;
                }
            })
            $.ajax({
                url: path + 'sprint/fetchTasks',
                type: 'POST',
                dataType: 'json',
                data: { 'id': hiddenInput, 'taskStatus': taskStatus },
                success: function (response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: "Task status has been updated",
                        showConfirmButton: false
                    });
                    setTimeout(function () {
                        Swal.close();
                    }, 1500);

                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.error(jqXHR);
                    console.error('Error saving sprint plan details:', textStatus, errorThrown);
                    Swal.fire({
                        icon: 'warning',
                        title: 'Error!',
                        text: 'Your sprint plan has not been updated',
                        showConfirmButton: false
                    });
                    setTimeout(function () {
                        Swal.close();
                    }, 2000);
                }
            });
        })
    })


    // Attach event listener to checkboxes to capture checked values
    var checkboxes = document.querySelectorAll('input[type="checkbox"]');
    checkboxes.forEach(function (checkbox) {
        checkbox.addEventListener('change', function () {
            if (this.checked) {
                // Add checked task ID to the array if it doesn't already exist
                if (!checkedTasks.includes(this.value)) {
                    checkedTasks.push(this.value);
                }
            } else {
                // Remove unchecked task ID from the array
                var index = checkedTasks.indexOf(this.value);
                if (index !== -1) {
                    checkedTasks.splice(index, 1);
                }
            }

            console.log('Checked Tasks:', checkedTasks); // Log the array for verification

            // Update 'Select All' checkbox based on individual checkboxes' state
            updateSelectAllCheckbox();
        });
    });

    // Generate pagination links (assuming this part was already implemented)
    totalPages = Math.ceil(filteredBacklogData.length / itemsPerPage);


    // for (var i = 1; i <= totalPages; i++) {
    //     paginationContent += '<li class="page-item' + (currentPage === i ? ' active' : '') + '"><a class="page-link"  onclick="changePage(' + i + ')">' + i + '</a></li>';
    // }

    var checkAllCheckbox = document.getElementById(`BacklogData${currentPage}`);

    // Select all data checkboxes
    var dataCheckboxes = document.querySelectorAll(`.checkbox${currentPage}`);

    // Add click event listener to the 'Select All' checkbox
    // checkAllCheckbox.addEventListener('click', function () {
    //     // Loop through each data checkbox
    //     dataCheckboxes.forEach(function (checkbox) {
    //         // Set the state of each data checkbox to be the same as 'Select All' checkbox
    //         checkbox.checked = checkAllCheckbox.checked;

    //         // Update checkedTasks based on checkbox state
    //         if (checkbox.checked && !checkedTasks.includes(checkbox.value)) {
    //             checkedTasks.push(checkbox.value);
    //         } else if (!checkbox.checked) {
    //             var index = checkedTasks.indexOf(checkbox.value);
    //             if (index !== -1) {
    //                 checkedTasks.splice(index, 1);
    //             }
    //         }
    //     });

    //     console.log('Checked Tasks:', checkedTasks); // Log the array for verification
    // });

    // Update 'Select All' checkbox state based on current checkboxes' state
    // updateSelectAllCheckbox();
    if (currentPage === 1) {
        console.log('hide prev')
        document.getElementById('prevbutton').style.opacity = "0.2";
        document.getElementById('prevbutton').style.disabled = true;
        document.getElementById('nextbutton').style.opacity = "0.9";
        document.getElementById('nextbutton').style.disabled = false;

    }
    if (currentPage == 1 && totalPages == 1) {
        document.getElementById('prevbutton').style.opacity = "0.2";
        document.getElementById('prevbutton').style.disabled = true;
        document.getElementById('nextbutton').style.opacity = "0.2";
        document.getElementById('nextbutton').style.disabled = true;
    }
}

document.getElementById('nextbutton').addEventListener('click', () => {
    if (currentPage === totalPages) {
        return 0;
    }
    else {
        currentPage++;
        if (currentPage === totalPages) {
            console.log('hide next')
            document.getElementById('nextbutton').style.opacity = "0.2";
            document.getElementById('nextbutton').style.disabled = true;
            document.getElementById('prevbutton').style.opacity = "0.9";
            document.getElementById('prevbutton').style.disabled = false;

        }
        else {
            console.log('show -prev')
            document.getElementById('nextbutton').style.opacity = "0.9";
            document.getElementById('prevbutton').style.opacity = "0.9";
        }
        console.log('next', currentPage);
        getsprintTask();
    }


})
function errormsg() {
    document.getElementById('backlogTable').style.display = 'none'
    document.getElementById('myNav').style.display = 'none'
    document.getElementById('errormsg').style.display = 'block';

}
if (currentPage === 1) {
    console.log('hide prev')
    document.getElementById('prevbutton').style.opacity = "0.2";
    document.getElementById('prevbutton').style.disabled = true;
    document.getElementById('nextbutton').style.opacity = "0.9";
    document.getElementById('nextbutton').style.disabled = false;

}
document.getElementById('prevbutton').addEventListener('click', () => {

    if (currentPage === 1) {
        return 0;
    }
    else {
        currentPage--
        if (currentPage === 1) {
            console.log('hide prev')
            document.getElementById('prevbutton').style.opacity = "0.2";
            document.getElementById('prevbutton').style.disabled = true;
            document.getElementById('nextbutton').style.opacity = "0.9";
            document.getElementById('nextbutton').style.disabled = false;

        }
        else {
            console.log('show -next')
            document.getElementById('nextbutton').style.opacity = "0.9";
            document.getElementById('prevbutton').style.opacity = "0.9";
        }
        console.log('prev', currentPage);
        getsprintTask();
    }
})
function countCompletedTasks(backlogItem) {
    var completedTasks = 0;
    backlogItem.epics.forEach(function (epicItem) {
        epicItem.userStories.forEach(function (userStoryItem) {
            userStoryItem.tasks.forEach(function (taskItem) {
                if (taskItem.status === 'Completed') {
                    completedTasks++;
                }
            });
        });
    });
    return completedTasks;
}
function countTotalTasks(backlogItem) {
    var totalTasks = 0;
    backlogItem.epics.forEach(function (epicItem) {
        epicItem.userStories.forEach(function (userStoryItem) {
            totalTasks += userStoryItem.tasks.length;
        });
    });
    return totalTasks;
}

// Example function calls (you would call this wherever appropriate in your application flow)
if (backlogData != "") {
    generateTable(currentPage);
}
else {
    errormsg();
} // Initial generation of table

// Function to change the current page and regenerate the table



function countTotalTasks(backlogItem) {
    var totalTasks = 0;
    backlogItem.epics.forEach(function (epicItem) {
        epicItem.userStories.forEach(function (userStoryItem) {
            totalTasks += userStoryItem.tasks.length;
        });
    });
    return totalTasks;
}
// document.addEventListener('DOMContentLoaded', function () {
//     const tableStriped = document.querySelector('.formElements');
//     const nav = document.getElementById('myNav');

//     // Add mouseover event listener
//     tableStriped.addEventListener('mouseover', function () {
//         nav.style.display = 'block'; // Show nav
//     });

//     // Add mouseout event listener
//     tableStriped.addEventListener('mouseout', function () {
//         nav.style.display = 'none'; // Hide nav
//     });
// });

function countCompletedTasks(backlogItem) {
    var completedTasks = 0;
    backlogItem.epics.forEach(function (epicItem) {
        epicItem.userStories.forEach(function (userStoryItem) {
            userStoryItem.tasks.forEach(function (taskItem) {
                if (taskItem.status === 'Completed') {
                    completedTasks++;
                }
            });
        });
    });
    return completedTasks;
}

function totalCompleteTasks(userData) {
    var completedTasks = 0;
    var totalTasks = 0;
    userData.forEach(backlog => {
        backlog.epics.forEach(epic => {
            epic.userStories.forEach(userStories => {
                userStories.tasks.forEach(tasks => {
                    totalTasks += 1;
                    if (tasks.status === 'completed' || tasks.status === 'Completed') {
                        completedTasks += 1;
                    }
                });
            });
        });
    });
    document.getElementById('total-tasks').innerHTML = totalTasks;
    document.getElementById('task-no').innerHTML = completedTasks;
}
// generateTable(currentPage);
function changePage(page) {
    currentPage = page;
    generateTable(currentPage);
}
function getStatusClass(status) {
    switch (status) {
        case 'In Progress':
            return 'status-in-progress';
        case 'to review':
            return 'status-to-do';
        case 'Completed':
            return 'status-done';
        default:
            return '';
    }
}
function countBacklogRows(backlogItem) {
    var rowCount = 0;
    backlogItem.epics.forEach(function (epicItem) {
        epicItem.userStories.forEach(function (userStoryItem) {
            rowCount += userStoryItem.tasks.length;
        });
    });
    return rowCount;
}

function countEpicRows(epicItem) {
    var rowCount = 0;
    epicItem.userStories.forEach(function (userStoryItem) {
        rowCount += userStoryItem.tasks.length;
    });
    return rowCount;
}

function countUserStoryRows(userStoryItem) {
    return userStoryItem.tasks.length;
}
function filterBacklogDataByStatus(data, status) {
    if (status === 'all') {
        return data;
    }

    return data.map(backlogItem => {
        var filteredEpics = backlogItem.epics.map(epicItem => {
            var filteredUserStories = epicItem.userStories.map(userStoryItem => {
                var filteredTasks = userStoryItem.tasks.filter(taskItem => taskItem.status === status);
                return {
                    ...userStoryItem,
                    tasks: filteredTasks
                };
            }).filter(userStoryItem => userStoryItem.tasks.length > 0);

            return {
                ...epicItem,
                userStories: filteredUserStories
            };
        }).filter(epicItem => epicItem.userStories.length > 0);

        return {
            ...backlogItem,
            epics: filteredEpics
        };
    }).filter(backlogItem => backlogItem.epics.length > 0);

}

let presentPage = 1;
const countPerPage = 7;
let filteredMembers = [];
let checkedMembers = new Set();

function displayMembers(page, membersToUse = members) {
    const memberTableBody = document.getElementById('memberTableBody');
    memberTableBody.innerHTML = ''; // Clear existing rows

    const membersArray = Array.isArray(membersToUse) ? membersToUse : Object.values(membersToUse);
    const start = (page - 1) * countPerPage;
    const end = start + countPerPage;
    const membersToDisplay = membersArray.slice(start, end);

    membersToDisplay.forEach((member) => {
        const isChecked = checkedMembers.has(member.id.toString()) ? 'checked' : '';
        const row = `<tr>
        <td><input type="checkbox" name="selectedMembers[]" id="checkbox_${member.id}" value="${member.id}" ${isChecked}></td>
        <td><label for="checkbox_${member.id}">${member.name}</label></td>
        <td><label for="checkbox_${member.id}">${member.role_name}</label></td>
        </tr>`;

        memberTableBody.innerHTML += row;
    });

    updatePaginationControls(membersArray.length);
    updateSelectAllUserCheckbox();
    updateSelectedMembersDisplay();
}

function updateSelectedMembersDisplay() {
    const container = document.getElementById('selectedMembersContainer');
    container.innerHTML = '';

    checkedMembers.forEach(memberId => {
        const member = Object.values(members).find(m => m.id.toString() === memberId);
        if (member) {
            const memberBox = document.createElement('span');
            memberBox.className = 'badge bg-primary me-2 mb-2';
            memberBox.innerHTML = `
                ${member.name}
                <i class="fas fa-times ms-1" style="cursor: pointer;" data-member-id="${member.id}"></i>
            `;
            memberBox.querySelector('i').addEventListener('click', function () {
                removeMember(this.getAttribute('data-member-id'));
            });
            container.appendChild(memberBox);
        }
    });
}

function removeMember(memberId) {
    checkedMembers.delete(memberId.toString());
    const checkbox = document.getElementById(`checkbox_${memberId}`);
    if (checkbox) {
        checkbox.checked = false;
    }
    updateSelectedMembersDisplay();
    updateSelectAllUserCheckbox();
}

function changePage(newPage) {
    const membersToUse = filteredMembers.length > 0 ? filteredMembers : members;
    const totalPages = Math.ceil(Object.keys(membersToUse).length / countPerPage);
    if (newPage >= 1 && newPage <= totalPages) {
        presentPage = newPage;
        displayMembers(presentPage, membersToUse);
    }
}

function updatePaginationControls(totalItems) {
    const totalPages = Math.max(1, Math.ceil(totalItems / countPerPage));
    const paginationControls = document.getElementById('paginationControls');
    let controlsHTML = `
    <button class="btn btn-primary p-1 m-1 btn-pagination"  onclick="changePage(${presentPage - 1})" ${presentPage <= 1 ? 'disabled' : ''}><i class='fas fa-angle-left'
                        style='font-size:36px'></i></button>
    <button class="btn btn-primary p-1 m-1 btn-pagination"   onclick="changePage(${presentPage + 1})" ${presentPage >= totalPages ? 'disabled' : ''}><i class='fas fa-angle-right'
                        style='font-size:36px'></i></button>`;
    paginationControls.innerHTML = controlsHTML;
}

console.log(members);

displayMembers(presentPage, members);

// Select all members functionality
document.getElementById('selectAllMembers').addEventListener('change', function () {
    const checkboxes = document.querySelectorAll('#memberTableBody input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
        if (this.checked) {
            checkedMembers.add(checkbox.value);
        } else {
            checkedMembers.delete(checkbox.value);
        }
    });
    updateSelectedMembersDisplay();
});

// Update individual checkbox event listeners
document.getElementById('memberTableBody').addEventListener('change', function (e) {
    if (e.target.type === 'checkbox') {
        const memberId = e.target.value;
        if (e.target.checked) {
            checkedMembers.add(memberId);
        } else {
            checkedMembers.delete(memberId);
        }
        updateSelectedMembersDisplay();
        updateSelectAllUserCheckbox();
    }
});

function updateSelectAllUserCheckbox() {
    const selectAllCheckbox = document.getElementById('selectAllMembers');
    const checkboxes = document.querySelectorAll('#memberTableBody input[type="checkbox"]');
    selectAllCheckbox.checked = checkboxes.length > 0 && Array.from(checkboxes).every(cb => cb.checked);
}

// Member search functionality
document.getElementById('memberSearch').addEventListener('input', function () {
    const searchTerm = this.value.toLowerCase();
    filteredMembers = Object.values(members).filter(member =>
        member.name.toLowerCase().includes(searchTerm)
    );
    presentPage = 1; // Reset to first page when searching
    displayMembers(presentPage, filteredMembers);
});

// Make changePage function global so it can be called from HTML
window.changePage = changePage;
 }