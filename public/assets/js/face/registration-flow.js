import { Camera } from './camera.js';
import { FaceEngine } from './face-engine.js';
import { QualityChecker } from './quality.js';
import { PoseChecker } from './pose.js';

export class RegistrationFlow {
  constructor(opts) {
    this.video = opts.video;
    this.canvas = opts.canvas;
    this.statusEl = opts.statusEl;
    this.progressEl = opts.progressEl;
    this.studentId = opts.studentId;
    this.settings = opts.settings;
    this.modelsPath = opts.modelsPath;
    this.camera = new Camera(this.video);
    this.engine = new FaceEngine(this.modelsPath);
    this.quality = new QualityChecker(this.settings);
    this.pose = new PoseChecker();
    this.samples = [];
    this.needed = this.settings.registration_samples || 3;
    this.running = false;
    this.stable = 0;
    this.lastDescriptor = null;
  }

  setStatus(message, type = 'info') {
    if (!this.statusEl) return;
    const tone = type === 'error' ? 'is-error' : type === 'success' ? 'is-success' : 'is-info';
    this.statusEl.className = `face-status ${tone}`;
    this.statusEl.innerHTML = `<div class="status-line">${message}</div>`;
  }

  updateProgress() {
    if (this.progressEl) {
      this.progressEl.textContent = `Samples: ${this.samples.length} / ${this.needed}`;
    }
  }

  async start() {
    this.setStatus('Loading face models...');
    await this.engine.load();
    await this.camera.start();
    this.running = true;
    this.setStatus('Please position your face inside the camera.');
    this.updateProgress();
    this.loop();
  }

  stop() {
    this.running = false;
    this.camera.stop();
  }

  async loop() {
    if (!this.running) return;
    try {
      const result = await this.engine.detectSingle(this.video, this.settings.detection_confidence);
      this.engine.draw(this.canvas, this.video, result.detections);

      if (result.status === 'none') {
        this.stable = 0;
        this.setStatus('No face detected.');
      } else if (result.status === 'multiple') {
        this.stable = 0;
        this.setStatus('Multiple faces detected.', 'error');
      } else {
        const q = this.quality.evaluate(result.detection, this.video);
        if (!q.ok) {
          this.stable = 0;
          this.setStatus(q.message, 'error');
        } else if (!this.pose.isFrontal(result.detection.landmarks)) {
          this.stable = 0;
          this.setStatus('Please face the camera directly.', 'error');
        } else {
          this.stable += 1;
          this.setStatus(`Hold still... (${this.stable}/${this.settings.stable_frames})`);
          if (this.stable >= this.settings.stable_frames) {
            const desc = Array.from(result.detection.descriptor);
            this.samples.push(desc);
            this.stable = 0;
            this.updateProgress();
            this.setStatus(`Sample ${this.samples.length} captured.`, 'success');
            if (this.samples.length >= this.needed) {
              await this.submit();
              return;
            }
            await this.wait(900);
          }
        }
      }
    } catch (e) {
      this.setStatus(e.message || 'Camera/detection error.', 'error');
    }
    requestAnimationFrame(() => this.loop());
  }

  wait(ms) {
    return new Promise((r) => setTimeout(r, ms));
  }

  async submit() {
    this.running = false;
    this.setStatus('Saving face registration...');
    const preview = this.camera.captureDataUrl();
    const res = await AppApi.post('/api/face/register', {
      student_id: this.studentId,
      descriptors: this.samples,
      preview_image: preview,
    });
    if (res.success) {
      this.setStatus('Face registration successful.', 'success');
      setTimeout(() => window.location.reload(), 1200);
    } else {
      this.setStatus(res.message || 'Face registration failed.', 'error');
      this.samples = [];
      this.updateProgress();
      this.running = true;
      this.loop();
    }
  }
}
