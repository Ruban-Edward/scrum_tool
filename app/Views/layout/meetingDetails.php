<div class="d-flex justify-content-between">
    <h5 class="content-header d-flex align-items-center justify-content-between mb-2">
        Notifications
        <i class="icon-Notification" id="notification-icon"></i>
    </h5>
    <div id="meeting-filters" class="pb-2" style="display:none;">
        <input type="radio" class="form-check-input meet-select-radio pointer" id="meetings" name="meetingFilter" value="meetings" checked>
        <label for="meetings" class="fw-bold meet-select pointer">Meetings</label>
        <input type="radio" class="form-check-input meet-select-radio pointer" id="upcoming-scrum" name="meetingFilter" value="upcoming-scrum">
        <label for="upcoming-scrum" class="fw-bold meet-select pointer">Scrum</label>
    </div>
</div>
<ul id="meeting-list"></ul>
<div id="no-meetings-message" class="card mt-3 p-2" style="display:none;">
    <h6 class="text-center text-dark">No notification available</h6>
</div>
