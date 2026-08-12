"use client";

import Link from "next/link";
import { usePathname } from "next/navigation";
import { Home, ListChecks, Clock, User, ClipboardList } from "lucide-react";
import { cn } from "@/lib/utils";

const NAV_ITEMS = [
  { href: "/home", label: "Home", icon: Home },
  { href: "/tasks", label: "Tasks", icon: ListChecks },
  { href: "/survey", label: "Survey", icon: ClipboardList },
  { href: "/attendance", label: "Attendance", icon: Clock },
  { href: "/profile", label: "Profile", icon: User },
];

const VISIBLE_ON = ["/home", "/tasks", "/survey", "/attendance", "/profile"];

export function BottomNav() {
  const pathname = usePathname();

  if (!VISIBLE_ON.includes(pathname)) return null;

  return (
    <nav className="sticky bottom-0 z-20 shrink-0 border-t border-slate-100 bg-white/95 px-2 pb-[calc(0.75rem+env(safe-area-inset-bottom))] pt-2 backdrop-blur">
      <div className="mx-auto grid max-w-md grid-cols-5">
        {NAV_ITEMS.map(({ href, label, icon: Icon }) => {
          const active = pathname === href;
          return (
            <Link
              key={href}
              href={href}
              className={cn(
                "flex flex-col items-center gap-1 py-1 text-slate-400 transition-colors",
                active && "text-aqua-600"
              )}
            >
              <Icon className="h-5 w-5" strokeWidth={1.75} />
              <span className="text-[10px] font-semibold">{label}</span>
            </Link>
          );
        })}
      </div>
    </nav>
  );
}
