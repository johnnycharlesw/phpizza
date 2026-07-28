let myVideo: HTMLVideoElement | null = null;
let paused = true;
let playPauseBtn: HTMLButtonElement | null = null;
let playIcon: HTMLImageElement | null = null;
const seek = document.getElementById("seek") as HTMLInputElement | null;
const timeStatus = document.getElementById("timeStatus");

function fmtTime(t: number) {
  if (!Number.isFinite(t) || t < 0) return "0:00";
  const m = Math.floor(t / 60);
  const s = Math.floor(t % 60).toString().padStart(2, "0");
  return `${m}:${s}`;
}

window.addEventListener("DOMContentLoaded", () => {
  myVideo = document.getElementById("video") as HTMLVideoElement | null;
  playPauseBtn = document.getElementById("playPauseBtn") as HTMLButtonElement | null;
  playIcon = document.getElementById("playIcon") as HTMLImageElement | null;
  paused = myVideo ? myVideo.paused : true;
  if (!myVideo) {
    return;
  }
  if (!playPauseBtn) {
    return;
  }
  if (!playIcon) {
    return
  }
    myVideo!.addEventListener("loadedmetadata", () => {
    if (seek && Number.isFinite(myVideo!.duration)) {
      seek.max = String(myVideo!.duration);
      seek.value = String(myVideo!.currentTime);
    }
    if (timeStatus) timeStatus.textContent = `${fmtTime(myVideo!.currentTime)} / ${fmtTime(myVideo!.duration)}`;
  });
    // Update UI as playback progresses
  myVideo!.addEventListener("timeupdate", () => {
    if (!myVideo) return;
    if (seek) seek.value = String(myVideo.currentTime);
    if (timeStatus) timeStatus.textContent = `${fmtTime(myVideo.currentTime)} / ${fmtTime(myVideo.duration)}`;
  });

    // Optional: keep button icon in sync

  myVideo.addEventListener("play", () => {
    if (playPauseBtn && playIcon) playIcon.src="/node_modules/feather-icons/dist/icons/pause.svg";
  });
  myVideo.addEventListener("pause", () => {
    if (playPauseBtn && playIcon) playIcon.src="/node_modules/feather-icons/dist/icons/play.svg";
  });

    // Seek when user drags
  let isSeeking = false;

  if (seek) {
    seek.addEventListener("input", () => {
      if (!myVideo) return;
      isSeeking = true;
      myVideo.currentTime = Number(seek.value);
    });

    seek.addEventListener("change", () => {
      isSeeking = false;
    });
  }

});

(window as any).playPause = function playPause() {
  if (!myVideo) {
    console.warn('Video element not found: expected id="video"');
    return;
  }

  if (paused || myVideo.paused) {
    myVideo.play().catch(err => console.warn("play() failed:", err));
    if (playPauseBtn && playIcon) playIcon.src="/node_modules/feather-icons/dist/icons/play.svg";
    paused = false;
  } else {
    myVideo.pause();
    if (playPauseBtn && playIcon) playIcon.src="/node_modules/feather-icons/dist/icons/pause.svg";
    paused = true;
  }
};
