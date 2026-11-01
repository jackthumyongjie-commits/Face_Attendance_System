<div class="panel">
    <div class="panel-title"><?= (int)$student['face_registered']===1 ? 'Re-register' : 'Register' ?> Your Face</div>
    <div class="row g-3">
        <div class="col-lg-7">
            <div class="camera-stage">
                <video id="faceVideo" autoplay muted playsinline></video>
                <canvas id="faceCanvas"></canvas>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="face-status" id="faceStatus">Click Start Camera to begin.</div>
            <div class="mt-3" id="faceProgress">Samples: 0</div>
            <button class="btn btn-primary mt-3" id="startFaceBtn" type="button">Start Camera</button>
            <button class="btn btn-outline-danger mt-3" id="stopFaceBtn" type="button">Stop</button>
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
