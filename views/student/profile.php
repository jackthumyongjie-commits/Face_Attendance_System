<div class="panel">
    <div class="panel-title">My Profile</div>
    <div class="row g-3">
        <div class="col-md-6"><strong>Full Name:</strong> <?= e($student['full_name']) ?></div>
        <div class="col-md-6"><strong>Student Code:</strong> <?= e($student['student_code']) ?></div>
        <div class="col-md-6"><strong>Email:</strong> <?= e($student['email']) ?></div>
        <div class="col-md-6"><strong>Username:</strong> <?= e($student['username']) ?></div>
        <div class="col-md-6"><strong>Phone:</strong> <?= e($student['phone'] ?? '-') ?></div>
        <div class="col-md-6"><strong>Gender:</strong> <?= e($student['gender'] ?? '-') ?></div>
        <div class="col-md-6"><strong>Date of Birth:</strong> <?= e($student['date_of_birth'] ?? '-') ?></div>
        <div class="col-md-6"><strong>Class:</strong> <?= e($student['class_name'] ?? '-') ?></div>
        <div class="col-md-6"><strong>Face Registered:</strong> <?= (int)$student['face_registered']===1?'Yes':'No' ?></div>
        <div class="col-md-6"><strong>Registration Version:</strong> <?= (int)$student['registration_version'] ?></div>
    </div>
</div>
