export class FaceEngine {
  constructor(modelsPath) {
    this.modelsPath = modelsPath;
    this.ready = false;
  }

  async load() {
    if (this.ready) return;
    if (!window.faceapi) {
      throw new Error('face-api.js library is not loaded.');
    }
    const faceapi = window.faceapi;
    await Promise.all([
      faceapi.nets.tinyFaceDetector.loadFromUri(this.modelsPath),
      faceapi.nets.faceLandmark68Net.loadFromUri(this.modelsPath),
      faceapi.nets.faceRecognitionNet.loadFromUri(this.modelsPath),
    ]);
    this.ready = true;
  }

  options(scoreThreshold = 0.5) {
    return new faceapi.TinyFaceDetectorOptions({ inputSize: 320, scoreThreshold });
  }

  async detectAll(input, scoreThreshold = 0.5) {
    await this.load();
    return faceapi
      .detectAllFaces(input, this.options(scoreThreshold))
      .withFaceLandmarks()
      .withFaceDescriptors();
  }

  async detectSingle(input, scoreThreshold = 0.5) {
    const dets = await this.detectAll(input, scoreThreshold);
    if (!dets.length) return { status: 'none', detections: [] };
    if (dets.length > 1) return { status: 'multiple', detections: dets };
    return { status: 'single', detections: dets, detection: dets[0] };
  }

  draw(canvas, video, detections) {
    const displaySize = { width: video.clientWidth, height: video.clientHeight };
    faceapi.matchDimensions(canvas, displaySize);
    const resized = faceapi.resizeResults(detections, displaySize);
    const ctx = canvas.getContext('2d');
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    faceapi.draw.drawDetections(canvas, resized);
    faceapi.draw.drawFaceLandmarks(canvas, resized);
  }
}
