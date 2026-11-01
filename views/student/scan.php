<div class="panel">
    <div class="panel-title">Scan Face for Attendance</div>
    <?php if ($todayAttendance): ?>
        <div class="alert alert-secondary">Today status: <strong><?= e($todayAttendance['status']) ?></strong>
            — In: <?= e($todayAttendance['check_in_time'] ? date('H:i:s', strtotime($todayAttendance['check_in_time'])) : '-') ?>
            — Out: <?= e($todayAttendance['check_out_time'] ? date('H:i:s', strtotime($todayAttendance['check_out_time'])) : '-') ?>
        </div>
    <?php endif; ?>
    <div class="row g-3">
        <div class="col-lg-7">
            <div class="camera-stage">
                <video id="faceVideo" autoplay muted playsinline></video>
                <canvas id="faceCanvas"></canvas>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="face-status" id="faceStatus">Click Start to scan.</div>
            <div class="face-status mt-3" id="faceResult">Waiting...</div>
            <button class="btn btn-primary mt-3" id="startFaceBtn" type="button">Start Scan</button>
            <button class="btn btn-outline-danger mt-3" id="stopFaceBtn" type="button">Stop</button>
        </div>
    </div>
</div>
<script src="<?= asset('assets/vendor/face-api.min.js') ?>"></script>
<script type="module">
import { AttendanceFlow } from <?= json_encode(asset('assets/js/face/attendance-flow.js')) ?>;
const settings = <?= json_encode($faceSettings) ?>;
const modelsPath = <?= json_encode(asset(ltrim(config('app.face_models_path'), '/'))) ?>;
const flow = new AttendanceFlow({
  video: document.getElementById('faceVideo'),
  canvas: document.getElementById('faceCanvas'),
  statusEl: document.getElementById('faceStatus'),
  resultEl: document.getElementById('faceResult'),
  studentId: <?= (int)$student['id'] ?>,
  settings,
  modelsPath,
});
document.getElementById('startFaceBtn').addEventListener('click', () => flow.start().catch(e => alert(e.message)));
document.getElementById('stopFaceBtn').addEventListener('click', () => flow.stop());
</script>
