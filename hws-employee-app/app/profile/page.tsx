"use client";

import { useRouter } from "next/navigation";
import { Calendar, Briefcase, Wallet, SlidersHorizontal, ChevronRight, LogOut } from "lucide-react";
import { Card } from "@/components/ui/Card";
import { Button } from "@/components/ui/Button";

const MENU_ITEMS = [
  { label: "Attendance history", icon: Calendar },
  { label: "Leave requests", icon: Briefcase },
  { label: "Expense claims", icon: Wallet },
  { label: "Settings", icon: SlidersHorizontal },
];

export default function ProfilePage() {
  const router = useRouter();

  return (
    <div className="flex-1 px-5 pb-6 pt-6">
      <div className="mb-6 flex flex-col items-center text-center">
        <div className="mb-3 flex h-20 w-20 items-center justify-center rounded-full bg-aqua-600 font-display text-2xl font-bold text-white">
          RK
        </div>
        <p className="font-display text-base font-bold text-slate-800">Ramesh Kumar</p>
        <p className="mt-0.5 text-xs font-semibold text-slate-400">
          Field Service Engineer · Dehradun
        </p>
      </div>

      <div className="mb-6 grid grid-cols-3 gap-3">
        <Card className="p-3 text-center">
          <p className="font-display text-lg font-bold text-slate-800">142</p>
          <p className="mt-0.5 text-[10px] font-semibold uppercase text-slate-400">Jobs</p>
        </Card>
        <Card className="p-3 text-center">
          <p className="font-display text-lg font-bold text-amber-500">4.8</p>
          <p className="mt-0.5 text-[10px] font-semibold uppercase text-slate-400">Rating</p>
        </Card>
        <Card className="p-3 text-center">
          <p className="font-display text-lg font-bold text-emerald-600">96%</p>
          <p className="mt-0.5 text-[10px] font-semibold uppercase text-slate-400">Attendance</p>
        </Card>
      </div>

      <Card className="mb-6 divide-y divide-slate-100 overflow-hidden">
        {MENU_ITEMS.map(({ label, icon: Icon }) => (
          <button
            key={label}
            className="flex w-full items-center gap-3 px-4 py-3.5 hover:bg-slate-50"
          >
            <Icon className="h-5 w-5 text-slate-400" strokeWidth={1.75} />
            <span className="flex-1 text-left text-sm font-semibold text-slate-700">{label}</span>
            <ChevronRight className="h-4 w-4 text-slate-300" strokeWidth={1.75} />
          </button>
        ))}
      </Card>

      <Button variant="danger" block onClick={() => router.push("/")}>
        <LogOut className="h-4 w-4" strokeWidth={1.75} />
        Logout
      </Button>
    </div>
  );
}
