export class PoseChecker {
  isFrontal(landmarks) {
    if (!landmarks?.positions) return true;
    const nose = landmarks.positions[30];
    const leftEye = landmarks.positions[36];
    const rightEye = landmarks.positions[45];
    const eyeMidX = (leftEye.x + rightEye.x) / 2;
    const offset = Math.abs(nose.x - eyeMidX);
    const eyeDist = Math.abs(rightEye.x - leftEye.x) || 1;
    return offset / eyeDist < 0.35;
  }
}
