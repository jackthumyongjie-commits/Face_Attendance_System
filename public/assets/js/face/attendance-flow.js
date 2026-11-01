import { Camera } from './camera.js';
import { FaceEngine } from './face-engine.js';
import { QualityChecker } from './quality.js';
import { FaceMatcher } from './matcher.js';

export class AttendanceFlow {
  constructor(opts) {
    this.video = opts.video;
    this.canvas = opts.canvas;
    this.statusEl = opts.statusEl;
    this.resultEl = opts.resultEl;
    this.studentId = opts.studentId || null; // if set, only allow this student
    this.settings = opts.settings;
    this.modelsPath = opts.modelsPath;
    this.camera = new Camera(this.video);
    this.engine = new FaceEngine(this.modelsPath);
    this.quality = new QualityChecker(this.settings);
    this.matcher = new FaceMatcher(this.settings.face_match_threshold);
    this.running = false;
    this.stable = 0;
    this.busy = false;
  }

  setStatus(msg, type = 'info') {
    if (this.statusEl) {
      const tone = type === 'error' ? 'is-error' : type === 'success' ? 'is-success' : 'is-info';
      this.statusEl.className = `face-status ${tone}`;
      this.statusEl.innerHTML = `<div class="status-line">${msg}</div><div class="hint">Keep your face centered and well-lit.</div>`;
    }
  }

  setResult(html, cls = '') {
    if (!this.resultEl) return;
    this.resultEl.className = 'face-status ' + cls;
    this.resultEl.innerHTML = html;
  }

  async start() {
    this.setStatus('Loading models and descriptors...');
    await this.engine.load();
    const descRes = await AppApi.get('/api/face/descriptors');
    if (!descRes.success) throw new Error(descRes.message || 'Failed to load descriptors');
    this.matcher.loadStudents(descRes.students || []);
    await this.camera.start();
    this.running = true;
    this.setStatus('Ready to scan. Look at the camera.');
    this.loop();
  }

  stop() {
    this.running = false;
    this.camera.stop();
  }

  async loop() {
    if (!this.running) return;
    if (this.busy) {
      requestAnimationFrame(() => this.loop());
      return;
    }
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
        } else {
          this.stable += 1;
          this.setStatus(`Face detected. Stabilizing... (${this.stable}/${this.settings.stable_frames})`);
          if (this.stable >= this.settings.stable_frames) {
            await this.process(result.detection);
            this.stable = 0;
          }
        }
      }
    } catch (e) {
      this.setStatus(e.message || 'Scan error.', 'error');
    }
    requestAnimationFrame(() => this.loop());
  }

  async process(detection) {
    this.busy = true;
    const match = this.matcher.match(detection.descriptor);
    if (!match.matched) {
      this.setStatus(match.message || 'Unknown face.', 'error');
      this.setResult(`<strong>Unknown face</strong><br>No matching student found.`, 'error');
      this.busy = false;
      await this.wait(1500);
      return;
    }

    if (this.studentId && match.student_id !== this.studentId) {
      this.setStatus('Face does not match your registered profile.', 'error');
      this.busy = false;
      await this.wait(1500);
      return;
    }

    if (match.confidence < this.settings.min_attendance_confidence) {
      this.setStatus('Face match confidence is too low.', 'error');
      this.busy = false;
      await this.wait(1500);
      return;
    }

    this.setStatus(`Matched ${match.full_name}. Recording attendance...`);
    const photo = this.camera.captureDataUrl(0.8);
    const res = await AppApi.post('/api/face/attendance', {
      student_id: match.student_id,
      confidence: match.confidence,
      photo,
    });

    if (res.success) {
      this.setStatus(res.message, 'success');
      this.setResult(`
        <strong>${res.message}</strong><br>
        ${res.student?.full_name || match.full_name} (${res.student?.student_code || match.student_code})<br>
        Confidence: ${(match.confidence * 100).toFixed(1)}%<br>
        Time: ${res.time || ''}
      `, 'success');
      await this.wait(3000);
    } else {
      this.setStatus(res.message || 'Attendance failed.', 'error');
      this.setResult(`<strong>${res.message || 'Attendance failed.'}</strong>`, 'error');
      await this.wait(2000);
    }
    this.busy = false;
  }

  wait(ms) {
    return new Promise((r) => setTimeout(r, ms));
  }
}
