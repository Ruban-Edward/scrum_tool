<?php
/**
 * @author     Jeril
 * @datetime   05 Aug 2024
 * Purpose: Design Template for the sprint view page to generate pdf.
 * 
 */
?>
<style>
  body {
    font-family: Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
    font-size: 10px;
  }

  table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
  }

  th,
  td {
    border: 1px solid #000000;
    padding: 8px;
    text-align: left;
  }

  th {
    background-color: #d8dcf3;
    color: #000000;
    text-align: center;
  }

  .overview th {
    text-align: left;
  }

  h2,
  h3 {
    color: #333;

  }

  tr {
    page-break-inside: avoid;
  }
</style>

<h2 style="text-align:center;"><u>Sprint Report</u></h2>

<h2>Sprint name: <?= $viewData['sprintOverview'][0]['sprint_name'] ?></h2>

<h3>Sprint Overview</h3>
<table class="overview">
  <tr>
    <th>Sprint name</th>
    <td><?= $viewData['sprintOverview'][0]['sprint_name'] ?></td>
  </tr>
  <tr>
    <th>Sprint version</th>
    <td><?= $viewData['sprintOverview'][0]['sprint_version'] ?></td>
  </tr>
  <tr>
    <th>Product</th>
    <td><?= $viewData['sprintOverview'][0]['product_name'] ?></td>
  </tr>
  <tr>
    <th>Customer</th>
    <td><?= $viewData['sprintOverview'][0]['customer_name'] ?></td>
  </tr>
  <tr>
    <th>Start date</th>
    <td><?= $viewData['sprintOverview'][0]['start_date'] ?></td>
  </tr>
  <tr>
    <th>End date</th>
    <td><?= $viewData['sprintOverview'][0]['end_date'] ?></td>
  </tr>
  <tr>
    <th>Sprint goal</th>
    <td><?= strip_tags($viewData['sprintOverview'][0]['sprint_goal']) ?></td>
  </tr>
  <tr>
    <th>Duration</th>
    <td><?= $viewData['sprintOverview'][0]['sprint_duration'] ?></td>
  </tr>
  <tr>
    <th>Sprint status</th>
    <td><?= $viewData['sprintOverview'][0]['sprint_status_name'] ?></td>
  </tr>
</table>

<h3>Sprint Members</h3>
<table>
  <tr>
    <th>Employee name</th>
    <th>Email id</th>
    <th>Employee role</th>
  </tr>
  <?php foreach ($viewData['sprintMembers'] as $member): ?>
    <tr>
      <td><?= $member['name'] ?></td>
      <td><?= $member['email_id'] ?></td>
      <td><?= $member['role_name'] ?></td>
    </tr>
  <?php endforeach; ?>
</table>
<h3>Sprint Planning</h3>
<table>
  <tr>
    <th>Component name</th>
    <th>Start date</th>
    <th>End date</th>
    <th>Status</th>
    <th>Comments</th>
  </tr>
  <?php if (count($viewData['sprintPlanning']) > 0) {
    foreach ($viewData['sprintPlanning'] as $plan): ?>
      <tr>
        <td><?= $plan['activity'] ?></td>
        <td><?= $plan['startDate'] ?></td>
        <td><?= $plan['endDate'] ?></td>
        <td><?= $plan['status_name'] ?></td>
        <td><?= $plan['notes'] ?></td>
      </tr>
    <?php endforeach;
  } else { ?>
    <tr>
      <td colspan="5" style="text-align:center">No data found</td>
    </tr>
  <?php } ?>
</table>

<h3>Sprint Tasks</h3>
<table>
  <tr>
    <th>Backlog</th>
    <th>Epic</th>
    <th>User story</th>
    <th>Task</th>
    <th>Task status</th>
  </tr>
  <?php foreach ($viewData['sprintTask'] as $task): ?>
    <tr>
      <td><?= $task['backlog_item_name'] ?></td>
      <td><?= $task['epic_name'] ?></td>
      <td><?= $task['user_story'] ?></td>
      <td><?= $task['task_title'] ?></td>
      <td><?= $task['task_status'] ?></td>
    </tr>
  <?php endforeach; ?>
</table>

<h3>Daily Scrum</h3>
<table style="border: 1px solid black">
  <thead>
    <tr>
      <th>Date</th>
      <th>Tasks</th>
      <th>Challenges</th>
      <th>Comments</th>
    </tr>
  </thead>
  <tbody>
    <?php if (count($viewData['dailyScrum']) > 0) {
      foreach ($viewData['dailyScrum'] as $dailyscrum): ?>
        <tr>
          <td><?= $dailyscrum['added_date'] ?></td>
          <td><?= $dailyscrum['task_title'] ?></td>
          <td><?= $dailyscrum['challenges'] ?></td>
          <td><?= $dailyscrum['notes'] ?></td>
        </tr>
      <?php endforeach;
    } else { ?>
      <tr>
        <td colspan="4" style="text-align:center">No data found</td>
      </tr>
    <?php } ?>
  </tbody>
</table>

<h3>Sprint Review</h3>
<p><strong>Review date:</strong> <?= $viewData['sprintReviewDate'][0]['review_date'] ?></p>
<table style="border: 1px solid black">
  <thead>
    <tr>
      <th>General</th>
      <th>Challenges</th>
      <th>Challenge faced</th>
      <th>Code review</th>
      <th>Reason</th>
      <th>Code review done by</th>
      <th>Sprint goal</th>
      <th>Reason</th>
    </tr>
  </thead>
  <tbody>
    <?php if (count($viewData['sprintReview']) > 0) { ?>
      <tr>
        <td><?= $viewData['sprintReview']['general'] ?></td>
        <td><?= $viewData['sprintReview']['challengesStatus'] ?></td>
        <td><?= $viewData['sprintReview']['challengeFaced'] ?></td>
        <td><?= $viewData['sprintReview']['codeReviewStatus'] ?></td>
        <td><?= $viewData['sprintReview']['codeReview'] ?></td>
        <td><?= $viewData['sprintReview']['codeReviewers'] ?></td>
        <td><?= $viewData['sprintReview']['sprintGoalStatus'] ?></td>
        <td><?= $viewData['sprintReview']['sprintGoal'] ?></td>
      </tr>
    <?php } else { ?>
      <tr>
        <td colspan="8" style="text-align:center">No data found</td>
      </tr>
    <?php } ?>
  </tbody>
</table>

<h3>Sprint Retrospective</h3>
<p><strong>Retrospective date:</strong>
  <?= $viewData['sprintRetrospectiveDate'][0]['retrospective_date'] ?></p>
<table style="border: 1px solid black">
  <thead>
    <tr>
      <th>Feedback type</th>
      <th>Comments</th>
    </tr>
  </thead>
  <tbody>
    <?php if (count($viewData['sprintRetrospective']) > 0) {
      foreach ($viewData['sprintRetrospective'] as $retrospective):
        if (isset($retrospective['notes'])) { ?>
          <tr>
            <td><?= $retrospective['challenge'] ?></td>
            <td><?= $retrospective['notes'] ?></td>
          </tr>
        <?php }endforeach;
    } else { ?>
      <tr>
        <td colspan="2" style="text-align:center">No data found</td>
      </tr>
    <?php } ?>
  </tbody>
</table>