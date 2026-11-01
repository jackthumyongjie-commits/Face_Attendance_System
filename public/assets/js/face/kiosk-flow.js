import { AttendanceFlow } from './attendance-flow.js';

export class KioskFlow extends AttendanceFlow {
  constructor(opts) {
    super(opts);
    this.cooldownMs = (opts.settings.duplicate_scan_interval || 300) * 1000;
  }

  async process(detection) {
    this.busy = true;
    const match = this.matcher.match(detection.descriptor);
    if (!match.matched) {
      this.setStatus('Unknown face.', 'error');
      this.setResult('<div class="kiosk-result error"><h3>Unknown Face</h3><p>Please register your face first.</p></div>');
      this.busy = false;
      await this.wait(1800);
      return;
    }
    if (match.confidence < this.settings.min_attendance_confidence) {
      this.setStatus('Face match confidence is too low.', 'error');
      this.setResult('<div class="kiosk-result error"><h3>Low Confidence</h3><p>Please try again.</p></div>');
      this.busy = false;
      await this.wait(1800);
      return;
    }

    const photo = this.camera.captureDataUrl(0.75);
    const res = await AppApi.post('/api/face/attendance', {
      student_id: match.student_id,
      confidence: match.confidence,
      photo,
    });

    if (res.success) {
      this.setStatus(res.message, 'success');
      this.setResult(`
        <div class="kiosk-result success">
          <h2>${res.message}</h2>
          <h3>${res.student.full_name}</h3>
          <p>Code: ${res.student.student_code}</p>
          <p>Confidence: ${(match.confidence * 100).toFixed(1)}%</p>
          <p>Time: ${res.time}</p>
        </div>
      `);
      await this.wait(Math.min(this.cooldownMs, 5000));
    } else {
      this.setStatus(res.message || 'Failed', 'error');
      this.setResult(`<div class="kiosk-result error"><h3>${res.message || 'Failed'}</h3></div>`);
      await this.wait(2500);
    }
    this.busy = false;
  }
}
