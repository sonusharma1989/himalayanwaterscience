"use client";

import { useRouter } from "next/navigation";
import { LogOut } from "lucide-react";
import { Card } from "@/components/ui/Card";
import { Button } from "@/components/ui/Button";
import { useApp } from "@/lib/store";

export default function ProfilePage() {
  const router = useRouter();
  const { user, logout } = useApp();

  const initials = user?.name
    ? user.name
        .split(" ")
        .map((n) => n[0])
        .join("")
        .toUpperCase()
        .slice(0, 2)
    : "EM";

  function handleLogout() {
    logout();
    router.push("/");
  }

  return (
    <div className="flex-1 px-5 pb-6 pt-6">
      <div className="mb-6 flex flex-col items-center text-center">
        <div className="mb-3 flex h-20 w-20 items-center justify-center rounded-full bg-aqua-600 font-display text-2xl font-bold text-white">
          {initials}
        </div>
        <p className="font-display text-base font-bold text-slate-800">{user?.name || "Employee"}</p>
        <p className="mt-0.5 text-xs font-semibold text-slate-400">
          {user?.role || "Field Service Engineer"} · Dehradun
        </p>
      </div>

      <div className="mb-6 grid grid-cols-3 gap-3">
        <Card className="p-3 text-center">
          <p className="font-display text-lg font-bold text-slate-800">{user?.stats?.jobs ?? 0}</p>
          <p className="mt-0.5 text-[10px] font-semibold uppercase text-slate-400">Jobs</p>
        </Card>
        <Card className="p-3 text-center">
          <p className="font-display text-lg font-bold text-amber-500">{user?.stats?.rating ?? "5.0"}</p>
          <p className="mt-0.5 text-[10px] font-semibold uppercase text-slate-400">Rating</p>
        </Card>
        <Card className="p-3 text-center">
          <p className="font-display text-lg font-bold text-emerald-600">{user?.stats?.attendance ?? 0}%</p>
          <p className="mt-0.5 text-[10px] font-semibold uppercase text-slate-400">Attendance</p>
        </Card>
      </div>

      <Button variant="danger" block onClick={handleLogout}>
        <LogOut className="h-4 w-4" strokeWidth={1.75} />
        Logout
      </Button>
    </div>
  );
}
