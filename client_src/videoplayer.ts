//@ts-ignore
let myVideo: HTMLVideoElement | null = null;
let paused = true;
let playIcon: HTMLImageElement | null = null;
let pauseIconSrc = "/node_modules/feather-icons/dist/icons/pause.svg";
let playIconSrc = "/node_modules/feather-icons/dist/icons/play.svg";
let playPauseBtn: HTMLButtonElement | null = null;
let seek: HTMLInputElement | null = null;
let timeStatus: HTMLSpanElement | null = null;
let isSeeking = false;
let played = false;


function fmtTime(t: number) {
  if (!Number.isFinite(t) || t < 0) return "LIVE"; // If the video supposedly goes on forever, assume it's a livestream
  const m = Math.floor(t / 60);
  const s = Math.floor(t % 60).toString().padStart(2, "0");
  return `${m}:${s}`;
}


(window as any).playPause = function () { 
    if (paused) {
      
        myVideo!.play();
        playIcon!.src = pauseIconSrc;
        paused = false;
    }
    else {
        myVideo!.pause(); 
        playIcon!.src = playIconSrc;
        paused = true;
    }
} 

document.addEventListener('DOMContentLoaded', function (ev) {
                                    
    if (!myVideo) {
        myVideo = document.getElementById("video")! as HTMLVideoElement;
    }

    if (!playIcon) {
        playIcon = document.getElementById("playIcon")! as HTMLImageElement;
        playIcon.src=playIconSrc;
    }
    if (!seek) {
        seek = document.getElementById("seek")! as HTMLInputElement;
    }
    if (!timeStatus) {
        timeStatus = document.getElementById("timeStatus")! as HTMLSpanElement;
    }
    if (!playPauseBtn) {
      playPauseBtn = document.getElementById("playPauseBtn")! as HTMLButtonElement;
    }
    myVideo.addEventListener('loadedmetadata', function (ev) {
        if (seek && isFinite(myVideo!.duration)) {
            seek.max = String(myVideo!.duration);
            seek.value = String(myVideo!.currentTime);
        } else if (seek && !isFinite(myVideo!.duration)) {
            seek.max = String(0);
            seek.value = String(0);
        }
        if (timeStatus) timeStatus.textContent = `${fmtTime(myVideo!.currentTime)} / ${fmtTime(myVideo!.duration)}`;
    })


    // Seek

    seek!.addEventListener("input", () => {
        if (!myVideo) return;
        isSeeking = true;
        myVideo.currentTime = Number(seek!.value);
    });

    seek!.addEventListener("change", () => {
        isSeeking = false;
    });

    myVideo!.addEventListener('timeupdate', function (ev: Event) {
        seek!.value = String(myVideo!.currentTime);
        timeStatus!.textContent = `${fmtTime(myVideo!.currentTime)} / ${fmtTime(myVideo!.duration)}`;
    })
});
