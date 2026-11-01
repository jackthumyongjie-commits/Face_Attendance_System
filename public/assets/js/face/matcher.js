export class FaceMatcher {
  constructor(threshold = 0.55) {
    this.threshold = threshold;
    this.labeled = [];
  }

  loadStudents(students) {
    this.labeled = (students || []).map((s) => {
      const descriptors = (s.descriptors || [])
        .filter((d) => Array.isArray(d) && d.length)
        .map((d) => new Float32Array(d));
      return {
        student_id: s.student_id,
        student_code: s.student_code,
        full_name: s.full_name,
        descriptors,
      };
    }).filter((s) => s.descriptors.length);
  }

  match(descriptor) {
    if (!descriptor || !this.labeled.length) {
      return { matched: false, message: 'Unknown face.' };
    }

    let best = null;
    for (const student of this.labeled) {
      for (const ref of student.descriptors) {
        const distance = this.euclidean(descriptor, ref);
        if (!best || distance < best.distance) {
          best = { student, distance };
        }
      }
    }

    if (!best || best.distance > this.threshold) {
      return { matched: false, message: 'Unknown face.', distance: best?.distance ?? null };
    }

    const confidence = Math.max(0, Math.min(1, 1 - best.distance));
    return {
      matched: true,
      student_id: best.student.student_id,
      student_code: best.student.student_code,
      full_name: best.student.full_name,
      distance: best.distance,
      confidence,
    };
  }

  euclidean(a, b) {
    let sum = 0;
    const len = Math.min(a.length, b.length);
    for (let i = 0; i < len; i++) {
      const d = a[i] - b[i];
      sum += d * d;
    }
    return Math.sqrt(sum);
  }
}
