"use client";

import { useEffect, useMemo, useState, useRef } from "react";
import { Clock, MapPin, Camera } from "lucide-react";
import { useApp } from "@/lib/store";
import { Card } from "@/components/ui/Card";
import { Button } from "@/components/ui/Button";
import { cn } from "@/lib/utils";

const WEEK_LABELS = ["M", "T", "W", "T", "F", "S", "S"];

function useClock() {
  const [time, setTime] = useState<string>("");
  useEffect(() => {
    const tick = () =>
      setTime(new Date().toLocaleTimeString("en-IN", { hour: "2-digit", minute: "2-digit", hour12: true }));
    tick();
    const id = setInterval(tick, 30000);
    return () => clearInterval(id);
  }, []);
  return time;
}

function compressImage(file: File): Promise<File> {
  return new Promise((resolve) => {
    const reader = new FileReader();
    reader.readAsDataURL(file);
    reader.onload = (event) => {
      const img = new Image();
      img.src = event.target?.result as string;
      img.onload = () => {
        const canvas = document.createElement("canvas");
        const MAX_WIDTH = 600;
        const MAX_HEIGHT = 600;
        let width = img.width;
        let height = img.height;

        if (width > height) {
          if (width > MAX_WIDTH) {
            height *= MAX_WIDTH / width;
            width = MAX_WIDTH;
          }
        } else {
          if (height > MAX_HEIGHT) {
            width *= MAX_HEIGHT / height;
            height = MAX_HEIGHT;
          }
        }
        canvas.width = width;
        canvas.height = height;
        const ctx = canvas.getContext("2d");
        ctx?.drawImage(img, 0, 0, width, height);
        canvas.toBlob(
          (blob) => {
            if (blob) {
              const compressedFile = new File([blob], file.name, {
                type: "image/jpeg",
                lastModified: Date.now(),
              });
              resolve(compressedFile);
            } else {
              resolve(file);
            }
          },
          "image/jpeg",
          0.7
        );
      };
    };
  });
}

export default function AttendancePage() {
  const { attendance, toggleAttendance, loading } = useApp();
  const clockTime = useClock();
  const fileInputRef = useRef<HTMLInputElement>(null);
  
  const [selfieFile, setSelfieFile] = useState<File | null>(null);
  const [selfiePreview, setSelfiePreview] = useState<string | null>(null);
  const [compressing, setCompressing] = useState(false);
  const [submitting, setSubmitting] = useState(false);

  const today = useMemo(
    () => new Date().toLocaleDateString("en-IN", { weekday: "long", day: "numeric", month: "long", year: "numeric" }),
    []
  );
  const todayIndex = useMemo(() => (new Date().getDay() + 6) % 7, []); // Monday = 0

  const actionLabel = attendance.checkedIn
    ? "Check Out"
    : attendance.checkOutTime
      ? "Day Completed"
      : "Check In Now";
  const actionDisabled = !attendance.checkedIn && !!attendance.checkOutTime;
  const actionVariant = attendance.checkedIn ? "danger" : "primary";

  async function handleFileChange(e: React.ChangeEvent<HTMLInputElement>) {
    const file = e.target.files?.[0];
    if (!file) return;

    setCompressing(true);
    const compressed = await compressImage(file);
    setSelfieFile(compressed);
    setSelfiePreview(URL.createObjectURL(compressed));
    setCompressing(false);
  }

  async function handleToggleAttendance() {
    setSubmitting(true);
    await toggleAttendance(selfieFile || undefined);
    // Clear selfie states after checkin
    setSelfieFile(null);
    setSelfiePreview(null);
    setSubmitting(false);
  }

  return (
    <div className="flex-1 px-5 pb-6 pt-5">
      <div className="mb-5 flex items-center gap-2">
        <Clock className="h-5 w-5 text-aqua-600" strokeWidth={1.75} />
        <p className="font-display text-lg font-bold text-slate-800">Attendance</p>
      </div>

      <Card className="mb-5 border-0 bg-gradient-to-br from-aqua-600 to-aqua-800 p-5 text-center text-white">
        <p className="font-display text-4xl font-bold tracking-tight">{clockTime || "--:--"}</p>
        <p className="mt-1 text-xs font-medium text-aqua-100">{today}</p>
      </Card>

      <div className="mb-5 grid grid-cols-2 gap-3">
        <Card className="p-3.5">
          <p className="mb-1 text-[10px] font-bold uppercase text-slate-400">Check in</p>
          <p className="font-display text-base font-bold text-slate-800">
            {attendance.checkInTime || "--:--"}
          </p>
        </Card>
        <Card className="p-3.5">
          <p className="mb-1 text-[10px] font-bold uppercase text-slate-400">Check out</p>
          <p className="font-display text-base font-bold text-slate-800">
            {attendance.checkOutTime || "--:--"}
          </p>
        </Card>
      </div>

      <Card className="mb-5 p-4">
        <div className="mb-3 flex items-center gap-2">
          <MapPin className="h-4 w-4 text-slate-400" strokeWidth={1.75} />
          <p className="text-xs font-bold text-slate-600">Location captured</p>
        </div>
        <div className="relative flex h-24 items-center justify-center overflow-hidden rounded-xl bg-slate-100">
          <div
            className="absolute inset-0 opacity-40"
            style={{
              backgroundImage: "radial-gradient(circle,#94a3b8 1px,transparent 1px)",
              backgroundSize: "14px 14px",
            }}
          />
          <MapPin className="relative z-10 h-7 w-7 text-rose-500" strokeWidth={1.75} />
        </div>
        <p className="mt-2 text-[11px] font-medium text-slate-400">
          30.3165° N, 78.0322° E · Rajpur Road, Dehradun
        </p>
      </Card>

      <input
        type="file"
        accept="image/*"
        capture="user"
        ref={fileInputRef}
        onChange={handleFileChange}
        className="hidden"
        disabled={!!attendance.checkOutTime}
      />

      <div className="mb-6 grid grid-cols-2 gap-4">
        {/* Check-In Selfie Box */}
        <div
          className={cn(
            "flex flex-col items-center p-3 border rounded-xl bg-slate-50",
            !attendance.checkedIn && !attendance.checkOutTime ? "cursor-pointer border-aqua-200 hover:bg-aqua-50/30" : "border-slate-200 opacity-80"
          )}
          onClick={() => {
            if (!attendance.checkedIn && !attendance.checkOutTime) {
              fileInputRef.current?.click();
            }
          }}
        >
          <div className="flex h-16 w-16 items-center justify-center rounded-xl border-2 border-dashed border-slate-300 bg-white overflow-hidden relative mb-2">
            {attendance.checkInSelfieUrl ? (
              <img src={attendance.checkInSelfieUrl} alt="Check-in Selfie" className="w-full h-full object-cover" />
            ) : (!attendance.checkedIn && !attendance.checkOutTime && selfiePreview) ? (
              <img src={selfiePreview} alt="Selfie preview" className="w-full h-full object-cover" />
            ) : (
              <Camera className="h-5 w-5 text-slate-400" strokeWidth={1.75} />
            )}
          </div>
          <span className="text-xs font-bold text-slate-600">Check-in Selfie</span>
          <span className="text-[10px] text-slate-400 text-center mt-0.5">
            {attendance.checkInSelfieUrl ? "✓ Uploaded" : (!attendance.checkedIn && !attendance.checkOutTime && selfieFile) ? "✓ Captured" : "Required"}
          </span>
        </div>

        {/* Check-Out Selfie Box */}
        <div
          className={cn(
            "flex flex-col items-center p-3 border rounded-xl bg-slate-50",
            attendance.checkedIn && !attendance.checkOutTime ? "cursor-pointer border-aqua-200 hover:bg-aqua-50/30" : "border-slate-200 opacity-80"
          )}
          onClick={() => {
            if (attendance.checkedIn && !attendance.checkOutTime) {
              fileInputRef.current?.click();
            }
          }}
        >
          <div className="flex h-16 w-16 items-center justify-center rounded-xl border-2 border-dashed border-slate-300 bg-white overflow-hidden relative mb-2">
            {attendance.checkOutSelfieUrl ? (
              <img src={attendance.checkOutSelfieUrl} alt="Check-out Selfie" className="w-full h-full object-cover" />
            ) : (attendance.checkedIn && !attendance.checkOutTime && selfiePreview) ? (
              <img src={selfiePreview} alt="Selfie preview" className="w-full h-full object-cover" />
            ) : (
              <Camera className="h-5 w-5 text-slate-400" strokeWidth={1.75} />
            )}
          </div>
          <span className="text-xs font-bold text-slate-600">Check-out Selfie</span>
          <span className="text-[10px] text-slate-400 text-center mt-0.5">
            {attendance.checkOutSelfieUrl ? "✓ Uploaded" : (attendance.checkedIn && !attendance.checkOutTime && selfieFile) ? "✓ Captured" : "Required"}
          </span>
        </div>
      </div>

      <Button
        block
        variant={actionVariant}
        disabled={actionDisabled || compressing || submitting || loading}
        onClick={handleToggleAttendance}
      >
        {submitting || loading ? (
          <span className="flex items-center justify-center gap-2">
            <svg className="animate-spin h-4 w-4 text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
              <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {loading ? "Checking Status..." : "Processing..."}
          </span>
        ) : (
          actionLabel
        )}
      </Button>

      <p className="mb-2.5 mt-7 text-xs font-bold text-slate-600">This week</p>
      <div className="grid grid-cols-7 gap-1.5">
        {WEEK_LABELS.map((label, i) => {
          const isToday = i === todayIndex;
          const isWeekend = i === 6; // Sunday
          const isPast = i < todayIndex;
          return (
            <div key={i} className="text-center">
              <p className="mb-1 text-[10px] font-semibold text-slate-400">{label}</p>
              <div
                className={cn(
                  "mx-auto h-6 w-6 rounded-full",
                  isToday && "bg-aqua-600 ring-2 ring-aqua-200",
                  !isToday && isPast && !isWeekend && "bg-emerald-500",
                  !isToday && isPast && isWeekend && "bg-amber-400",
                  !isToday && !isPast && "bg-slate-200"
                )}
              />
            </div>
          );
        })}
      </div>
    </div>
  );
}
