"use client";

import { useMemo } from "react";
import Link from "next/link";
import { useRouter } from "next/navigation";
import { Bell, Clock } from "lucide-react";
import { useApp } from "@/lib/store";
import { Card } from "@/components/ui/Card";
import { Button } from "@/components/ui/Button";
import { TaskCard } from "@/components/TaskCard";

function useGreeting() {
  return useMemo(() => {
    const h = new Date().getHours();
    if (h < 12) return "Good morning";
    if (h < 17) return "Good afternoon";
    return "Good evening";
  }, []);
}

export default function HomePage() {
  const { tasks, attendance } = useApp();
  const router = useRouter();
  const greeting = useGreeting();
  const today = useMemo(
    () =>
      new Date().toLocaleDateString("en-IN", {
        weekday: "long",
        day: "numeric",
        month: "long",
      }),
    []
  );

  const stats = useMemo(
    () => ({
      today: tasks.length,
      pending: tasks.filter((t) => t.step === 0).length,
      done: tasks.filter((t) => t.step >= 4).length,
    }),
    [tasks]
  );

  const attendanceLabel = attendance.checkedIn
    ? `Checked in at ${attendance.checkInTime}`
    : attendance.checkOutTime
      ? `Checked out at ${attendance.checkOutTime}`
      : "Not checked in";
  const attendanceValue = attendance.checkedIn
    ? "Have a productive day!"
    : attendance.checkOutTime
      ? "See you tomorrow"
      : "Mark your attendance";
  const attendanceBtnLabel = attendance.checkedIn
    ? "Check Out"
    : attendance.checkOutTime
      ? "Done"
      : "Check In";

  return (
    <div className="flex-1 px-5 pb-6 pt-5">
      <div className="mb-5 flex items-center justify-between">
        <div>
          <p className="font-display text-lg font-bold text-slate-800">{greeting}, Ramesh</p>
          <p className="mt-0.5 text-xs font-medium text-slate-400">{today}</p>
        </div>
        <button className="relative flex h-9 w-9 items-center justify-center rounded-full border border-slate-200 bg-white">
          <Bell className="h-5 w-5 text-slate-500" strokeWidth={1.75} />
          <span className="absolute -right-0.5 -top-0.5 h-2.5 w-2.5 rounded-full border-2 border-white bg-rose-500" />
        </button>
      </div>

      <Card className="mb-4 flex items-center justify-between p-4">
        <div className="flex items-center gap-3">
          <div className="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-emerald-100">
            <Clock className="h-5 w-5 text-emerald-600" strokeWidth={1.75} />
          </div>
          <div>
            <p className="text-xs font-semibold text-slate-400">{attendanceLabel}</p>
            <p className="text-sm font-bold text-slate-700">{attendanceValue}</p>
          </div>
        </div>
        <Button size="sm" onClick={() => router.push("/attendance")}>
          {attendanceBtnLabel}
        </Button>
      </Card>

      <div className="mb-5 grid grid-cols-3 gap-3">
        <Card className="p-3 text-center">
          <p className="font-display text-xl font-bold text-slate-800">{stats.today}</p>
          <p className="mt-0.5 text-[10px] font-semibold uppercase text-slate-400">Today</p>
        </Card>
        <Card className="p-3 text-center">
          <p className="font-display text-xl font-bold text-amber-600">{stats.pending}</p>
          <p className="mt-0.5 text-[10px] font-semibold uppercase text-slate-400">Pending</p>
        </Card>
        <Card className="p-3 text-center">
          <p className="font-display text-xl font-bold text-emerald-600">{stats.done}</p>
          <p className="mt-0.5 text-[10px] font-semibold uppercase text-slate-400">Done</p>
        </Card>
      </div>

      <div className="mb-2.5 flex items-center justify-between">
        <p className="text-sm font-bold text-slate-700">Today&apos;s tasks</p>
        <Link href="/tasks" className="text-xs font-semibold text-aqua-600">
          View all
        </Link>
      </div>
      <Card className="divide-y divide-slate-100 overflow-hidden">
        {tasks.slice(0, 3).map((t) => (
          <TaskCard key={t.id} task={t} />
        ))}
      </Card>
    </div>
  );
}
