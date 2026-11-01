export class QualityChecker {
  constructor(settings) {
    this.settings = settings;
  }

  evaluate(detection, video) {
    if (!detection) {
      return { ok: false, message: 'No face detected.' };
    }

    const box = detection.detection.box;
    const score = detection.detection.score;
    const size = Math.max(box.width, box.height);

    if (score < this.settings.detection_confidence) {
      return { ok: false, message: 'Face detection confidence is too low.' };
    }
    if (size < this.settings.min_face_size) {
      return { ok: false, message: 'Face too small. Please move closer.' };
    }
    if (size > this.settings.max_face_size) {
      return { ok: false, message: 'Face too large. Please move farther.' };
    }

    const brightness = this.estimateBrightness(video, box);
    if (brightness < this.settings.min_brightness) {
      return { ok: false, message: 'Lighting is too low.' };
    }
    if (brightness > this.settings.max_brightness) {
      return { ok: false, message: 'Lighting is too bright.' };
    }

    const sharpness = this.estimateSharpness(video, box);
    if (sharpness < this.settings.min_sharpness) {
      return { ok: false, message: 'Image quality is too low. Hold still.' };
    }

    return { ok: true, message: 'Face quality OK', brightness, sharpness, size, score };
  }

  estimateBrightness(video, box) {
    const canvas = document.createElement('canvas');
    const w = Math.max(16, Math.floor(box.width));
    const h = Math.max(16, Math.floor(box.height));
    canvas.width = w;
    canvas.height = h;
    const ctx = canvas.getContext('2d', { willReadFrequently: true });
    ctx.drawImage(video, box.x, box.y, box.width, box.height, 0, 0, w, h);
    const data = ctx.getImageData(0, 0, w, h).data;
    let sum = 0;
    for (let i = 0; i < data.length; i += 16) {
      sum += 0.299 * data[i] + 0.587 * data[i + 1] + 0.114 * data[i + 2];
    }
    return sum / (data.length / 16);
  }

  estimateSharpness(video, box) {
    const canvas = document.createElement('canvas');
    const w = 64;
    const h = 64;
    canvas.width = w;
    canvas.height = h;
    const ctx = canvas.getContext('2d', { willReadFrequently: true });
    ctx.drawImage(video, box.x, box.y, box.width, box.height, 0, 0, w, h);
    const img = ctx.getImageData(0, 0, w, h).data;
    let sum = 0;
    let count = 0;
    for (let y = 1; y < h - 1; y++) {
      for (let x = 1; x < w - 1; x++) {
        const i = (y * w + x) * 4;
        const gray = 0.299 * img[i] + 0.587 * img[i + 1] + 0.114 * img[i + 2];
        const right = 0.299 * img[i + 4] + 0.587 * img[i + 5] + 0.114 * img[i + 6];
        const downIdx = ((y + 1) * w + x) * 4;
        const down = 0.299 * img[downIdx] + 0.587 * img[downIdx + 1] + 0.114 * img[downIdx + 2];
        const lap = Math.abs(4 * gray - right - down - (0.299 * img[i - 4] + 0.587 * img[i - 3] + 0.114 * img[i - 2]) - (0.299 * img[((y - 1) * w + x) * 4] + 0.587 * img[((y - 1) * w + x) * 4 + 1] + 0.114 * img[((y - 1) * w + x) * 4 + 2]));
        sum += lap;
        count++;
      }
    }
    return count ? sum / count : 0;
  }
}
