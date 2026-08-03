"use client";

import { useEffect, useMemo, useState } from "react";
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

export default function AttendancePage() {
  const { attendance, toggleAttendance } = useApp();
  const clockTime = useClock();
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

      <div className="mb-6 flex items-center gap-3">
        <div className="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl border-2 border-dashed border-slate-300 bg-slate-100">
          <Camera className="h-5 w-5 text-slate-400" strokeWidth={1.75} />
        </div>
        <div>
          <p className="text-xs font-bold text-slate-600">Selfie verification</p>
          <p className="text-[11px] text-slate-400">Optional — captured on check-in</p>
        </div>
      </div>

      <Button
        block
        variant={actionVariant}
        disabled={actionDisabled}
        onClick={toggleAttendance}
      >
        {actionLabel}
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
