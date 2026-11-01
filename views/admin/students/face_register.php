<div class="panel">
    <div class="panel-title">
        <span><?= (int)$student['face_registered']===1 ? 'Re-register' : 'Register' ?> Face — <?= e($student['full_name']) ?> (<?= e($student['student_code']) ?>)</span>
        <a href="<?= url('/admin/students/'.$student['id']) ?>" class="btn btn-sm btn-outline-secondary">Back</a>
    </div>
    <div class="row g-3">
        <div class="col-lg-7">
            <div class="camera-stage">
                <video id="faceVideo" autoplay muted playsinline></video>
                <canvas id="faceCanvas"></canvas>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="face-status" id="faceStatus">Initializing camera...</div>
            <div class="mt-3" id="faceProgress">Samples: 0</div>
            <button class="btn btn-primary mt-3" id="startFaceBtn" type="button">Start Camera</button>
            <button class="btn btn-outline-danger mt-3" id="stopFaceBtn" type="button">Stop</button>
            <p class="small text-muted mt-3 mb-0">Tips: good lighting, one face only, look straight at the camera, hold still for each sample.</p>
        </div>
    </div>
</div>
<script src="<?= asset('assets/vendor/face-api.min.js') ?>"></script>
<script type="module">
import { RegistrationFlow } from <?= json_encode(asset('assets/js/face/registration-flow.js')) ?>;
const settings = <?= json_encode($faceSettings) ?>;
const modelsPath = <?= json_encode(asset(ltrim(config('app.face_models_path'), '/'))) ?>;
const flow = new RegistrationFlow({
  video: document.getElementById('faceVideo'),
  canvas: document.getElementById('faceCanvas'),
  statusEl: document.getElementById('faceStatus'),
  progressEl: document.getElementById('faceProgress'),
  studentId: <?= (int)$student['id'] ?>,
  settings,
  modelsPath,
});
document.getElementById('startFaceBtn').addEventListener('click', () => flow.start().catch(e => alert(e.message)));
document.getElementById('stopFaceBtn').addEventListener('click', () => flow.stop());
</script>
