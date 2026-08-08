"use client";

import { forwardRef, useEffect, useImperativeHandle, useRef } from "react";

export interface SignaturePadHandle {
  clear: () => void;
  toDataURL: () => string;
}

export const SignaturePad = forwardRef<SignaturePadHandle>(function SignaturePad(_props, ref) {
  const canvasRef = useRef<HTMLCanvasElement>(null);

  useImperativeHandle(ref, () => ({
    clear() {
      const canvas = canvasRef.current;
      const ctx = canvas?.getContext("2d");
      if (canvas && ctx) ctx.clearRect(0, 0, canvas.width, canvas.height);
    },
    toDataURL() {
      return canvasRef.current?.toDataURL("image/png") || "";
    }
  }));

  useEffect(() => {
    const canvas = canvasRef.current;
    if (!canvas) return;
    const ctx = canvas.getContext("2d");
    if (!ctx) return;

    ctx.lineWidth = 2.2;
    ctx.lineCap = "round";
    ctx.strokeStyle = "#1D7C88";

    let drawing = false;
    let last: { x: number; y: number } | null = null;

    function pos(e: MouseEvent | TouchEvent) {
      const rect = canvas!.getBoundingClientRect();
      const p = "touches" in e ? e.touches[0] : e;
      return {
        x: (p.clientX - rect.left) * (canvas!.width / rect.width),
        y: (p.clientY - rect.top) * (canvas!.height / rect.height),
      };
    }
    function start(e: MouseEvent | TouchEvent) {
      drawing = true;
      last = pos(e);
      e.preventDefault();
    }
    function move(e: MouseEvent | TouchEvent) {
      if (!drawing || !last) return;
      const p = pos(e);
      ctx!.beginPath();
      ctx!.moveTo(last.x, last.y);
      ctx!.lineTo(p.x, p.y);
      ctx!.stroke();
      last = p;
      e.preventDefault();
    }
    function end() {
      drawing = false;
    }

    canvas.addEventListener("mousedown", start);
    canvas.addEventListener("mousemove", move);
    window.addEventListener("mouseup", end);
    canvas.addEventListener("touchstart", start, { passive: false });
    canvas.addEventListener("touchmove", move, { passive: false });
    canvas.addEventListener("touchend", end);

    return () => {
      canvas.removeEventListener("mousedown", start);
      canvas.removeEventListener("mousemove", move);
      window.removeEventListener("mouseup", end);
      canvas.removeEventListener("touchstart", start);
      canvas.removeEventListener("touchmove", move);
      canvas.removeEventListener("touchend", end);
    };
  }, []);

  return (
    <canvas
      ref={canvasRef}
      width={600}
      height={160}
      className="h-24 w-full touch-none rounded-xl border border-slate-200 bg-slate-50"
    />
  );
});
