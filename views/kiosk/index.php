<div class="kiosk-wrap">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h3 mb-0">Attendance Kiosk</h1>
            <div class="opacity-75"><?= e(config('app.name')) ?> · <?= e(date('l, d M Y H:i')) ?></div>
        </div>
        <a href="<?= url('/admin/dashboard') ?>" class="btn btn-outline-light btn-sm">Exit Kiosk</a>
    </div>
    <div class="row g-4">
        <div class="col-lg-7">
            <div class="camera-stage shadow">
                <video id="faceVideo" autoplay muted playsinline></video>
                <canvas id="faceCanvas"></canvas>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="face-status mb-3" id="faceStatus" style="background:rgba(255,255,255,.08);color:#fff;border-color:rgba(255,255,255,.15)">Starting kiosk...</div>
            <div id="faceResult"><div class="kiosk-result"><h3>Ready</h3><p>Stand in front of the camera to check in or check out.</p></div></div>
            <button class="btn btn-light mt-3" id="startFaceBtn" type="button">Start Camera</button>
            <button class="btn btn-outline-light mt-3" id="stopFaceBtn" type="button">Stop</button>
        </div>
    </div>
</div>
<script src="<?= asset('assets/js/app.js') ?>"></script>
<script src="<?= asset('assets/vendor/face-api.min.js') ?>"></script>
<script type="module">
import { KioskFlow } from <?= json_encode(asset('assets/js/face/kiosk-flow.js')) ?>;
const settings = <?= json_encode($faceSettings) ?>;
const modelsPath = <?= json_encode(asset(ltrim(config('app.face_models_path'), '/'))) ?>;
const flow = new KioskFlow({
  video: document.getElementById('faceVideo'),
  canvas: document.getElementById('faceCanvas'),
  statusEl: document.getElementById('faceStatus'),
  resultEl: document.getElementById('faceResult'),
  settings,
  modelsPath,
});
document.getElementById('startFaceBtn').addEventListener('click', () => flow.start().catch(e => alert(e.message)));
document.getElementById('stopFaceBtn').addEventListener('click', () => flow.stop());
flow.start().catch(() => {});
</script>
